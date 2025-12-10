<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\KategoriSe;
use App\Models\RangeSe;
use App\Models\IndikatorKategoriSe;
use PDF;
use App\Services\PdfFooter;
use App\Models\Periode;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class BidangKategoriSeController extends Controller
{
    public function index()
    {
        $namaOpd = 'SEMUA OPD';

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $asetPL = Aset::whereHas('subklasifikasiaset', function ($q) {
            $q->whereIn('subklasifikasiaset', [
                'Aplikasi berbasis Website',
                'Aplikasi berbasis Mobile',
                'Aplikasi berbasis Desktop'
            ]);
        })
            ->where('periode_id', $periodeAktifId)
            ->with(['kategoriSe', 'subklasifikasiaset'])
            ->get();

        $rangeSes = RangeSe::all();

        $norm = fn($v) => strtoupper(trim((string) $v));
        $kategoriMeta = collect(['STRATEGIS', 'TINGGI', 'RENDAH'])->mapWithKeys(function ($K) use ($rangeSes, $norm) {
            $row = $rangeSes->first(fn($r) => $norm($r->nilai_akhir_aset) === $K);
            return [
                $K => [
                    'label'     => $K,
                    'deskripsi' => $row->deskripsi ?? '-',
                ],
            ];
        })->toArray();
        $kategoriMeta['BELUM'] = [
            'label'     => 'BELUM DINILAI',
            'deskripsi' => 'Belum ada skor (belum dilakukan penilaian).',
        ];
        $kategoriMeta['TOTAL'] = [
            'label'     => 'TOTAL',
            'deskripsi' => 'Jumlah seluruh aset perangkat lunak pada periode aktif.',
        ];

        $kategoriCount = [
            'STRATEGIS' => 0,
            'TINGGI'    => 0,
            'RENDAH'    => 0,
            'BELUM'     => 0,
            'TOTAL'     => $asetPL->count(),
        ];

        foreach ($asetPL as $aset) {
            $skor = $aset->kategoriSe->skor_total ?? null;

            if ($skor === null) {
                $kategoriCount['BELUM']++;
                continue;
            }

            $kategori = $rangeSes->first(function ($range) use ($skor) {
                return $skor >= $range->nilai_bawah && $skor <= $range->nilai_atas;
            });

            if ($kategori) {
                $namaKategori = strtoupper($kategori->nilai_akhir_aset);
                if (isset($kategoriCount[$namaKategori])) {
                    $kategoriCount[$namaKategori]++;
                } else {
                    $kategoriCount[$namaKategori] = 1;
                }
            }
        }

        return view('bidang.kategorise.index', [
            'strategis'    => $kategoriCount['STRATEGIS'] ?? 0,
            'tinggi'       => $kategoriCount['TINGGI'] ?? 0,
            'rendah'       => $kategoriCount['RENDAH'] ?? 0,
            'belum'        => $kategoriCount['BELUM'],
            'total'        => $kategoriCount['TOTAL'],
            'namaOpd'      => $namaOpd,
            'kategoriMeta' => $kategoriMeta,
            'rangeSes'     => $rangeSes,
        ]);
    }


    public function show($kategori)
    {
        $namaOpd = 'SEMUA OPD';
        $kategori = strtolower($kategori);

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $range = null;
        if (in_array($kategori, ['strategis', 'tinggi', 'rendah'])) {
            $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [$kategori])->first();
        }

        $query = Aset::query()
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
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id',
                'opd'
            ]);

        if ($range) {
            $query->whereHas('kategoriSe', function ($q) use ($range) {
                $q->where('skor_total', '>=', $range->nilai_bawah)
                    ->where('skor_total', '<=', $range->nilai_atas);
            });
        } elseif ($kategori === 'belum') {
            $query->doesntHave('kategoriSe');
        }

        $data = $query->get();
        $rangeSes = RangeSe::all();

        return view('bidang.kategorise.list', compact('data', 'kategori', 'namaOpd', 'rangeSes'));
    }


    public function exportRekapPdf()
    {
        $namaOpd = 'PEMERINTAH PROVINSI BALI';

        $data = Aset::query()
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
                'opd:id,namaopd',
            ])
            ->get();

        $rangeSes = RangeSe::all();

        $orderKategori = [
            'STRATEGIS'     => 1,
            'TINGGI'        => 2,
            'RENDAH'        => 3,
            'Belum Dinilai' => 4,
        ];

        $sorted = $data->sort(function ($a, $b) use ($rangeSes, $orderKategori) {
            $katA = $a->kategoriSe
                ? $rangeSes->first(function ($r) use ($a) {
                    return $a->kategoriSe->skor_total >= $r->nilai_bawah &&
                        $a->kategoriSe->skor_total <= $r->nilai_atas;
                })->nilai_akhir_aset ?? 'Belum Dinilai'
                : 'Belum Dinilai';

            $katB = $b->kategoriSe
                ? $rangeSes->first(function ($r) use ($b) {
                    return $b->kategoriSe->skor_total >= $r->nilai_bawah &&
                        $b->kategoriSe->skor_total <= $r->nilai_atas;
                })->nilai_akhir_aset ?? 'Belum Dinilai'
                : 'Belum Dinilai';

            $cmp = $orderKategori[$katA] <=> $orderKategori[$katB];
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp($a->nama_aset, $b->nama_aset);
        });

        $data = $sorted->values();

        $pdf = PDF::loadView('bidang.kategorise.export_rekap_pdf', compact('data', 'namaOpd', 'rangeSes'))
            ->setPaper('A4', 'landscape');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('rekap_kategorise_' . date('Ymd_His') . '.pdf');
    }

    public function exportRekapKategoriPdf($kategori)
    {
        $namaOpd = 'PEMERINTAH PROVINSI BALI';

        $kategori = strtolower($kategori);
        $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [$kategori])->first();

        $query = Aset::query()
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
                'opd:id,namaopd',
            ]);

        if ($range) {
            $query->whereHas('kategoriSe', function ($q) use ($range) {
                $q->where('skor_total', '>=', $range->nilai_bawah)
                    ->where('skor_total', '<=', $range->nilai_atas);
            });
        } elseif ($kategori === 'belum') {
            $query->doesntHave('kategoriSe');
        }

        $data = $query->get();
        $rangeSes = RangeSe::all();

        $pdf = PDF::loadView('bidang.kategorise.export_rekap_kategori_pdf', compact('data', 'kategori', 'namaOpd', 'rangeSes'))
            ->setPaper('A4', 'landscape');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('kategorisepernilai_' . date('Ymd_His') . '.pdf');
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
        $deskripsiLabel = $range->deskripsi ?? '-';
        $warna = $range->warna_hexa ?? '#888';

        $pdf = PDF::loadView('bidang.kategorise.pdf_detail', compact(
            'aset',
            'kategoriSe',
            'indikators',
            'kategoriLabel',
            'deskripsiLabel',
            'warna',
            'skor'
        ))
            ->setPaper('A4', 'potrait');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('penilaiankategorise_' . date('Ymd_His') . '.pdf');
    }


    public function destroy($id)
    {
        $aset = Aset::with('kategoriSe')->findOrFail($id);

        if (!$aset->kategoriSe) {
            return back()->with('error', 'Tidak ada data kategori SE untuk aset ini.');
        }

        try {
            $aset->kategoriSe->delete();

            return back()->with('success', 'Kategori SE berhasil dihapus.');
        } catch (QueryException $e) {
            Log::warning('Bidang gagal menghapus kategori SE', [
                'aset_id'    => $aset->id,
                'mysql_code' => $e->errorInfo[1] ?? null,
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'route'      => request()->path(),
            ]);

            return back()->with('error', 'Gagal menghapus kategori SE. Silakan coba lagi.');
        }
    }
}
