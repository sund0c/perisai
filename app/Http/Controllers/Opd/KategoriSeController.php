<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;

use App\Models\Aset;
use App\Models\KategoriSe;
use App\Models\RangeSe;
use App\Models\IndikatorKategoriSe;
use Illuminate\Http\Request;
use PDF;

use App\Models\KlasifikasiAset;
use App\Models\Periode;

class KategoriSeController extends Controller
{
    public function index()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // Ambil semua aset perangkat lunak milik OPD login
        $asetPL = Aset::whereHas('klasifikasi', function ($q) {
            $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        })
            ->where('opd_id', $userOpdId)
            ->with('kategoriSe')
            ->get();

        // Ambil semua range kategori dari tabel range_ses
        $rangeSes = RangeSe::all();

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
        ]);
    }

    public function show($kategori)
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

        // Ambil semua aset perangkat lunak milik OPD login
        $asetPL = \App\Models\Aset::whereHas('klasifikasi', function ($q) {
            $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        })
            ->where('opd_id', $userOpdId)
            ->with('kategoriSe')
            ->get();

        // Hitung kategori
        $tinggi = $asetPL->where('opd.kategorise.skor_total', '>=', 16)->count();
        $sedang = $asetPL->whereBetween('opd.kategorise.skor_total', [8, 15])->count();
        $rendah = $asetPL->where('opd.kategorise.skor_total', '<', 8)->filter(function ($a) {
            return $a->kategoriSe !== null;
        })->count();
        $belum = $asetPL->whereNull('kategoriSe')->count();
        $total = $asetPL->count();

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.kategorise.export_rekap_pdf', compact('tinggi', 'sedang', 'rendah', 'belum', 'total', 'namaOpd'))
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

        KategoriSe::updateOrCreate(
            ['aset_id' => $asetId],
            [
                'jawaban' => $inputJawaban,
                'skor_total' => $skorTotal
            ]
        );

        return redirect()->route('opd.kategorise.index')->with('success', 'Penilaian berhasil disimpan.');
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
