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
use App\Models\RekomendasiStandard;
use App\Models\PtkkaJawaban;
use App\Models\Periode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PtkkaController extends Controller
{
    public function indexPtkka()
    {

        $this->authorize('viewAny', Aset::class);

        $opdId = auth()->user()->opd_id;

        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // bebas: bisa redirect balik dengan flash message juga
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $asets = Aset::where('opd_id', $opdId)
            ->where('periode_id', $periodeAktifId)
            ->whereHas('klasifikasi', function ($q) {
                $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
            })
            ->with([
                // sesi PTKKA terakhir berstatus RAMPUNG + jawabans-nya
                'ptkkaTerakhirRampung.jawabans:id,ptkka_session_id,jawaban',
                'klasifikasi:id,klasifikasiaset',
            ])
            ->orderBy('nama_aset')
            ->get();

        $kategoriIds = $asets
            ->pluck('ptkkaTerakhirRampung.standar_kategori_id')
            ->filter()
            ->unique()
            ->values();
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
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        return view('opd.ptkka.index', compact('asets', 'namaOpd'));
    }

    public function riwayat(Aset $aset)
    {
        $this->authorize('view', $aset);
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
        $namaOpd = auth()->user()->opd->namaopd ?? '-';
        return view('opd.ptkka.riwayat', compact('aset', 'riwayat', 'namaOpd'));
    }

    public function store(Aset $aset, Request $request)
    {
        if ($aset->opd_id !== auth()->user()->opd_id) {
            abort(403);
        }

        $masihAktif = $aset->ptkkaSessions()
            ->whereIn('status', [0, 1, 2, 3])
            ->exists();

        if ($masihAktif) {
            return back()->with('error', 'Anda masih punya PTKKA yang masih berlangsung untuk aset ini.');
        }

        $kategoriId = $request->standar_kategori_id;

        if (!in_array($kategoriId, [2, 3])) {
            return back()->with('error', 'Kategori standar tidak valid.');
        }

        try {

            $session = PtkkaSession::create([
                'user_id'            => auth()->id(),
                'aset_id'            => $aset->id,
                'standar_kategori_id' => $kategoriId,
                'status'             => 0,
                'uid'                => Str::uuid(),
            ]);

            PtkkaStatusLog::create([
                'ptkka_session_id' => $session->id,
                'from_status'      => null,
                'to_status'        => 0,
                'user_id'          => auth()->id(),
                'catatan'          => 'Membuat sesi PTKKA',
                'changed_at'       => now(),
            ]);
            return redirect()
                ->route('opd.ptkka.riwayat', $aset)
                ->with('success', 'Form PTKKA berhasil dibuat.');
        } catch (QueryException $e) {
            Log::warning('KategoriSe update QueryException', [
                'mysql_code' => $e->errorInfo[1] ?? null,
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'aset_id'    => $aset->id,
            ]);
            return back()->withInput()->with('error', 'Gagal menyimpan. Periksa kembali isian Anda.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function destroy(PtkkaSession $session)
    {
        // 🔐 Hanya OPD pemilik aset yang boleh menghapus
        $session->load('aset:id,opd_id');
        if (($session->aset->opd_id ?? null) !== auth()->user()->opd_id) {
            abort(403);
        }

        // 🔐 Hanya boleh hapus saat status = 0 (Pengisian)
        if ((int) $session->status !== 0) {
            return back()->with('error', 'Hanya PTKKA berstatus Pengisian yang dapat dihapus.');
        }

        try {
            DB::transaction(function () use ($session) {
                // Hapus data turunan jika belum cascade di schema
                // Contoh:
                $session->jawabans()->delete();       // if exists: hasMany PtkkaJawaban
                $session->statusLogs()->delete();     // if exists: hasMany PtkkaStatusLog

                $session->delete();
            });

            return back()->with('success', 'Pengajuan PTKKA berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // 1451 = cannot delete/update parent row (FK constraint)
            return back()->with('error', 'Penghapusan gagal: data terkait masih digunakan.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus. Silakan coba lagi.');
        }
    }

    public function showDetail(PtkkaSession $session)
    {
        // batasi hanya ke OPD yang sedang login
        abort_unless(
            $session->aset && auth()->user()?->opd_id === $session->aset->opd_id,
            403
        );
        // load aset lengkap kolom yang dibutuhkan
        $session->load([
            'aset:id,uuid,opd_id,nama_aset,kode_aset',
            'kategori',
        ]);

        $fungsiStandars = FungsiStandar::with([
            'indikators.rekomendasis.jawabans' // asumsi eager ini memang diperlukan
        ])
            ->where('kategori_id', $session->standar_kategori_id)
            ->orderBy('urutan')
            ->get();

        $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)
            ->get()
            ->keyBy('standar_indikator_id');
        $namaOpd = auth()->user()->opd->namaopd ?? '-';
        return view('opd.ptkka.detail', compact('session', 'fungsiStandars', 'jawabans', 'namaOpd'));
    }

    // public function simpan(Request $request, PtkkaSession $session)
    // {
    //     // 🔐 pastikan milik OPD yang login
    //     $session->load('aset:id,opd_id');
    //     if (($session->aset->opd_id ?? null) !== auth()->user()->opd_id) {
    //         abort(403);
    //     }

    //     // 🔐 hanya boleh simpan saat status 0 (Pengisian) atau 3 (Klarifikasi)
    //     if (!in_array((int)$session->status, [0, 3], true)) {
    //         return back()->with('error', 'Sesi tidak dapat diedit pada status saat ini.');
    //     }

    //     // ✅ Validasi input
    //     $rules = [
    //         'jawaban'                    => 'required|array',
    //         'jawaban.*'                  => 'required|in:0,1,2',
    //         'penjelasanopd'              => 'required|array',
    //         'penjelasanopd.*'            => 'required|string',
    //         'linkbuktidukung'            => 'required|array',
    //         'linkbuktidukung.*'          => 'required|url',
    //     ];
    //     $messages = [
    //         'jawaban.required'           => 'Semua rekomendasi wajib diberi jawaban.',
    //         'jawaban.*.in'               => 'Nilai jawaban harus 0/1/2.',
    //         'penjelasanopd.*.required'   => 'Penjelasan wajib diisi.',
    //         'linkbuktidukung.*.required' => 'Link bukti dukung wajib diisi.',
    //         'linkbuktidukung.*.url'      => 'Link bukti dukung harus berupa URL yang valid.',
    //     ];
    //     $validated = $request->validate($rules, $messages);

    //     $jawabans      = $validated['jawaban'];
    //     $penjelasanOpd = $validated['penjelasanopd'];
    //     $linkBukti     = $validated['linkbuktidukung'];

    //     try {
    //         DB::transaction(function () use ($session, $jawabans, $penjelasanOpd, $linkBukti) {
    //             foreach ($jawabans as $rekomendasiId => $jawabanNilai) {
    //                 PtkkaJawaban::updateOrCreate(
    //                     [
    //                         'ptkka_session_id'        => $session->id,
    //                         'rekomendasi_standard_id' => $rekomendasiId,
    //                     ],
    //                     [
    //                         'jawaban'          => (int) $jawabanNilai,
    //                         'penjelasanopd'    => $penjelasanOpd[$rekomendasiId] ?? null,
    //                         'linkbuktidukung'  => $linkBukti[$rekomendasiId] ?? null,
    //                     ]
    //                 );
    //             }
    //         });

    //         return back()->with('success', 'Jawaban berhasil disimpan.');
    //     } catch (QueryException $e) {
    //         Log::warning('PTKKA simpan QueryException', [
    //             'mysql_code' => $e->errorInfo[1] ?? null,
    //             'sql_state'  => $e->errorInfo[0] ?? null,
    //             'driver_msg' => $e->errorInfo[2] ?? null,
    //             'session_id' => $session->id,
    //         ]);
    //         return back()->withInput()->with('error', 'Gagal menyimpan. Periksa kembali isian Anda.');
    //     } catch (\Throwable $e) {
    //         report($e);
    //         return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
    //     }
    // }

    public function simpanPerFungsi(Request $request, PtkkaSession $session, FungsiStandar $fungsi)
    {
        // 🔐 OPD pemilik aset saja yang boleh
        $session->load('aset:id,opd_id');
        if (($session->aset->opd_id ?? null) !== auth()->user()->opd_id) {
            abort(403);
        }

        // 🔐 Hanya boleh edit saat Pengisian (0) atau Klarifikasi (3)
        if (!in_array((int) $session->status, [0, 3], true)) {
            return back()->with('error', 'Sesi tidak dapat diedit pada status saat ini.');
        }

        // 🔒 Fungsi harus sesuai kategori sesi ini (hindari tampering lintas fungsi/kategori)
        if ((int) $fungsi->kategori_id !== (int) $session->standar_kategori_id) {
            abort(404);
        }

        // ✅ Validasi input
        $validated = $request->validate([
            'jawaban'                     => 'required|array',
            'jawaban.*'                   => 'required|in:0,1,2,3',
            'penjelasanopd'               => 'required|array',
            'penjelasanopd.*'             => 'required|string',
            'linkbuktidukung'             => 'required|array',
            'linkbuktidukung.*'           => 'required|url',
        ], [
            'jawaban.required'            => 'Semua rekomendasi wajib diberi jawaban.',
            'jawaban.*.in'                => 'Nilai jawaban harus 0/1/2/3.',
            'penjelasanopd.*.required'    => 'Penjelasan wajib diisi.',
            'linkbuktidukung.*.required'  => 'Link bukti dukung wajib diisi.',
            'linkbuktidukung.*.url'       => 'Link bukti dukung harus URL yang valid.',
        ]);

        $validRekomIds = \App\Models\RekomendasiStandard::whereHas('indikator', function ($q) use ($fungsi) {
            $q->where('fungsi_standar_id', $fungsi->id);
        })
            ->pluck('id')
            ->map(fn($v) => (int)$v)
            ->all();


        $incomingIds = array_map('intval', array_keys($validated['jawaban']));
        $allowedIds  = array_values(array_intersect($incomingIds, $validRekomIds));

        try {
            DB::transaction(function () use ($session, $validated, $allowedIds) {
                foreach ($allowedIds as $rekomId) {
                    PtkkaJawaban::updateOrCreate(
                        [
                            'ptkka_session_id'        => $session->id,
                            'rekomendasi_standard_id' => $rekomId,
                        ],
                        [
                            'jawaban'         => (int) $validated['jawaban'][$rekomId],
                            'penjelasanopd'   => $validated['penjelasanopd'][$rekomId],
                            'linkbuktidukung' => $validated['linkbuktidukung'][$rekomId],
                        ]
                    );
                }
            });

            return back()->with('success', 'Self-Assessment PTKKA berhasil diupdate (aspek: ' . $fungsi->nama . ').');
        } catch (QueryException $e) {
            Log::warning('PTKKA simpanPerFungsi QueryException', [
                'mysql_code' => $e->errorInfo[1] ?? null,
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'session_id' => $session->id,
                'fungsi_id'  => $fungsi->id,
            ]);
            return back()->withInput()->with('error', 'Gagal menyimpan. Periksa kembali isian Anda.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
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

    public function exportPDF(PtkkaSession $session)
    {

        // batasi hanya ke OPD yang sedang login
        abort_unless(
            $session->aset && auth()->user()?->opd_id === $session->aset->opd_id,
            403
        );

        $session->load(['kategori', 'aset.opd']);

        $fungsiStandars = FungsiStandar::with(['indikators.rekomendasis'])
            ->where('kategori_id', $session->standar_kategori_id)
            ->orderBy('urutan')
            ->get();

        // Jawaban user (keyBy rekomendasi)
        $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)
            ->get()
            ->keyBy('rekomendasi_standard_id');

        // Hitung total rekomendasi relevan (semua rekomendasi di kategori)
        $jumlahRekomendasi = 0;
        foreach ($fungsiStandars as $fungsi) {
            foreach ($fungsi->indikators as $indikator) {
                $jumlahRekomendasi += $indikator->rekomendasis->count();
            }
        }

        $skorMaksimal = $jumlahRekomendasi * 2;
        $totalSkor    = (int) $jawabans->sum('jawaban');
        $persentase   = 0.0;
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

        /**
         * ======== Hitung skor per fungsi ========
         * $skorPerFungsi: array berisi ringkasan per FungsiStandar
         */
        // $skorPerFungsi = [];
        // foreach ($fungsiStandars as $fungsi) {
        //     $jmlRek = 0;
        //     $skorTotalFungsi = 0;

        //     foreach ($fungsi->indikators as $indikator) {
        //         foreach ($indikator->rekomendasis as $rek) {
        //             $jmlRek++;
        //             $skorTotalFungsi += (int) ($jawabans[$rek->id]->jawaban ?? 0);
        //         }
        //     }

        //     $skorMaksFungsi = $jmlRek * 2;
        //     $persenFungsi = $skorMaksFungsi > 0 ? round(($skorTotalFungsi / $skorMaksFungsi) * 100, 2) : 0;

        //     $kategoriFungsi = 'TIDAK TERDEFINISI';
        //     if ($skorMaksFungsi > 0) {
        //         if ($persenFungsi >= 66.7) {
        //             $kategoriFungsi = 'TINGGI';
        //         } elseif ($persenFungsi >= 33.4) {
        //             $kategoriFungsi = 'SEDANG';
        //         } else {
        //             $kategoriFungsi = 'RENDAH';
        //         }
        //     }

        //     $skorPerFungsi[] = [
        //         'fungsi_id'            => $fungsi->id,
        //         'fungsi_nama'          => $fungsi->nama ?? ('Fungsi #' . $fungsi->id),
        //         'jumlah_rekomendasi'   => $jmlRek,
        //         'skor_maks'            => $skorMaksFungsi,
        //         'skor_total'           => $skorTotalFungsi,
        //         'persentase'           => $persenFungsi,
        //         'kategori'             => $kategoriFungsi,
        //     ];
        // }

        // Pastikan $jawabans sudah di-keyBy rekomendasi_standard_id:
        // $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)->get()
        //     ->keyBy('rekomendasi_standard_id');

        $skorPerFungsi = [];
        $maksPerRek = 3; // skema 0..3

        foreach ($fungsiStandars as $fungsi) {
            $jmlTotal       = 0; // total rekomendasi awal (apa adanya)
            $jmlDipakai     = 0; // rekomendasi yg dihitung (jawaban 1/2/3)
            $jmlZero        = 0; // rekomendasi dengan jawaban 0 (Tidak Relevan)
            $jmlBelum       = 0; // rekomendasi belum dijawab (null)
            $skorTotal      = 0;

            foreach ($fungsi->indikators as $indikator) {
                foreach ($indikator->rekomendasis as $rek) {
                    $jmlTotal++;

                    $rowJawab = $jawabans[$rek->id] ?? null;
                    $nilai    = $rowJawab ? (int) $rowJawab->jawaban : null;

                    if ($nilai === null) {
                        // Belum diisi → tidak masuk denominator & tampil "-"
                        $jmlBelum++;
                        continue;
                    }

                    if ($nilai === 0) {
                        // Tidak relevan → dikeluarkan dari denominator
                        $jmlZero++;
                        continue;
                    }

                    // Hanya nilai 1/2/3 yang dihitung
                    $jmlDipakai++;
                    $skorTotal += min($nilai, $maksPerRek);
                }
            }

            $skorMaks   = $jmlDipakai * $maksPerRek;
            $persen     = $skorMaks > 0 ? round(($skorTotal / $skorMaks) * 100, 2) : 0.0;

            $kategori = 'TIDAK TERDEFINISI';
            if ($skorMaks > 0) {
                if ($persen >= 66.7) {
                    $kategori = 'TINGGI';
                } elseif ($persen >= 33.4) {
                    $kategori = 'SEDANG';
                } else {
                    $kategori = 'RENDAH';
                }
            }

            $skorPerFungsi[] = [
                'fungsi_id'                    => $fungsi->id,
                'fungsi_nama'                  => $fungsi->nama ?? ('Fungsi #' . $fungsi->id),
                'jumlah_rekomendasi_total'     => $jmlTotal,    // <-- total awal yang kamu minta
                'jumlah_rekomendasi_dipakai'   => $jmlDipakai,  // exclude 0 & null
                'jumlah_rekomendasi_zero'      => $jmlZero,     // info tambahan
                'jumlah_rekomendasi_belum'     => $jmlBelum,    // info tambahan
                'skor_maks'                    => $skorMaks,
                'skor_total'                   => $skorTotal,
                'persentase'                   => $persen,
                'kategori'                     => $kategori,
            ];
        }



        $pdf = Pdf::loadView('opd.ptkka.export_pdf', compact(
            'session',
            'fungsiStandars',
            'jawabans',
            'jumlahRekomendasi',
            'skorMaksimal',
            'totalSkor',
            'kategoriKepatuhan',
            'persentase',
            'skorPerFungsi' // <— tambahan untuk view
        ))->setPaper([0, 0, 595.28, 841.89], 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount) use ($canvas, $fontMetrics) {
            $text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = 820;
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('ptkka-' . $session->uid . '.pdf');
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
}
