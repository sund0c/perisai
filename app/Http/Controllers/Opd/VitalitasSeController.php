<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Aset;
use App\Models\VitalitasSe;
use App\Models\RangeSe;
use App\Models\IndikatorVitalitasSe;
use PDF;
use App\Services\PdfFooter;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\QueryException;

use App\Models\KlasifikasiAset;
use App\Models\Periode;
use Illuminate\Support\Facades\Log;

class VitalitasSeController extends Controller
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

        $asetPL = Aset::whereHas('subklasifikasiaset', function ($q) {
            $q->whereIn('subklasifikasiaset', [
                'Aplikasi berbasis Website',
                'Aplikasi berbasis Mobile',
            ]);
        })
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId)   // filter periode aktif
            ->with(['vitalitasSe', 'subklasifikasiaset']) // sekalian eager load subklas
            ->get();


        // Inisialisasi penghitung kategori
        $kategoriCount = [
            'VITAL' => 0,
            'Tidak Vital' => 0,
            'BELUM' => 0,
            'TOTAL' => $asetPL->count()
        ];



        foreach ($asetPL as $aset) {
            $skor = $aset->vitalitasSe->skor_total ?? null;

            if ($skor === null) {
                $kategoriCount['BELUM']++;
                continue;
            }

            $namaKategori = ($skor >= 15) ? 'VITAL' : 'Tidak Vital';
            $kategoriCount[$namaKategori] = ($kategoriCount[$namaKategori] ?? 0) + 1;
        }


        return view('opd.vitalitasse.index', [
            'vital' => $kategoriCount['VITAL'] ?? 0,
            'novital' => $kategoriCount['Tidak Vital'] ?? 0,
            'belum' => $kategoriCount['BELUM'],
            'total' => $kategoriCount['TOTAL'],
            'namaOpd' => $namaOpd,
        ]);
    }

    public function exportRekapPdf()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // bebas: bisa redirect balik dengan flash message juga
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $asetPL = Aset::whereHas('subklasifikasiaset', function ($q) {
            $q->whereIn('subklasifikasiaset', [
                'Aplikasi berbasis Website',
                'Aplikasi berbasis Mobile',
            ]);
        })
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId)   // filter periode aktif
            ->with(['vitalitasSe', 'subklasifikasiaset']) // sekalian eager load subklas
            ->get();


        // Inisialisasi penghitung kategori
        $kategoriCount = [
            'VITAL' => 0,
            'Tidak Vital' => 0,
            'BELUM' => 0,
            'TOTAL' => $asetPL->count()
        ];

        foreach ($asetPL as $aset) {
            $skor = $aset->vitalitasSe->skor_total ?? null;

            if ($skor === null) {
                $kategoriCount['BELUM']++;
                continue;
            }

            $namaKategori = ($skor >= 15) ? 'VITAL' : 'Tidak Vital';
            $kategoriCount[$namaKategori] = ($kategoriCount[$namaKategori] ?? 0) + 1;
        }


        $vital = $kategoriCount['VITAL'];
        $novital = $kategoriCount['Tidak Vital'];
        $total = $kategoriCount['TOTAL'];
        $belum = $kategoriCount['BELUM'];

        $namaOpd = auth()->user()->opd->namaopd ?? '-';


        $pdf = PDF::loadView('opd.vitalitasse.export_rekap_pdf', compact('vital', 'novital', 'belum', 'total', 'namaOpd'))
            ->setPaper('A4', 'portrait');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('rekap_vitalitasse_' . date('Ymd_His') . '.pdf');
    }

    public function exportRekapKategoriPdf($kategori)
    {
        $allowed = ['vital', 'novital', 'belum']; // tambah 'total' kalau perlu
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
        $query = Aset::query()
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId)
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                ]);
            })
            ->with([
                'vitalitasSe:id,aset_id,skor_total,jawaban',
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id'
            ]);

        // filter kategori
        if ($kategori === 'belum') {
            $query->doesntHave('vitalitasSe');
        } else {
            $query->whereHas('vitalitasSe', function ($q) use ($kategori) {
                $q->whereNotNull('skor_total');

                if ($kategori === 'vital') {
                    // Skor >= 15
                    $q->where('skor_total', '>=', 15);
                } else {
                    // Semua skor di bawah 15 dianggap Tidak Vital
                    $q->where('skor_total', '<', 15);
                }
            });
        }

        $data = $query->orderBy('nama_aset')->get();
        $rangeSes = RangeSe::all();

        $pdf = PDF::loadView('opd.vitalitasse.export_rekap_kategori_pdf', compact('data', 'kategori', 'namaOpd'))
            ->setPaper('A4', 'potrait');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('vitalitasse_pernilai_' . date('Ymd_His') . '.pdf');
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
        $allowed = ['vital', 'novital', 'belum']; // tambah 'total' kalau perlu
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
        $query = Aset::query()
            ->where('opd_id', $userOpdId)
            ->where('periode_id', $periodeAktifId)
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                ]);
            })
            ->with([
                'vitalitasSe:id,aset_id,skor_total,jawaban',
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id'
            ]);


        // filter kategori
        if ($kategori === 'belum') {
            $query->doesntHave('vitalitasSe');
        } else {
            $query->whereHas('vitalitasSe', function ($q) use ($kategori) {
                $q->whereNotNull('skor_total');

                if ($kategori === 'vital') {
                    // Skor >= 15
                    $q->where('skor_total', '>=', 15);
                } else {
                    // Semua skor di bawah 15 dianggap Tidak Vital
                    $q->where('skor_total', '<', 15);
                }
            });
        }


        $data = $query->orderBy('nama_aset')->get();

        return view('opd.vitalitasse.list', compact('data', 'kategori', 'namaOpd'));
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
            'vitalitasSe', // hasOne
        ]);

        // cek hanya untuk klasifikasi "PERANGKAT LUNAK" jika memang wajib
        if (optional($aset->klasifikasi)->kodeklas !== 'PL') {
            abort(404);
        }

        $vitalitasSe = $aset->vitalitasSe;
        $indikators = IndikatorVitalitasSe::orderBy('urutan')->get();

        $skorRaw = $vitalitasSe->skor_total ?? null;

        if (is_null($skorRaw)) {
            $kategoriLabel = 'BELUM DINILAI';
            $warna = 'transparent'; // tanpa warna background
            $warnatext = '#000'; // tanpa warna background
        } else {
            $skor = (int) $skorRaw;

            if ($skor >= 15) {
                $kategoriLabel = 'VITAL';
                $warna = '#dc3545'; // merah
                $warnatext = '#FFF';
            } else {
                $kategoriLabel = 'Tidak Vital';
                $warna = '#28a745'; // hijau
                $warnatext = '#FFF';
            }
        }

        // 7) Render PDF
        $pdf = PDF::loadView('opd.vitalitasse.pdf_detail', compact(
            'aset',
            'vitalitasSe',
            'indikators',
            'kategoriLabel',
            'warna',
            'warnatext',
            'skor',
            'namaOpd',
        ))
            ->setPaper('a4', 'portrait');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('penilaian_kategori_se_' . now()->format('Ymd_His') . '.pdf');
    }


    // GET /opd/kategorise/{aset}/edit
    public function edit(Request $request, Aset $aset)
    {

        $this->authorize('update', $aset);

        // validasi query ?kategori= (opsional, kalau mau dipakai di view)
        $kategori = (string) $request->query('kategori', '');
        if ($kategori && !in_array($kategori, ['vital', 'novital', 'belum'], true)) {
            abort(404);
        }

        $indikators = IndikatorVitalitasSe::orderBy('urutan')->get();

        // lewat relasi (lebih aman dari N+1)
        $vitalitasSe = $aset->vitalitasSe()->first(); // bisa null jika belum pernah dinilai

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        return view('opd.vitalitasse.edit', compact(
            'aset',
            'indikators',
            'vitalitasSe',
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
            'jawaban.*.jawaban'       => 'required|in:A,B,C,D',
            'jawaban.*.keterangan'    => 'nullable|string',
        ]);

        $map   = ['A' => 15, 'B' => 5, 'C' => 1, 'D' => 0];
        $jawab = collect($validated['jawaban'])
            ->map(fn($r) => [
                'jawaban'    => $r['jawaban'],
                'keterangan' => $r['keterangan'] ?? null,
            ])
            ->all(); // tanpa ->values()


        $skor = collect($jawab)->sum(fn($r) => $map[$r['jawaban']] ?? 0);

        try {
            // 🎯 1 aset ↔ 1 kategoriSe
            $existing = $aset->vitalitasSe()->first();

            if ($existing) {
                $existing->update([
                    'jawaban'    => $jawab,
                    'skor_total' => $skor,
                ]);
            } else {
                $aset->vitalitasSe()->create([
                    'uuid'       => (string) Str::uuid(), // ← pasti terisi saat pertama kali
                    'jawaban'    => $jawab,
                    'skor_total' => $skor,
                ]);
            }

            $allowed = ['vital', 'novital', 'belum'];

            // Opsi A: balik ke filter yang sedang dipilih user
            $target = strtolower($request->input('kategori', 'belum'));
            if (!in_array($target, $allowed, true)) {
                $target = 'belum';
            }
            return redirect()
                ->route('opd.vitalitasse.show_by_kategori', ['kategori' => $target])
                ->with('success', 'Penilaian Vitalitas SE berhasil disimpan.');
        } catch (QueryException $e) {
            Log::warning('VitalitasSe update QueryException', [
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
