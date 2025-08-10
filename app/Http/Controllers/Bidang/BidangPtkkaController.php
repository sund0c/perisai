<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\PtkkaStatusLog;
use Illuminate\Support\Str;
use App\Models\Aset;
use App\Models\Periode;
use App\Models\PtkkaSession;
use Illuminate\Http\Request;
use App\Models\FungsiStandar;
use App\Models\StandarIndikator;
use App\Models\PtkkaJawaban;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class BidangPtkkaController extends Controller
{

    public function indexPtkkaBidang(Request $request)
    {
        // Header info (opsional)
        $periodeAktif = Periode::where('status', 'open')->first();
        $tahunAktifGlobal = $periodeAktif->tahun ?? '-';
        $kunci = $periodeAktif ? 'unlocked' : 'locked';

        // Helper hitung persentase & kategori
        $computeKategori = function ($session) {
            if (!$session) return;
            $jawabans = $session->jawabans ?? collect();
            if ($jawabans->isEmpty()) return;

            $total = (int) $jawabans->sum('jawaban');   // 0/1/2
            $maks  = (int) ($jawabans->count() * 2);    // max = 2 per indikator
            $persen = $maks > 0 ? (int) floor(($total / $maks) * 100) : 0;

            if ($persen >= 66.7) $kat = 'TINGGI';
            elseif ($persen >= 33.4) $kat = 'SEDANG';
            else                    $kat = 'RENDAH';

            $session->persentase = $persen;
            $session->kategori_kepatuhan = $kat;
            $session->ptkka_sessions_id = $session->id; // agar cocok dengan blade

        };

        // -----------------------
        // KIRI: Pengajuan (status = 1)
        // -----------------------
        $asetsPengajuan = Aset::with([
            'opd:id,namaopd',
            'ptkkaSessions' => function ($q) {
                $q->with(['latestStatusLog', 'jawabans'])
                    ->whereHas('latestStatusLog', fn($l) => $l->where('status', 1))
                    ->latest('updated_at'); // ambil terbaru di depan
            },
        ])
            ->whereHas('ptkkaSessions.latestStatusLog', fn($l) => $l->where('status', 1))
            ->orderBy('opd_id')->orderBy('nama_aset')
            ->get()
            ->map(function ($aset) use ($computeKategori) {
                // sesi pertama di collection ini adalah yang terbaru status=1
                $aset->ptkkaPengajuan = $aset->ptkkaSessions->first();
                $computeKategori($aset->ptkkaPengajuan);
                return $aset;
            });

        // -----------------------
        // TENGAH: Proses (status ∈ {0,2,3})
        // -----------------------
        $statusProses = [0, 2, 3];

        $asetsProses = Aset::with([
            'opd:id,namaopd',
            'ptkkaSessions' => function ($q) use ($statusProses) {
                $q->with(['latestStatusLog', 'jawabans'])
                    ->whereHas('latestStatusLog', fn($l) => $l->whereIn('status', $statusProses))
                    ->latest('updated_at');
            },
        ])
            ->whereHas('ptkkaSessions.latestStatusLog', fn($l) => $l->whereIn('status', $statusProses))
            ->orderBy('opd_id')->orderBy('nama_aset')
            ->get()
            ->map(function ($aset) use ($computeKategori) {
                $aset->ptkkaTerakhir = $aset->ptkkaSessions->first(); // terbaru dari 0/2/3
                $computeKategori($aset->ptkkaTerakhir);
                return $aset;
            });

        // -----------------------
        // KANAN: Rampung (status = 4) — tampilkan SEMUA aset
        // -----------------------
        $asetsRampung = Aset::with([
            'opd:id,namaopd',
            'ptkkaSessions' => function ($q) {
                $q->with(['latestStatusLog', 'jawabans'])
                    ->whereHas('latestStatusLog', fn($l) => $l->where('status', 4))
                    ->latest('updated_at');
            },
        ])
            ->orderBy('opd_id')->orderBy('nama_aset')
            ->get()
            ->map(function ($aset) use ($computeKategori) {
                // bisa null jika aset belum pernah status=4
                $aset->ptkkaTerakhirRampung = $aset->ptkkaSessions->first();
                $computeKategori($aset->ptkkaTerakhirRampung);

                $aset->kategori_id_terakhir = optional($aset->ptkkaTerakhirRampung)->standar_kategori_id;
                $aset->kategori_label_terakhir = $aset->kategori_id_terakhir === 3 ? 'MOBILE' : ($aset->kategori_id_terakhir === 2 ? 'WEB' : '-');

                return $aset;
            });

        $badgeByKat = [
            'TINGGI' => 'success',
            'SEDANG' => 'warning',
            'RENDAH' => 'danger',
        ];

        return view('bidang.ptkka.index', compact(
            'tahunAktifGlobal',
            'kunci',
            'asetsPengajuan',
            'asetsProses',
            'asetsRampung',
            'badgeByKat'
        ));
    }


    public function ajukanVerifikasi(PtkkaSession $session)
    {
        //     dd($session);
        // Untuk role:bidang, OPD owner check tidak relevan. Hapus check user_id.
        if ((int)$session->status !== 1) {
            return back()->with('error', 'Hanya sesi berstatus PENGAJUAN (1) yang bisa dinaikkan ke VERIFIKASI (2).');
        }

        try {
            DB::transaction(function () use ($session) {
                // Log perubahan status: 1 -> 2
                PtkkaStatusLog::create([
                    'ptkka_session_id' => $session->id,
                    'from_status'      => 1,
                    'to_status'        => 2,
                    'user_id'          => auth()->id(),
                    'catatan'          => 'Naik ke Verifikasi',
                    'changed_at'       => now(),
                ]);

                // Pastikan tersimpan meskipun 'status' tidak ada di $fillable
                $session->forceFill(['status' => 2]);
                $session->save();
            });

            return back()->with('success', "Berhasil dinaikkan ke VERIFIKASI");
        } catch (\Throwable $e) {
            // Debug cepat kalau ada masalah lain (constraint, dll.)
            report($e);
            return back()->with('error', 'Gagal mengajukan verifikasi: ' . $e->getMessage());
        }
    }


    public function ajukanKlarifikasi(PtkkaSession $session)
    {
        //     dd($session);
        // Untuk role:bidang, OPD owner check tidak relevan. Hapus check user_id.
        if ((int)$session->status !== 2) {
            return back()->with('error', 'Hanya sesi berstatus VERIFIKASI yang bisa dinaikkan ke KLARIFIKASI.');
        }

        try {
            DB::transaction(function () use ($session) {
                // Log perubahan status: 1 -> 2
                PtkkaStatusLog::create([
                    'ptkka_session_id' => $session->id,
                    'from_status'      => 2,
                    'to_status'        => 3,
                    'user_id'          => auth()->id(),
                    'catatan'          => 'Naik ke Klarifikasi',
                    'changed_at'       => now(),
                ]);

                // Pastikan tersimpan meskipun 'status' tidak ada di $fillable
                $session->forceFill(['status' => 3]);
                $session->save();
            });

            return back()->with('success', "Berhasil dinaikkan ke KLARIFIKASI.");
        } catch (\Throwable $e) {
            // Debug cepat kalau ada masalah lain (constraint, dll.)
            report($e);
            return back()->with('error', 'Gagal mengajukan klarifikasi: ' . $e->getMessage());
        }
    }

    public function ajukanClosing(PtkkaSession $session)
    {
        //dd($session);
        // Untuk role:bidang, OPD owner check tidak relevan. Hapus check user_id.
        if ((int)$session->status !== 3) {
            return back()->with('error', 'Hanya sesi berstatus KLARIFIKASI yang bisa di CLOSING.');
        }

        DB::transaction(function () use ($session) {
            // Log perubahan status: 1 -> 2
            PtkkaStatusLog::create([
                'ptkka_session_id' => $session->id,
                'from_status'      => 3,
                'to_status'        => 4,
                'user_id'          => auth()->id(),
                'catatan'          => 'CLOSING',
                'changed_at'       => now(),
            ]);

            // Pastikan tersimpan meskipun 'status' tidak ada di $fillable
            $session->forceFill(['status' => 4]);
            $session->save();
        });

        // ⬇️ Kembali ke index Bidang PTKKA
        return redirect()
            ->route('bidang.ptkka.index')
            ->with('success', 'PPTK Berhasil Rampung');
    }

    public function pengajuanPDF()
    {
        $asetsPengajuan = Aset::with([
            'opd:id,namaopd',
            'ptkkaPengajuan' => function ($q) {
                $q->with(['jawabans:id,ptkka_session_id,jawaban']);
                // Hindari kolom tanpa prefix:
                // $q->select('ptkka_sessions.*'); // opsional
            },
        ])->whereHas('ptkkaSessions', fn($q) => $q->where('status', 1))
            ->orderBy('opd_id')->orderBy('nama_aset')
            ->get();

        // hitung kategori & persentase
        foreach ($asetsPengajuan as $aset) {
            $s = $aset->ptkkaPengajuan;
            if ($s && $s->jawabans->isNotEmpty()) {
                $total = (int) $s->jawabans->sum('jawaban');
                $maks  = (int) ($s->jawabans->count() * 2);
                $persen = $maks > 0 ? (int) floor(($total / $maks) * 100) : 0;


                if ($persen >= 66.7) $kat = 'TINGGI';
                elseif ($persen >= 33.4) $kat = 'SEDANG';
                else                    $kat = 'RENDAH';

                $s->persentase = $persen;
                $s->kategori_kepatuhan = $kat;
            }
        }

        $pdf = PDF::loadView('bidang.ptkka.export_pengajuan_pdf', [
            'asetsPengajuan' => $asetsPengajuan
        ])
            ->setPaper('A4', 'landscape');

        // Render dulu agar page count tersedia
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Footer terpusat di bawah
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = 575;
            $canvas->text($x, $y, $text, $font, $size);
        });
        return $pdf->download('ptkka_pengajuan_' . date('Ymd_His') . '.pdf');
    }

    public function progressPDF()
    {
        // status on progress: 0, 2, 3
        $statusProses = [0, 2, 3];

        $asetsProses = \App\Models\Aset::with([
            'opd:id,namaopd',
            // ambil sesi TERAKHIR yang berstatus 0/2/3 + jawabans
            'ptkkaSessions' => function ($q) use ($statusProses) {
                $q->with(['latestStatusLog', 'jawabans:id,ptkka_session_id,jawaban'])
                    ->whereHas('latestStatusLog', fn($l) => $l->whereIn('status', $statusProses))
                    ->latest('updated_at');
            },
        ])
            ->whereHas('ptkkaSessions.latestStatusLog', fn($l) => $l->whereIn('status', $statusProses))
            ->orderBy('opd_id')->orderBy('nama_aset')
            ->get()
            ->map(function ($aset) {
                // sesi terbaru dari kumpulan 0/2/3
                $aset->ptkkaTerakhir = $aset->ptkkaSessions->first();
                $s = $aset->ptkkaTerakhir;

                if ($s && $s->jawabans->isNotEmpty()) {
                    $total  = (int) $s->jawabans->sum('jawaban');   // 0/1/2
                    $maks   = (int) ($s->jawabans->count() * 2);
                    $persen = $maks > 0 ? (int) floor(($total / $maks) * 100) : 0;


                    if ($persen >= 66.7) $kat = 'TINGGI';
                    elseif ($persen >= 33.4) $kat = 'SEDANG';
                    else                    $kat = 'RENDAH';

                    $s->persentase = $persen;
                    $s->kategori_kepatuhan = $kat;
                }

                return $aset;
            });

        $pdf = PDF::loadView('bidang.ptkka.export_progress_pdf', [
            'asetsProses' => $asetsProses
        ])
            ->setPaper('A4', 'landscape');

        // render dulu supaya page count tersedia
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // footer terpusat (dinamis mengikuti orientasi)
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = $height - 20; // 20pt dari bawah (aman untuk portrait/landscape)
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('ptkka_progress_' . date('Ymd_His') . '.pdf');
    }

    public function closingPDF(Request $request)
    {
        // Ambil semua aset (lintas OPD) untuk ringkasan kanan
        $asets = Aset::withoutGlobalScopes()
            ->with(['opd:id,namaopd'])
            ->orderBy('opd_id')
            ->orderBy('nama_aset')
            ->get();

        // Kumpulan baris untuk PDF
        $rows = [];

        foreach ($asets as $aset) {
            // Sesi terakhir dengan status = 4 (Rampung)
            $lastClosed = $aset->ptkkaSessions()
                ->where('status', 4)
                ->latest('updated_at')
                ->first();

            if ($lastClosed) {
                // Hitung skor dari tabel ptkka_jawabans untuk session ini
                $jawabans = PtkkaJawaban::where('ptkka_session_id', $lastClosed->id)->get();
                $jumlah   = $jawabans->count();
                $skorMax  = $jumlah * 2;
                $skor     = (int) $jawabans->sum('jawaban');
                $persen   = $skorMax > 0 ? round(($skor / $skorMax) * 100) : 0;

                if ($persen >= 66.7) $kat = 'TINGGI';
                elseif ($persen >= 33.4) $kat = 'SEDANG';
                else                    $kat = 'RENDAH';




                $rows[] = [
                    'kode_aset'     => $aset->kode_aset,
                    'nama_aset'     => $aset->nama_aset,
                    'opd'           => $aset->opd->namaopd ?? '-',
                    'kategori'      => $lastClosed->standar_kategori_id == 3 ? 'MOBILE' : 'WEB', // 2=WEB, 3=MOBILE; default ke WEB
                    'uid'           => $lastClosed->uid ?? '-',
                    'tanggal'       => $lastClosed->updated_at
                        ? Carbon::parse($lastClosed->updated_at)->translatedFormat('d F Y')
                        : '-',
                    'skor_text'     => $kat . ' ( ' . $persen . '% )',
                    // 'skor_text'     => $skorMax > 0 ? "{$skor} / {$skorMax} ({$persen}%)" : '0 / 0 (0%)',
                    'has_closing'   => true,
                ];
            } else {
                // Belum ada sesi rampung
                $rows[] = [
                    'kode_aset'     => $aset->kode_aset,
                    'nama_aset'     => $aset->nama_aset,
                    'opd'           => $aset->opd->namaopd ?? '-',
                    'kategori'      => '-', // tidak ada sesi rampung
                    'uid'           => '-',
                    'tanggal'       => '-',
                    'skor_text'     => 'BELUM PERNAH',
                    'has_closing'   => false,
                ];
            }
        }

        $generatedAt = Carbon::now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('bidang.ptkka..export_closing_pdf', compact('rows', 'generatedAt'))
            ->setPaper('A4', 'landscape');

        // render dulu supaya page count tersedia
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // footer terpusat (dinamis mengikuti orientasi)
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = $height - 20; // 20pt dari bawah (aman untuk portrait/landscape)
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('ptkkapemprovbali_' . now()->format('Ymd-His') . '.pdf');
    }

    public function showDetail($id)
    {
        // muat juga aset + opd agar bisa dipakai di view
        $session = PtkkaSession::with(['kategori', 'aset.opd'])->findOrFail($id);

        $fungsiStandars = FungsiStandar::with([
            'indikators.rekomendasis.jawabans' // sudah oke
        ])
            ->where('kategori_id', $session->standar_kategori_id)
            ->orderBy('urutan')
            ->get();

        // kalau jawaban per indikator dipakai di view
        $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)
            ->get()
            ->keyBy('standar_indikator_id');

        // label kategori
        $kategoriLabel = [
            2 => 'WEB',
            3 => 'MOBILE',
        ];
        $kategoriText = $kategoriLabel[$session->standar_kategori_id] ?? '-';

        return view('bidang.ptkka.detail', compact(
            'session',
            'fungsiStandars',
            'jawabans',
            'kategoriText'
        ));
    }


    public function simpanCatatan(Request $request, $sessionId, $fungsiId)
    {
        $session = \App\Models\PtkkaSession::findOrFail($sessionId);
        $catatanAdminArr = $request->input('catatanadmin', []);

        if (empty($catatanAdminArr)) {
            return back()->with('warning', 'Tidak ada catatan untuk disimpan.');
        }

        $affected = 0;

        foreach ($catatanAdminArr as $rekomendasiId => $note) {
            \App\Models\PtkkaJawaban::updateOrCreate(
                [
                    'ptkka_session_id'        => $session->id,
                    'rekomendasi_standard_id' => $rekomendasiId,
                ],
                ['catatanadmin' => $note]
            );
            $affected++;
        }

        return back()->with('success', "Catatan berhasil disimpan");
    }



    public function riwayat(Aset $aset)
    {
        // batasi hanya ke OPD yang sedang login
        if ($aset->opd_id !== auth()->user()->opd_id) {
            abort(403);
        }

        $riwayat = $aset->ptkkaSessions()->withCount(['jawabans'])->latest()->get();
        // Data tambahan untuk setiap sesi
        foreach ($riwayat as $session) {
            // Ambil semua fungsi standar terkait kategori dari session
            $fungsiStandars = FungsiStandar::with('indikators.rekomendasis')
                ->where('kategori_id', $session->standar_kategori_id)
                ->get();

            $jumlahRekomendasi = 0;

            foreach ($fungsiStandars as $fungsi) {
                foreach ($fungsi->indikators as $indikator) {
                    $jumlahRekomendasi += $indikator->rekomendasis->count();
                }
            }

            $jumlahJawaban = $jumlahRekomendasi;
            $skorMaksimal = $jumlahJawaban * 2;
            $totalSkor = $session->jawabans->sum('jawaban');

            $persentase = $skorMaksimal > 0 ? ($totalSkor / $skorMaksimal) * 100 : 0;

            $kategoriKepatuhan = 'TIDAK TERDEFINISI';
            if ($persentase >= 66.7) {
                $kategoriKepatuhan = 'TINGGI';
            } elseif ($persentase >= 33.4) {
                $kategoriKepatuhan = 'SEDANG';
            } else {
                $kategoriKepatuhan = 'RENDAH';
            }

            // Tambahkan properti ke objek session (tidak perlu simpan ke DB)
            $session->jumlah_jawaban = $jumlahJawaban;
            $session->skor_maksimal = $skorMaksimal;
            $session->total_skor = $totalSkor;
            $session->persentase = round($persentase, 2);
            $session->kategori_kepatuhan = $kategoriKepatuhan;
        }




        return view('bidang.ptkka.riwayat', compact('aset', 'riwayat'));
    }


    public function store(Aset $aset, Request $request)
    {
        if ($aset->opd_id !== auth()->user()->opd_id) {
            abort(403);
        }

        $masihAktif = $aset->ptkkaSessions()->whereIn('status', [0, 1, 2, 3])->exists();


        if ($masihAktif) {
            return back()->with('error', 'Anda masih punya PTKKA yang masih berlangsung untuk aset ini.');
        }

        $kategoriId = $request->standar_kategori_id;

        if (!in_array($kategoriId, [2, 3])) {
            return back()->with('error', 'Kategori standar tidak valid.');
        }

        $session = PtkkaSession::create([
            'user_id' => auth()->id(),
            'aset_id' => $aset->id,
            'standar_kategori_id' => $kategoriId,
            'status' => 0,
            'uid' => Str::uuid(),
        ]);

        PtkkaStatusLog::create([
            'ptkka_session_id' => $session->id,
            'from_status' => null,
            'to_status' => 0,
            'user_id' => auth()->id(),
            'catatan' => 'Membuat sesi PTKKA',
            'changed_at' => now(),
        ]);

        return redirect()->route('ptkka.riwayat', $aset->id)->with('success', 'Form PTKKA berhasil dibuat.');
    }


    public function destroy(PtkkaSession $session)
    {
        // Pastikan hanya OPD pemilik yang bisa hapus
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // Hanya bisa hapus saat status masih PENGISIAN (0)
        if ($session->status !== 0) {
            return back()->with('error', 'Hanya PTKKA yang berstatus Pengisian yang dapat dihapus.');
        }

        $session->delete(); // akan menghapus juga status_logs karena onDelete('cascade')

        return back()->with('success', 'Pengajuan PTKKA berhasil dihapus.');
    }






    public function simpan(Request $request, $id)
    {
        $session = PtkkaSession::findOrFail($id);

        $jawabans = $request->input('jawaban', []);
        $penjelasanOpd = $request->input('penjelasanopd', []);
        $linkBukti = $request->input('linkbuktidukung', []);

        foreach ($jawabans as $rekomendasiId => $jawabanNilai) {
            $penjelasan = $penjelasanOpd[$rekomendasiId] ?? null;
            $link = $linkBukti[$rekomendasiId] ?? null;

            // Validasi: jika penjelasan atau link kosong
            if (empty($penjelasan) || empty($link)) {
                return back()->with('error', 'Semua jawaban harus disertai penjelasan dan link bukti dukung.');
            }

            \App\Models\PtkkaJawaban::updateOrCreate(
                [
                    'ptkka_session_id' => $session->id,
                    'rekomendasi_standard_id' => $rekomendasiId,
                ],
                [
                    'jawaban' => $jawabanNilai,
                    'penjelasanopd' => $penjelasan,
                    'linkbuktidukung' => $link,
                ]
            );
        }

        return redirect()->back()->with('success', 'Jawaban berhasil disimpan.');
    }



    public function ajukanVerifikasid(Request $request, $sessionId)
    {
        $session = PtkkaSession::findOrFail($sessionId);

        // Ambil semua rekomendasi dari fungsi standar session ini
        $fungsiStandars = FungsiStandar::with('indikators.rekomendasis')->where('kategori_id', $session->standar_kategori_id)->get();

        $incomplete = [];

        foreach ($fungsiStandars as $fungsi) {
            foreach ($fungsi->indikators as $indikator) {
                foreach ($indikator->rekomendasis as $rek) {
                    $jawaban = PtkkaJawaban::where('ptkka_session_id', $session->id)
                        ->where('rekomendasi_standard_id', $rek->id)
                        ->first();

                    if (!$jawaban || empty($jawaban->penjelasanopd) || empty($jawaban->linkbuktidukung)) {
                        $incomplete[] = $rek->id;
                    }
                }
            }
        }

        if (count($incomplete) > 0) {
            return back()->with('error', 'Masih ada isian yang belum lengkap. Silakan lengkapi semua jawaban sebelum mengajukan verifikasi.');
        }

        // Ubah status session
        $oldStatus = $session->status;
        $session->status = 1; // 1 = Pengajuan
        $session->save();

        // Tambahkan ke status log
        \App\Models\PtkkaStatusLog::create([
            'ptkka_session_id' => $session->id,
            'from_status' => $oldStatus,
            'to_status' => 1,
            'user_id' => auth()->id(),
            'catatan' => 'Mengajukan verifikasi oleh OPD',
            'changed_at' => now(),
        ]);

        return redirect()->route('ptkka.riwayat', $session->aset_id)
            ->with('success', 'Pengajuan verifikasi berhasil dikirim. Hubungi Dinas Kominfos Prov Bali untuk jadwal Verifikasi.');
    }


    public function simpanJawaban(Request $request)
    {
        $data = $request->validate([
            'ptkka_session_id' => 'required|exists:ptkka_sessions,id',
            'standar_indikator_id' => 'required|exists:standar_indikator,id',
            'jawaban' => 'required|in:0,1,2',
            'rekomendasi' => 'nullable|string',
        ]);

        PtkkaJawaban::updateOrCreate(
            [
                'ptkka_session_id' => $data['ptkka_session_id'],
                'standar_indikator_id' => $data['standar_indikator_id'],
            ],
            [
                'jawaban' => $data['jawaban'],
                'rekomendasi' => $data['rekomendasi'],
            ]
        );

        return back()->with('success', 'Jawaban berhasil disimpan.');
    }

    public function exportPDF($id)
    {
        // $session = PtkkaSession::with('kategori', 'aset')->findOrFail($id);
        $session = PtkkaSession::with(['kategori', 'aset.opd'])->findOrFail($id);


        $fungsiStandars = FungsiStandar::with([
            'indikators.rekomendasis' // ini aman kalau rekomendasis tidak punya relasi aneh
        ])
            ->where('kategori_id', $session->standar_kategori_id)
            ->orderBy('urutan')
            ->get();



        $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)
            ->get()
            ->keyBy('rekomendasi_standard_id');

        $jumlahJawaban = $jawabans->count();
        $skorMaksimal = $jumlahJawaban * 2;
        $totalSkor = $jawabans->sum('jawaban');
        // Hitung jumlah rekomendasi yang relevan
        $jumlahJawaban = 0;
        foreach ($fungsiStandars as $fungsi) {
            foreach ($fungsi->indikators as $indikator) {
                $jumlahJawaban += $indikator->rekomendasis->count();
            }
        }

        $skorMaksimal = $jumlahJawaban * 2;
        $totalSkor = $jawabans->sum('jawaban');
        $kategoriKepatuhan = 'TIDAK TERDEFINISI';
        if ($skorMaksimal > 0) {
            $persentase = round(($totalSkor / $skorMaksimal) * 100, 2);
            if ($persentase >= 66.7) {
                $kategoriKepatuhan = 'TINGGI';
            } elseif ($persentase >= 33.4) {
                $kategoriKepatuhan = 'SEDANG';
            } else {
                $kategoriKepatuhan = 'RENDAH';
            }
        }

        // Load PDF View
        $pdf = PDF::loadView('bidang.ptkka.export_pdf', compact(
            'session',
            'fungsiStandars',
            'jawabans',
            'jumlahJawaban',
            'skorMaksimal',
            'totalSkor',
            'kategoriKepatuhan',
            'persentase'
        ))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait'); // A4 in points

        // Call render first to make sure page count is available
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Footer and page script
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = 820; // posisi bawah halaman A4
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('ptkka-' . $session->uid . '.pdf');
    }
}
