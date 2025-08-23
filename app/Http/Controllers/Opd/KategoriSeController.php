<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;

use App\Models\Aset;
use App\Models\KategoriSe;
use App\Models\RangeSe;
use App\Models\IndikatorKategoriSe;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\DB;

use App\Models\KlasifikasiAset;
use App\Models\Periode;

class KategoriSeController extends Controller
{
    public function index()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // bebas: bisa redirect balik dengan flash message juga
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $asetPL = Aset::whereHas('klasifikasi', function ($q) {
            $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        })
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId) // ← filter periode aktif
            ->with('kategoriSe')
            ->get();

        // Ambil semua range kategori dari tabel range_ses
        $rangeSes = RangeSe::all();

        // Normalisasi helper
        $norm = fn($v) => strtoupper(trim((string) $v));

        // Bangun mapping yang tahan spasi/kapitalisasi
        $kategoriMeta = collect(['TINGGI', 'SEDANG', 'RENDAH'])->mapWithKeys(function ($K) use ($rangeSes, $norm) {
            $row = $rangeSes->first(fn($r) => $norm($r->nilai_akhir_aset) === $K);
            return [
                $K => [
                    'label'     => $K,
                    'deskripsi' => $row->deskripsi ?? '-',
                ],
            ];
        })->toArray();

        // Tambahan khusus
        $kategoriMeta['BELUM'] = [
            'label'     => 'BELUM DINILAI',
            'deskripsi' => 'Belum ada skor (belum dilakukan penilaian).',
        ];
        $kategoriMeta['TOTAL'] = [
            'label'     => 'TOTAL',
            'deskripsi' => 'Jumlah seluruh aset perangkat lunak pada periode aktif.',
        ];



        // Inisialisasi penghitung kategori
        $kategoriCount = [
            'TINGGI' => 0,
            'SEDANG' => 0,
            'RENDAH' => 0,
            'BELUM' => 0,
            'TOTAL' => $asetPL->count()
        ];

        foreach ($asetPL as $aset) {
            $skor = $aset->kategoriSe->skor_total ?? null;

            if ($skor === null) {
                $kategoriCount['BELUM']++;
                continue;
            }

            // Tentukan kategori berdasarkan skor dan range
            $kategori = $rangeSes->first(function ($range) use ($skor) {
                return $skor >= $range->nilai_bawah && $skor <= $range->nilai_atas;
            });

            if ($kategori) {
                $namaKategori = strtoupper($kategori->nilai_akhir_aset); // contoh: TINGGI
                if (isset($kategoriCount[$namaKategori])) {
                    $kategoriCount[$namaKategori]++;
                } else {
                    $kategoriCount[$namaKategori] = 1;
                }
            }
        }


        return view('opd.kategorise.index', [
            'tinggi' => $kategoriCount['TINGGI'] ?? 0,
            'sedang' => $kategoriCount['SEDANG'] ?? 0,
            'rendah' => $kategoriCount['RENDAH'] ?? 0,
            'belum' => $kategoriCount['BELUM'],
            'total' => $kategoriCount['TOTAL'],
            'namaOpd' => $namaOpd,
            'kategoriMeta' => $kategoriMeta,   // ← tambahkan key + variabel
            'rangeSes'    => $rangeSes,       // ← tambahkan key + variabel
        ]);
    }


    public function syncFromPrevious(Request $request)
    {
        $opdId = auth()->user()->opd_id;

        $periodeAktif = Periode::where('status', 'open')->first();
        if (!$periodeAktif) {
            return back()->withErrors(['periode' => 'Tidak ada periode aktif.']);
        }

        // Batasi: tombol hanya boleh jalan jika TIDAK ada aset di periode aktif
        $sudahAda = Aset::where('opd_id', $opdId)
            ->where('periode_id', $periodeAktif->id)
            ->exists();

        if ($sudahAda) {
            return back()->withErrors([
                'sync' => 'Sinkronisasi dibatalkan: data aset untuk periode aktif sudah ada.'
            ]);
        }

        // Cari periode sebelumnya (tahun aktif - 1)
        $periodeSebelumnya = Periode::where('tahun', $periodeAktif->tahun - 1)->first();
        if (!$periodeSebelumnya) {
            return back()->withErrors([
                'sync' => 'Periode tahun sebelumnya tidak ditemukan.'
            ]);
        }

        // Cek ada aset sumber?
        $totalPrev = Aset::where('opd_id', $opdId)
            ->where('periode_id', $periodeSebelumnya->id)
            ->count();

        if ($totalPrev === 0) {
            return back()->with('warning', 'Tidak ada data aset pada periode tahun sebelumnya untuk disinkronkan.');
        }

        $copiedAssets = 0;
        $skippedAssets = 0;
        $copiedKategori = 0;
        $skippedKategori = 0;

        DB::transaction(function () use (
            $opdId,
            $periodeAktif,
            $periodeSebelumnya,
            &$copiedAssets,
            &$skippedAssets,
            &$copiedKategori,
            &$skippedKategori
        ) {
            // 1) Copy ASET + buat peta ID lama → ID baru
            $idMap = []; // [old_aset_id => new_aset_id]

            Aset::where('opd_id', $opdId)
                ->where('periode_id', $periodeSebelumnya->id)
                ->orderBy('id')
                ->chunkById(300, function ($rows) use ($periodeAktif, &$copiedAssets, &$skippedAssets, &$idMap) {
                    foreach ($rows as $row) {
                        $new = $row->replicate();     // semua kolom kecuali PK
                        $new->periode_id = $periodeAktif->id;

                        // (opsional) jika ada kolom unik (mis. kode_aset), regenerate di sini:
                        // $new->kode_aset = app(YourService::class)->generateKodeAset($row);

                        $new->created_at = now();
                        $new->updated_at = now();

                        try {
                            $new->save();
                            $copiedAssets++;
                            $idMap[$row->id] = $new->id;
                        } catch (\Throwable $e) {
                            $skippedAssets++;
                        }
                    }
                });

            if (empty($idMap)) {
                return; // tidak ada aset yang tersalin → stop
            }

            // 2) Copy KATEGORI_SE dan ganti aset_id ke ID baru hasil peta
            KategoriSe::whereIn('aset_id', array_keys($idMap))
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($idMap, $periodeAktif, &$copiedKategori, &$skippedKategori) {
                    foreach ($rows as $row) {
                        if (!isset($idMap[$row->aset_id])) {
                            $skippedKategori++;
                            continue;
                        }

                        $new = $row->replicate();
                        $new->aset_id = $idMap[$row->aset_id];

                        // Jika tabel kategori_ses punya kolom periode_id dan harus ikut periode aktif, set di sini:
                        // $new->periode_id = $periodeAktif->id;

                        $new->created_at = now();
                        $new->updated_at = now();

                        try {
                            $new->save();
                            $copiedKategori++;
                        } catch (\Throwable $e) {
                            $skippedKategori++;
                        }
                    }
                });
        });

        $msg = "Sinkronisasi selesai → " .
            "Aset: {$copiedAssets} tersalin" . ($skippedAssets ? ", {$skippedAssets} dilewati" : "") .
            " | Kategori SE: {$copiedKategori} tersalin" . ($skippedKategori ? ", {$skippedKategori} dilewati" : "") . ".";

        return back()->with('success', $msg);
    }

    public function show($kategori)
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // Ambil semua range dari DB
        $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [strtolower($kategori)])->first();

        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // bebas: bisa redirect balik dengan flash message juga
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $query = Aset::whereHas('klasifikasi', function ($q) {
            $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        })
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId) // ← filter periode aktif
            ->with('kategoriSe');

        // $query = Aset::whereHas('klasifikasi', function ($q) {
        //     $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        // })
        //     ->where('opd_id', $userOpdId)
        //     ->with(['subklasifikasiaset', 'kategoriSe']);

        if ($range) {
            // Jika ada skor total, filter berdasarkan range
            $query->whereHas('kategoriSe', function ($q) use ($range) {
                $q->where('skor_total', '>=', $range->nilai_bawah)
                    ->where('skor_total', '<=', $range->nilai_atas);
            });
        } elseif ($kategori === 'belum') {
            // Jika kategori "belum", tampilkan yang belum dinilai
            $query->doesntHave('kategoriSe');
        }

        $data = $query->get();
        $rangeSes = RangeSe::all();
        return view('opd.kategorise.list', compact('data', 'kategori', 'namaOpd', 'rangeSes'));
    }


    public function edit($asetId)
    {
        $aset = Aset::findOrFail($asetId);
        $indikators = IndikatorKategoriSe::orderBy('urutan')->get();

        $kategoriSe = KategoriSe::where('aset_id', $asetId)->first();
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        return view('opd.kategorise.edit', compact('aset', 'indikators', 'kategoriSe', 'namaOpd'));
    }

    public function exportRekapPdf()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // bebas: bisa redirect balik dengan flash message juga
            abort(409, 'Tidak ada periode yang berstatus open.');
        }
        $asetPL = Aset::whereHas('klasifikasi', function ($q) {
            $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        })
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId) // ← filter periode aktif
            ->with('kategoriSe')
            ->get();

        $rangeSes = RangeSe::all();
        // Normalisasi helper
        $norm = fn($v) => strtoupper(trim((string) $v));

        // Bangun mapping yang tahan spasi/kapitalisasi
        $kategoriMeta = collect(['TINGGI', 'SEDANG', 'RENDAH'])->mapWithKeys(function ($K) use ($rangeSes, $norm) {
            $row = $rangeSes->first(fn($r) => $norm($r->nilai_akhir_aset) === $K);
            return [
                $K => [
                    'label'     => $K,
                    'deskripsi' => $row->deskripsi ?? '-',
                ],
            ];
        })->toArray();

        // Tambahan khusus

        $kategoriMeta['BELUM'] = [
            'label'     => 'BELUM DINILAI',
            'deskripsi' => 'Belum ada skor (belum dilakukan penilaian).',
        ];
        $kategoriMeta['TOTAL'] = [
            'label'     => 'TOTAL',
            'deskripsi' => 'Jumlah seluruh aset perangkat lunak pada periode aktif.',
        ];

        // Inisialisasi penghitung kategori
        $kategoriCount = [
            'TINGGI' => 0,
            'SEDANG' => 0,
            'RENDAH' => 0,
            'BELUM' => 0,
            'TOTAL' => $asetPL->count()
        ];

        foreach ($asetPL as $aset) {
            $skor = $aset->kategoriSe->skor_total ?? null;

            if ($skor === null) {
                $kategoriCount['BELUM']++;
                continue;
            }

            // Tentukan kategori berdasarkan skor dan range
            $kategori = $rangeSes->first(function ($range) use ($skor) {
                return $skor >= $range->nilai_bawah && $skor <= $range->nilai_atas;
            });

            if ($kategori) {
                $namaKategori = strtoupper($kategori->nilai_akhir_aset); // contoh: TINGGI
                if (isset($kategoriCount[$namaKategori])) {
                    $kategoriCount[$namaKategori]++;
                } else {
                    $kategoriCount[$namaKategori] = 1;
                }
            }
        }
        $tinggi = $kategoriCount['TINGGI'];
        $sedang = $kategoriCount['SEDANG'];
        $rendah = $kategoriCount['RENDAH'];
        $total = $kategoriCount['TOTAL'];
        $belum = $kategoriCount['BELUM'];

        $namaOpd = auth()->user()->opd->namaopd ?? '-';


        $pdf = PDF::loadView('opd.kategorise.export_rekap_pdf', compact('tinggi', 'sedang', 'rendah', 'belum', 'total', 'namaOpd', 'kategoriMeta'))
            ->setPaper('A4', 'portrait');

        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI  :: Page $pageNumber of $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = $height - 30;
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('rekap_kategorise_' . date('Ymd_His') . '.pdf');
    }

    public function update(Request $request, $asetId)
    {
        $request->validate([
            'jawaban' => 'required|array',
            'kategori' => 'required|in:tinggi,sedang,rendah',
        ]);

        $indikators = IndikatorKategoriSe::all()->keyBy('kode');

        $inputJawaban = $request->input('jawaban');
        $skorTotal = 0;

        foreach ($inputJawaban as $kode => $data) {
            $jawaban = strtoupper($data['jawaban'] ?? '');
            if ($jawaban === 'A') {
                $skorTotal += $indikators[$kode]->nilai_a ?? 0;
            } elseif ($jawaban === 'B') {
                $skorTotal += $indikators[$kode]->nilai_b ?? 0;
            } elseif ($jawaban === 'C') {
                $skorTotal += $indikators[$kode]->nilai_c ?? 0;
            }
        }

        try {
            KategoriSe::updateOrCreate(
                ['aset_id' => $asetId],
                [
                    'jawaban' => $inputJawaban,
                    'skor_total' => $skorTotal
                ]
            );
            $kategori = $request->string('kategori'); // 'tinggi' | 'sedang' | 'rendah'
            return redirect()
                ->route('opd.kategorise.show', ['kategori' => $kategori])
                ->with('success', 'Kategori SE berhasil diperbaharui.');
        } catch (QueryException $e) {
            Log::warning('Gagal memperbaharui aset (QueryException)', [
                'mysql_code' => $e->errorInfo[1] ?? null,   // 1062, 1452, dst.
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'user_id'    => auth()->id(),
                'route'      => request()->path(),
            ]);
            return back()->withInput()->with('error', 'Gagal memperbaharui. Silakan periksa kembali isian Anda.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba lagi atau hubungi admin.');
        }



        // KategoriSe::updateOrCreate(
        //     ['aset_id' => $asetId],
        //     [
        //         'jawaban' => $inputJawaban,
        //         'skor_total' => $skorTotal
        //     ]
        // );

        // return redirect()->route('opd.kategorise.index')->with('success', 'Penilaian berhasil disimpan.');
    }

    public function exportRekapKategoriPdf($kategori)
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // Ambil semua range dari DB
        $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [strtolower($kategori)])->first();

        $query = Aset::whereHas('klasifikasi', function ($q) {
            $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        })
            ->where('opd_id', $userOpdId)
            ->with(['subklasifikasiaset', 'kategoriSe']);

        if ($range) {
            // Jika ada skor total, filter berdasarkan range
            $query->whereHas('kategoriSe', function ($q) use ($range) {
                $q->where('skor_total', '>=', $range->nilai_bawah)
                    ->where('skor_total', '<=', $range->nilai_atas);
            });
        } elseif ($kategori === 'belum') {
            // Jika kategori "belum", tampilkan yang belum dinilai
            $query->doesntHave('kategoriSe');
        }

        $data = $query->get();
        $rangeSes = RangeSe::all();



        $pdf = PDF::loadView('opd.kategorise.export_rekap_kategori_pdf', compact('data', 'kategori', 'namaOpd', 'rangeSes'))
            ->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI  :: Page $pageNumber of $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2; // Center horizontally
            $y = $height - 30; // 30 px from bottom
            $canvas->text($x, $y, $text, $font, $size);
        });
        return $pdf->download('kategorise_pernilai_' . date('Ymd_His') . '.pdf');
    }


    public function exportPdf($id)
    {
        $aset = \App\Models\Aset::with(['kategoriSe', 'opd'])->findOrFail($id);
        $kategoriSe = $aset->kategoriSe;
        $indikators = IndikatorKategoriSe::orderBy('urutan')->get();
        $rangeSes = RangeSe::all();

        $skor = $kategoriSe->skor_total ?? 0;

        $range = $rangeSes->first(function ($r) use ($skor) {
            return $skor >= $r->nilai_bawah && $skor <= $r->nilai_atas;
        });

        $kategoriLabel = $range->nilai_akhir_aset ?? 'BELUM DINILAI';
        $warna = $range->warna_hexa ?? '#888';


        $pdf = PDF::loadView('opd.kategorise.pdf_detail', compact(
            'aset',
            'kategoriSe',
            'indikators',
            'kategoriLabel',
            'warna',
            'skor'
        ))
            ->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI  :: Page $pageNumber of $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2; // Center horizontally
            $y = $height - 30; // 30 px from bottom
            $canvas->text($x, $y, $text, $font, $size);
        });
        return $pdf->download('penilaian_kategori_se_' . date('Ymd_His') . '.pdf');

        // return Pdf::loadView('opd.kategorise.pdf_detail', compact(
        //     'aset',
        //     'kategoriSe',
        //     'indikators',
        //     'kategoriLabel',
        //     'warna',
        //     'skor'
        // ))->setPaper('A4', 'portrait')->download('penilaian_kategori_se_' . $aset->id . '.pdf');
    }
}
