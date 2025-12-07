<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Aset;
use App\Models\KategoriSe;
use App\Models\RangeSe;
use App\Models\IndikatorKategoriSe;
use PDF;
use App\Services\PdfFooter;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\QueryException;

use App\Models\KlasifikasiAset;
use App\Models\Periode;
use Illuminate\Support\Facades\Log;

class KategoriSeController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Aset::class, 'aset');
    }


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

        // $asetPL = Aset::whereHas('klasifikasi', function ($q) {
        //     $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        // })
        //     ->where('opd_id', $userOpdId)
        //     ->where('periode_id', $periodeAktifId) // ← filter periode aktif
        //     ->with('kategoriSe')
        //     ->get();

        $asetPL = Aset::whereHas('subklasifikasiaset', function ($q) {
            $q->whereIn('subklasifikasiaset', [
                'Aplikasi berbasis Website',
                'Aplikasi berbasis Mobile',
                'Aplikasi berbasis Desktop'
            ]);
        })
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId)   // filter periode aktif
            ->with(['kategoriSe', 'subklasifikasiaset']) // sekalian eager load subklas
            ->get();


        // Ambil semua range kategori dari tabel range_ses
        $rangeSes = RangeSe::all();

        // Normalisasi helper
        $norm = fn($v) => strtoupper(trim((string) $v));

        // Bangun mapping yang tahan spasi/kapitalisasi
        $kategoriMeta = collect(['STRATEGIS', 'TINGGI', 'RENDAH'])->mapWithKeys(function ($K) use ($rangeSes, $norm) {
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
            'STRATEGIS' => 0,
            'TINGGI' => 0,
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
            'strategis' => $kategoriCount['STRATEGIS'] ?? 0,
            'rendah' => $kategoriCount['RENDAH'] ?? 0,
            'belum' => $kategoriCount['BELUM'],
            'total' => $kategoriCount['TOTAL'],
            'namaOpd' => $namaOpd,
            'kategoriMeta' => $kategoriMeta,   // ← tambahkan key + variabel
            'rangeSes'    => $rangeSes,       // ← tambahkan key + variabel
        ]);
    }

    public function exportRekapPdf_old()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // bebas: bisa redirect balik dengan flash message juga
            abort(409, 'Tidak ada periode yang berstatus open.');
        }
        // $asetPL = Aset::whereHas('klasifikasi', function ($q) {
        //     $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        // })
        //     ->where('opd_id', $userOpdId)
        //     ->where('periode_id', $periodeAktifId) // ← filter periode aktif
        //     ->with('kategoriSe')
        //     ->get();

        $asetPL = Aset::whereHas('subklasifikasiaset', function ($q) {
            $q->whereIn('subklasifikasiaset', [
                'Aplikasi berbasis Website',
                'Aplikasi berbasis Mobile',
                'Aplikasi berbasis Desktop'
            ]);
        })
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId)   // filter periode aktif
            ->with(['kategoriSe', 'subklasifikasiaset']) // sekalian eager load subklas
            ->get();

        $rangeSes = RangeSe::all();
        // Normalisasi helper
        $norm = fn($v) => strtoupper(trim((string) $v));

        // Bangun mapping yang tahan spasi/kapitalisasi
        $kategoriMeta = collect(['STRATEGIS', 'TINGGI', 'RENDAH'])->mapWithKeys(function ($K) use ($rangeSes, $norm) {
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
            'STRATEGIS' => 0,
            'TINGGI' => 0,
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
        $strategis = $kategoriCount['STRATEGIS'];
        $tinggi = $kategoriCount['TINGGI'];
        $rendah = $kategoriCount['RENDAH'];
        $total = $kategoriCount['TOTAL'];
        $belum = $kategoriCount['BELUM'];

        $namaOpd = auth()->user()->opd->namaopd ?? '-';


        $pdf = PDF::loadView('opd.kategorise.export_rekap_pdf', compact('strategis', 'tinggi', 'rendah', 'belum', 'total', 'namaOpd', 'kategoriMeta'))
            ->setPaper('A4', 'portrait');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('rekap_kategorise_' . date('Ymd_His') . '.pdf');
    }

    public function exportRekapPdf()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $query = Aset::query()
            ->where('opd_id', $userOpdId)
            // ->where('periode_id', $periodeAktifId) // aktifkan jika perlu filter periode
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                    'Aplikasi berbasis Desktop'
                ]);
            })
            ->with([
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id',
                'kategoriSe:id,aset_id,skor_total,jawaban',
            ]);


        $data = $query->get();
        $rangeSes = RangeSe::all();

        // Ranking kategori untuk sorting
        $orderKategori = [
            'STRATEGIS'        => 1,
            'TINGGI'        => 2,
            'RENDAH'        => 3,
            'Belum Dinilai' => 4,
        ];

        $sorted = $data->sort(function ($a, $b) use ($rangeSes, $orderKategori) {

            // Tentukan kategori aset A
            $katA = $a->kategoriSe
                ? $rangeSes->first(function ($r) use ($a) {
                    return $a->kategoriSe->skor_total >= $r->nilai_bawah &&
                        $a->kategoriSe->skor_total <= $r->nilai_atas;
                })->nilai_akhir_aset ?? 'Belum Dinilai'
                : 'Belum Dinilai';

            // Tentukan kategori aset B
            $katB = $b->kategoriSe
                ? $rangeSes->first(function ($r) use ($b) {
                    return $b->kategoriSe->skor_total >= $r->nilai_bawah &&
                        $b->kategoriSe->skor_total <= $r->nilai_atas;
                })->nilai_akhir_aset ?? 'Belum Dinilai'
                : 'Belum Dinilai';

            // 1. Sort kategori dulu
            $cmp = $orderKategori[$katA] <=> $orderKategori[$katB];
            if ($cmp !== 0) return $cmp;

            // 2. Sort nama aset ASC
            return strcmp($a->nama_aset, $b->nama_aset);
        });

        $data = $sorted->values(); // reset keys


        $pdf = PDF::loadView('opd.kategorise.export_rekap_pdf', compact('data', 'namaOpd', 'rangeSes'))
            ->setPaper('A4', 'landscape');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('rekap_kategorise_' . date('Ymd_His') . '.pdf');
    }

    public function exportRekapKategoriPdf($kategori)
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // Ambil semua range dari DB
        $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [strtolower($kategori)])->first();

        // $query = Aset::whereHas('klasifikasi', function ($q) {
        //     $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        // })
        //     ->where('opd_id', $userOpdId)
        //     ->with(['subklasifikasiaset', 'kategoriSe']);

        $query = Aset::query()
            ->where('opd_id', $userOpdId)
            // ->where('periode_id', $periodeAktifId) // aktifkan jika perlu filter periode
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                    'Aplikasi berbasis Desktop'
                ]);
            })
            ->with([
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id',
                'kategoriSe:id,aset_id,skor_total,jawaban',
            ]);



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
            ->setPaper('A4', 'landscape');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('kategorise_pernilai_' . date('Ymd_His') . '.pdf');
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


    public function showByKategori(string $kategori)
    {
        $allowed = ['strategis', 'tinggi', 'rendah', 'belum']; // tambah 'total' kalau perlu
        if (! in_array(strtolower($kategori), $allowed, true)) {
            abort(404);
        }

        $user = auth()->user();
        $userOpdId = $user->opd_id;
        $namaOpd   = $user->opd->namaopd ?? '-';

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (! $periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        // base query: aset perangkat lunak milik OPD & periode aktif
        // $query = Aset::query()
        //     ->where('opd_id', $userOpdId)
        //     ->where('periode_id', $periodeAktifId)
        //     ->whereHas('klasifikasi', fn($q) => $q->where('klasifikasiaset', 'PERANGKAT LUNAK'))
        //     ->with('kategoriSe:id,aset_id,skor_total,jawaban'); // cukup field penting

        $query = Aset::query()
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId)
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                    'Aplikasi berbasis Desktop'
                ]);
            })
            ->with([
                'kategoriSe:id,aset_id,skor_total,jawaban',
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id'
            ]);


        // filter kategori
        if ($kategori === 'belum') {
            $query->doesntHave('kategoriSe');
        } else {
            $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [strtolower($kategori)])->first();
            if (! $range) {
                abort(404); // tak ada range utk kategori tsb
            }
            $query->whereHas('kategoriSe', function ($q) use ($range) {
                $q->whereBetween('skor_total', [$range->nilai_bawah, $range->nilai_atas]);
            });
        }

        $data = $query->orderBy('nama_aset')->get();
        $rangeSes = RangeSe::all();

        return view('opd.kategorise.list', compact('data', 'kategori', 'namaOpd', 'rangeSes'));
    }



    public function exportPdf(Aset $aset) // ← dibinding via UUID
    {
        $this->authorize('view', $aset);
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $periodeAktifId = \App\Models\Periode::where('status', 'open')->value('id');
        if (! $periodeAktifId || (int)$aset->periode_id !== (int)$periodeAktifId) {
            abort(404);
        }

        if ((int)$aset->opd_id !== (int)(auth()->user()->opd_id ?? 0)) {
            abort(404);
        }

        // ⬇⬇ gunakan NAMA RELASI yang benar
        $aset->loadMissing([
            'klasifikasi:id,kodeklas,klasifikasiaset',
            'subklasifikasiaset:id,subklasifikasiaset',
            'opd:id,namaopd',
            'kategoriSe', // hasOne
        ]);

        // cek hanya untuk klasifikasi "PERANGKAT LUNAK" jika memang wajib
        if (optional($aset->klasifikasi)->kodeklas !== 'PL') {
            abort(404);
        }

        $kategoriSe = $aset->kategoriSe;
        $indikators = IndikatorKategoriSe::orderBy('urutan')->get();
        $rangeSes   = RangeSe::all();

        // 6) Hitung kategori & warna berdasar skor
        $skor  = (int) ($kategoriSe->skor_total ?? 0);
        $range = $rangeSes->first(fn($r) => $skor >= $r->nilai_bawah && $skor <= $r->nilai_atas);

        $kategoriLabel = $range->nilai_akhir_aset ?? 'BELUM DINILAI';
        $deskripsiLabel = $range->deskripsi ?? '-';
        $warna         = $range->warna_hexa ?? '#888888';

        // 7) Render PDF
        $pdf = PDF::loadView('opd.kategorise.pdf_detail', compact(
            'aset',
            'kategoriSe',
            'indikators',
            'rangeSes',
            'kategoriLabel',
            'warna',
            'skor',
            'namaOpd',
            'deskripsiLabel'
        ))
            ->setPaper('a4', 'portrait');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('penilaian_kategori_se_' . now()->format('Ymd_His') . '.pdf');
    }


    // GET /opd/kategorise/{aset}/edit
    public function edit(Request $request, Aset $aset)
    {

        $this->authorize('update', $aset);

        // validasi query ?kategori= (opsional, kalau mau dipakai di view)
        $kategori = strtoupper((string) $request->query('kategori', ''));
        if ($kategori && !in_array($kategori, ['STRATEGIS', 'TINGGI', 'RENDAH', 'BELUM'], true)) {
            abort(404);
        }

        $indikators = IndikatorKategoriSe::orderBy('urutan')->get();

        // lewat relasi (lebih aman dari N+1)
        $kategoriSe = $aset->kategoriSe()->first(); // bisa null jika belum pernah dinilai

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        return view('opd.kategorise.edit', compact(
            'aset',
            'indikators',
            'kategoriSe',
            'namaOpd',
            'kategori'
        ));
    }

    // PUT /opd/kategorise/{aset}

    public function update(Request $request, Aset $aset)
    {
        $this->authorize('update', $aset);

        // ✅ Validasi STRUKTUR jawaban per item
        $validated = $request->validate([
            'jawaban'                 => 'required|array',
            'jawaban.*.jawaban'       => 'required|in:A,B,C',
            'jawaban.*.keterangan'    => 'nullable|string',
        ]);

        // 🧮 Hitung skor (A=5, B=2, C=1)
        $map   = ['A' => 5, 'B' => 2, 'C' => 1];
        $jawab = collect($validated['jawaban'])
            ->map(fn($r) => [
                'jawaban'    => $r['jawaban'],
                'keterangan' => $r['keterangan'] ?? null,
            ])
            ->all(); // tanpa ->values()


        $skor = collect($jawab)->sum(fn($r) => $map[$r['jawaban']] ?? 0);

        try {
            // 🎯 1 aset ↔ 1 kategoriSe
            $existing = $aset->kategoriSe()->first();

            if ($existing) {
                $existing->update([
                    'jawaban'    => $jawab,
                    'skor_total' => $skor,
                ]);
            } else {
                $aset->kategoriSe()->create([
                    'uuid'       => (string) Str::uuid(), // ← pasti terisi saat pertama kali
                    'jawaban'    => $jawab,
                    'skor_total' => $skor,
                ]);
            }

            $allowed = ['tinggi', 'sedang', 'rendah', 'belum'];

            // Opsi A: balik ke filter yang sedang dipilih user
            $target = strtolower($request->input('kategori', 'belum'));
            if (!in_array($target, $allowed, true)) {
                $target = 'belum';
            }
            return redirect()
                ->route('opd.kategorise.show_by_kategori', ['kategori' => $target])
                ->with('success', 'Penilaian Kategori SE berhasil disimpan.');
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
}
