<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;

use App\Models\PtkkaStatusLog;
use Illuminate\Support\Str;
use App\Models\Aset;
use App\Models\PtkkaSession;
use Illuminate\Http\Request;
use App\Models\FungsiStandar;
use App\Models\StandarIndikator;
use App\Models\PtkkaJawaban;
use App\Models\Periode;
use Barryvdh\DomPDF\Facade\Pdf;

class PtkkaController extends Controller
{
    // public function indexPtkka()
    // {
    //     // Header info (opsional)
    //     $periodeAktif = Periode::where('status', 'open')->first();
    //     $tahunAktifGlobal = $periodeAktif->tahun ?? '-';
    //     $kunci = $periodeAktif ? 'unlocked' : 'locked';

    //     // Helper hitung persentase & kategori
    //     $computeKategori = function ($session) {
    //         if (!$session) return;
    //         $jawabans = $session->jawabans ?? collect();
    //         if ($jawabans->isEmpty()) return;

    //         $total = (int) $jawabans->sum('jawaban');   // 0/1/2
    //         $maks  = (int) ($jawabans->count() * 2);    // max = 2 per indikator
    //         $persen = $maks > 0 ? (int) floor(($total / $maks) * 100) : 0;

    //         if ($persen >= 80) $kat = 'TINGGI';
    //         elseif ($persen >= 50) $kat = 'SEDANG';
    //         else                    $kat = 'RENDAH';

    //         $session->persentase = $persen;
    //         $session->kategori_kepatuhan = $kat;
    //         $session->ptkka_sessions_id = $session->id; // agar cocok dengan blade

    //     };



    //     // -----------------------
    //     // KANAN: Rampung (status = 4) — tampilkan SEMUA aset
    //     // -----------------------
    //     $opdId = auth()->user()->opd_id;

    //     $asetsRampung = Aset::query()
    //         ->where('opd_id', $opdId) // ← hanya aset milik OPD user
    //         // opsional: hanya aset yang punya sesi rampung (status=4)
    //         ->whereHas('ptkkaSessions', function ($q) {
    //             $q->whereHas('latestStatusLog', fn($l) => $l->where('status', 4));
    //         })
    //         ->with([
    //             'opd:id,namaopd',
    //             'ptkkaSessions' => function ($q) {
    //                 $q->with(['latestStatusLog', 'jawabans'])
    //                     ->whereHas('latestStatusLog', fn($l) => $l->where('status', 4))
    //                     ->latest('updated_at');
    //             },
    //         ])
    //         ->orderBy('opd_id')->orderBy('nama_aset')
    //         ->get()
    //         ->map(function ($aset) use ($computeKategori) {
    //             // bisa null jika aset belum pernah status=4
    //             $aset->ptkkaTerakhirRampung = $aset->ptkkaSessions->first();

    //             if ($aset->ptkkaTerakhirRampung) {
    //                 $computeKategori($aset->ptkkaTerakhirRampung);
    //                 $aset->kategori_id_terakhir = $aset->ptkkaTerakhirRampung->standar_kategori_id;
    //             } else {
    //                 $aset->kategori_id_terakhir = null;
    //             }

    //             $aset->kategori_label_terakhir =
    //                 $aset->kategori_id_terakhir === 3 ? 'MOBILE' : ($aset->kategori_id_terakhir === 2 ? 'WEB' : '-');

    //             return $aset;
    //         });
    //     $badgeByKat = [
    //         'TINGGI' => 'success',
    //         'SEDANG' => 'warning',
    //         'RENDAH' => 'danger',
    //     ];

    //     return view('opd.ptkka.index', compact(
    //         'tahunAktifGlobal',
    //         'kunci',
    //         'asetsRampung',
    //         'badgeByKat'
    //     ));
    // }

    public function indexPtkka()
    {
        $opdId = auth()->user()->opd_id;

        // Ambil aset milik OPD + hanya sesi PTKKA terakhir yang STATUS=4 (Rampung)
        $asets = Aset::where('opd_id', $opdId)
            ->with([
                // relasi ini diasumsikan sudah ada:
                // ptkkaTerakhirRampung => sesi terakhir yang punya latestStatusLog status=4
                'ptkkaTerakhirRampung.jawabans:id,ptkka_session_id,jawaban',
            ])
            ->orderBy('nama_aset')
            ->get();

        // Kumpulkan kategori yang dipakai oleh sesi rampung untuk precompute skor maksimal
        $kategoriIds = $asets
            ->pluck('ptkkaTerakhirRampung.standar_kategori_id')
            ->filter()
            ->unique()
            ->values();

        // Hitung jumlah rekomendasi per kategori sekali saja (hemat query)
        $rekomCountByKategori = [];
        if ($kategoriIds->isNotEmpty()) {
            $fungsiGroup = \App\Models\FungsiStandar::with([
                'indikators' => function ($q) {
                    $q->withCount('rekomendasis');
                }
            ])
                ->whereIn('kategori_id', $kategoriIds)
                ->get()
                ->groupBy('kategori_id');

            foreach ($fungsiGroup as $kategoriId => $fungsiList) {
                $rekomCountByKategori[$kategoriId] = $fungsiList
                    ->flatMap->indikators
                    ->sum('rekomendasis_count');
            }
        }

        foreach ($asets as $aset) {
            // Samakan nama properti agar view lama yang pakai "ptkkaTerakhir" tetap jalan
            $aset->ptkkaTerakhir = $aset->ptkkaTerakhirRampung;
            $session = $aset->ptkkaTerakhir;

            if ($session) {
                $kategoriId = $session->standar_kategori_id;

                // jumlah rekomendasi untuk kategori sesi ini
                $jumlahRekomendasi = (int) ($rekomCountByKategori[$kategoriId] ?? 0);
                $skorMaksimal      = $jumlahRekomendasi * 2;
                $totalSkor         = (int) $session->jawabans->sum('jawaban');

                $persentase = $skorMaksimal > 0
                    ? round(($totalSkor / $skorMaksimal) * 100, 2)
                    : 0.0;

                if ($persentase >= 66.7) {
                    $kategoriKepatuhan = 'TINGGI';
                } elseif ($persentase >= 33.4) {
                    $kategoriKepatuhan = 'SEDANG';
                } else {
                    $kategoriKepatuhan = 'RENDAH';
                }

                // properti untuk dipakai di view
                $session->persentase = $persentase;
                $session->kategori_kepatuhan = $kategoriKepatuhan;
                $aset->ptkka_status = 'RAMPUNG';
            } else {
                // Tidak punya sesi STATUS=4 → tandai BELUM PERNAH
                $aset->ptkka_status = 'BELUM PERNAH';
                // opsional: nilai default supaya view aman
                $aset->ptkka_persentase = 0;
                $aset->ptkka_kategori_kepatuhan = '-';
            }
        }

        return view('opd.ptkka.index', compact('asets'));
    }

    // public function indexPtkkax()
    // {
    //     $opdId = auth()->user()->opd_id;

    //     $asets = Aset::where('opd_id', $opdId)
    //         ->with(['ptkkaTerakhir.jawabans'])
    //         ->get();
    //     foreach ($asets as $aset) {
    //         $session = $aset->ptkkaTerakhir;

    //         if ($session) {
    //             // Ambil semua rekomendasi_standard dari fungsi standar yang relevan dengan session
    //             $fungsiStandars = FungsiStandar::with('indikators.rekomendasis')
    //                 ->where('kategori_id', $session->standar_kategori_id)
    //                 ->get();

    //             $jumlahRekomendasi = 0;

    //             foreach ($fungsiStandars as $fungsi) {
    //                 foreach ($fungsi->indikators as $indikator) {
    //                     $jumlahRekomendasi += $indikator->rekomendasis->count();
    //                 }
    //             }

    //             $jumlahJawaban = $jumlahRekomendasi;
    //             $skorMaksimal = $jumlahJawaban * 2;
    //             $totalSkor = $session->jawabans->sum('jawaban');

    //             $persentase = $skorMaksimal > 0 ? round(($totalSkor / $skorMaksimal) * 100, 2) : 0;

    //             if ($persentase >= 66.7) {
    //                 $kategoriKepatuhan = 'TINGGI';
    //             } elseif ($persentase >= 33.4) {
    //                 $kategoriKepatuhan = 'SEDANG';
    //             } else {
    //                 $kategoriKepatuhan = 'RENDAH';
    //             }

    //             // Tambahkan properti ke session agar bisa dipakai langsung di view
    //             $session->persentase = $persentase;
    //             $session->kategori_kepatuhan = $kategoriKepatuhan;
    //         }
    //     }
    //     return view('opd.ptkka.index', compact('asets'));
    // }

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




        return view('opd.ptkka.riwayat', compact('aset', 'riwayat'));
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

        return redirect()->route('opd.ptkka.riwayat', $aset->id)->with('success', 'Form PTKKA berhasil dibuat.');
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


        try {
            $session->delete();
            $status = 'success';
            $pesan = 'Pengajuan PTKKA berhasil dihapus.';
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error 1451 untuk foreign key constraint
            $status = 'error';
            $pesan = 'Penghapusan Gagal karena data sudah terpakai';
        } catch (\Throwable $e) {
            // Penanganan error lain
            $status = 'error';
            $pesan = 'Penghapusan Gagal karena data sudah terpakai';
        }

        return back()->with($status, $pesan);
    }



    public function ajukan(PtkkaSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        if ($session->status !== 0) {
            return back()->with('error', 'Status tidak dapat diajukan lagi.');
        }

        // Simpan log status sebelum update
        PtkkaStatusLog::create([
            'ptkka_session_id' => $session->id,
            'from_status' => 0,
            'to_status' => 1,
            'user_id' => auth()->id(),
            'catatan' => 'Pengajuan awal oleh OPD',
            'changed_at' => now(),
        ]);

        // Update status jadi Pengajuan
        $session->update(['status' => 1]);

        return back()->with('success', 'PTKKA berhasil diajukan ke Diskominfos.');
    }

    public function showDetail($id)
    {
        $session = PtkkaSession::with('kategori')->findOrFail($id);
        $kategoriId = $session->standar_kategori_id;
        $fungsiStandars = FungsiStandar::with([
            'indikators.rekomendasis.jawabans'
        ])
            ->where('kategori_id', $session->standar_kategori_id)
            ->orderBy('urutan')
            ->get();


        $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)
            ->get()
            ->keyBy('standar_indikator_id'); // agar mudah diakses di view
        return view('opd.ptkka.detail', compact('session', 'fungsiStandars', 'jawabans'));
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

    public function simpanPerFungsi(Request $request, $sessionId, $fungsiId)
    {
        $session = PtkkaSession::findOrFail($sessionId);

        $jawabans = $request->input('jawaban', []);
        $penjelasanOpd = $request->input('penjelasanopd', []);
        $linkBukti = $request->input('linkbuktidukung', []);

        foreach ($jawabans as $rekomendasiId => $jawabanNilai) {
            $penjelasan = $penjelasanOpd[$rekomendasiId] ?? null;
            $link = $linkBukti[$rekomendasiId] ?? null;

            if (empty($penjelasan) || empty($link)) {
                return back()->with('error', 'Semua jawaban harus disertai Penjelasan dan Link Bukti Dukung.');
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

        return back()->with('success', 'Self-Asssessment PTKKA Berhasil Diupdate');
    }

    public function ajukanVerifikasi(Request $request, $sessionId)
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

        return redirect()->route('opd.ptkka.riwayat', $session->aset_id)
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
        $pdf = PDF::loadView('opd.ptkka.export_pdf', compact(
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
