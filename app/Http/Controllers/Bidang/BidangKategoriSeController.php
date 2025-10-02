<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;

use App\Models\Aset;
//use App\Models\KategoriSe;
use App\Models\RangeSe;
use App\Models\IndikatorKategoriSe;
//use Illuminate\Http\Request;
use PDF;
use App\Models\Periode;

// use App\Models\KlasifikasiAset;
// use App\Models\Periode;

class BidangKategoriSeController extends Controller
{
    public function index()
    {
        // Label cakupan data
        $namaOpd = 'SEMUA OPD';

        // Ambil semua aset dengan klasifikasi PERANGKAT LUNAK dari seluruh OPD
        // $asetPL = Aset::whereHas('klasifikasi', function ($q) {
        //     $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        // })
        //     ->with('kategoriSe') // agar skor_total tersedia tanpa N+1
        //     ->get();

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
    ->where('periode_id', $periodeAktifId)   // filter periode aktif
    ->with(['kategoriSe', 'subklasifikasiaset']) // sekalian eager load subklas
    ->get();



        // Ambil semua range kategori SE
        $rangeSes = RangeSe::all();

        // Inisialisasi counter
        $kategoriCount = [
            'TINGGI' => 0,
            'SEDANG' => 0,
            'RENDAH' => 0,
            'BELUM'  => 0,
            'TOTAL'  => $asetPL->count(),
        ];

        foreach ($asetPL as $aset) {
            $skor = $aset->kategoriSe->skor_total ?? null;

            if ($skor === null) {
                $kategoriCount['BELUM']++;
                continue;
            }

            // Tentukan kategori berdasarkan range
            $range = $rangeSes->first(function ($r) use ($skor) {
                return $skor >= $r->nilai_bawah && $skor <= $r->nilai_atas;
            });

            if ($range) {
                $namaKategori = strtoupper($range->nilai_akhir_aset); // TINGGI/SEDANG/RENDAH
                if (isset($kategoriCount[$namaKategori])) {
                    $kategoriCount[$namaKategori]++;
                } else {
                    $kategoriCount[$namaKategori] = 1;
                }
            } else {
                // Jika skor tidak jatuh ke range manapun, anggap BELUM
                $kategoriCount['BELUM']++;
            }
        }

        return view('bidang.kategorise.index', [
            'tinggi'  => $kategoriCount['TINGGI'] ?? 0,
            'sedang'  => $kategoriCount['SEDANG'] ?? 0,
            'rendah'  => $kategoriCount['RENDAH'] ?? 0,
            'belum'   => $kategoriCount['BELUM'],
            'total'   => $kategoriCount['TOTAL'],
            'namaOpd' => $namaOpd,
        ]);
    }


    public function show($kategori)
    {
        $namaOpd = 'SEMUA OPD';
        $kategori = strtolower($kategori); // 'tinggi' | 'sedang' | 'rendah' | 'belum' | 'total'
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (! $periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }
        $range = null;
        if (in_array($kategori, ['tinggi', 'sedang', 'rendah'])) {
            $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [$kategori])->first();
        }

        // $query = Aset::whereHas('klasifikasi', function ($q) {
        //     $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        // })
        //     ->with(['subklasifikasiaset', 'kategoriSe', 'opd']); // tambah opd jika ingin ditampilkan di view


   $query = Aset::query()
        ->where('periode_id', $periodeAktifId)
        ->whereHas('subklasifikasiaset', function ($q) {
            $q->whereIn('subklasifikasiaset', [
                'Aplikasi berbasis Website',
                'Aplikasi berbasis Mobile',
            ]);
        })
        ->with([
            'kategoriSe:id,aset_id,skor_total,jawaban',
            'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id','opd'
        ]);



        if ($range) {
            $query->whereHas('kategoriSe', function ($q) use ($range) {
                $q->where('skor_total', '>=', $range->nilai_bawah)
                    ->where('skor_total', '<=', $range->nilai_atas);
            });
        } elseif ($kategori === 'belum') {
            $query->doesntHave('kategoriSe');
        } else {
            // 'total' atau kategori tak dikenal → tampilkan semua aset PL (tanpa filter kategori)
            // tidak perlu ubah query
        }
        $data = $query->get();
        $rangeSes = RangeSe::all();
        return view('bidang.kategorise.list', compact('data', 'kategori', 'namaOpd', 'rangeSes'));
    }


    public function exportRekapPdf()
    {
        // Label cakupan data
        $namaOpd = 'SEMUA OPD';

        // Ambil semua aset PERANGKAT LUNAK dari seluruh OPD
        $asetPL = Aset::whereHas('klasifikasi', function ($q) {
            $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        })
            ->with(['kategoriSe:id,aset_id,skor_total']) // ambil skor_total saja biar ringan
            ->get(['id']); // kolom minimal

        // Ambil range kategori dari DB (TINGGI/SEDANG/RENDAH)
        $ranges = RangeSe::all()->keyBy(function ($r) {
            return strtoupper($r->nilai_akhir_aset); // TINGGI/SEDANG/RENDAH
        });

        // Helper untuk cek skor masuk range
        $inRange = function (?int $skor, $range) {
            if ($skor === null || !$range) return false;
            return $skor >= $range->nilai_bawah && $skor <= $range->nilai_atas;
        };

        // Hitung kategori
        $tinggi = $asetPL->filter(function ($a) use ($ranges, $inRange) {
            $skor = $a->kategoriSe->skor_total ?? null;
            return $inRange($skor, $ranges['TINGGI'] ?? null);
        })->count();

        $sedang = $asetPL->filter(function ($a) use ($ranges, $inRange) {
            $skor = $a->kategoriSe->skor_total ?? null;
            return $inRange($skor, $ranges['SEDANG'] ?? null);
        })->count();

        $rendah = $asetPL->filter(function ($a) use ($ranges, $inRange) {
            $skor = $a->kategoriSe->skor_total ?? null;
            return $inRange($skor, $ranges['RENDAH'] ?? null);
        })->count();

        $belum = $asetPL->filter(fn($a) => is_null($a->kategoriSe))->count();
        $total = $asetPL->count();

        // Buat PDF (A4 dalam points) + footer style "render → page_script"
        $pdf = PDF::loadView('bidang.kategorise.export_rekap_pdf', compact(
            'tinggi',
            'sedang',
            'rendah',
            'belum',
            'total',
            'namaOpd'
        ))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');

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

        return $pdf->download('kategorisepemprovbali_' . date('Ymd_His') . '.pdf');
    }

    public function exportRekapKategoriPdf($kategori)
    {
        // Label cakupan data
        $namaOpd = 'SEMUA OPD';

        // Normalisasi kategori
        $kategori = strtolower($kategori); // 'tinggi' | 'sedang' | 'rendah' | 'belum' | 'total'

        // (Opsional) Batasi ke Periode aktif
        // $periodeAktifId = Periode::where('status', 'open')->value('id');

        // Ambil range dari DB untuk kategori selain 'belum'/'total'
        $range = null;
        if (in_array($kategori, ['tinggi', 'sedang', 'rendah'])) {
            $range = RangeSe::whereRaw('LOWER(nilai_akhir_aset) = ?', [$kategori])->first();
        }

        // Query aset PERANGKAT LUNAK dari seluruh OPD
        // $query = Aset::whereHas('klasifikasi', function ($q) {
        //     $q->where('klasifikasiaset', 'PERANGKAT LUNAK');
        // })
        //     // ->where('periode_id', $periodeAktifId) // aktifkan jika perlu filter periode
        //     ->with(['subklasifikasiaset', 'kategoriSe', 'opd']);

          $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (! $periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

$query = Aset::query()
    ->where('periode_id', $periodeAktifId) // aktifkan jika perlu filter periode
    ->whereHas('subklasifikasiaset', function ($q) {
        $q->whereIn('subklasifikasiaset', [
            'Aplikasi berbasis Website',
            'Aplikasi berbasis Mobile',
        ]);
    })
    ->with([
        'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id',
        'kategoriSe:id,aset_id,skor_total,jawaban','opd'
    ]);



        if ($range) {
            // Filter berdasarkan skor_total sesuai range kategori
            $query->whereHas('kategoriSe', function ($q) use ($range) {
                $q->where('skor_total', '>=', $range->nilai_bawah)
                    ->where('skor_total', '<=', $range->nilai_atas);
            });
        } elseif ($kategori === 'belum') {
            // Belum dinilai: tidak punya relasi kategoriSe
            $query->doesntHave('kategoriSe');
        } else {
            // 'total' atau kategori tidak dikenal → tampilkan semua tanpa filter tambahan
        }

        $data = $query->get();
        $rangeSes = RangeSe::all();

        // Buat PDF: A4 (points) + footer via render → page_script
        $pdf = PDF::loadView('bidang.kategorise.export_rekap_kategori_pdf', compact('data', 'kategori', 'namaOpd', 'rangeSes'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');

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
        return $pdf->download('kategorisepernilai_' . date('Ymd_His') . '.pdf');
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


        $pdf = PDF::loadView('bidang.kategorise.pdf_detail', compact(
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
            $x = ($width - $textWidth) / 2;
            $y = $height - 30;
            $canvas->text($x, $y, $text, $font, $size);
        });
        return $pdf->download('penilaiankategorise_' . date('Ymd_His') . '.pdf');
    }
}
