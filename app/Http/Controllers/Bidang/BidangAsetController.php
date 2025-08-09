<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;


use App\Models\Aset;
use App\Models\RangeAset;
use App\Models\KlasifikasiAset;
use App\Models\Periode;
use App\Models\SubKlasifikasiAset;
use Barryvdh\DomPDF\Facade\Pdf;

class BidangAsetController extends Controller
{
    public function index()
    {
        // 1) Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // fallback kalau belum ada periode open
            return view('bidang.aset.index', [
                'klasifikasis' => collect(),
                'namaOpd'      => 'SEMUA OPD',
                'totalTinggi'  => 0,
                'totalSedang'  => 0,
                'totalRendah'  => 0,
            ]);
        }

        // 2) Hitung jumlah aset per klasifikasi untuk SEMUA OPD (dibatasi periode aktif saja)
        $klasifikasis = KlasifikasiAset::withCount([
            'asets as jumlah_aset' => function ($query) use ($periodeAktifId) {
                $query->where('periode_id', $periodeAktifId);
            }
        ])->get();

        // 3) Siapkan total global
        $totalTinggi = 0;
        $totalSedang = 0;
        $totalRendah = 0;

        // 4) Loop tiap klasifikasi → ambil aset (semua OPD) di periode aktif
        foreach ($klasifikasis as $klasifikasi) {
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('periode_id', $periodeAktifId)
                ->get(['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan']); // hemat kolom

            $jumlahTinggi = 0;
            $jumlahSedang = 0;
            $jumlahRendah = 0;

            foreach ($asets as $aset) {
                $total = (int)$aset->kerahasiaan
                    + (int)$aset->integritas
                    + (int)$aset->ketersediaan
                    + (int)$aset->keaslian
                    + (int)$aset->kenirsangkalan;

                $range = RangeAset::where('nilai_bawah', '<=', $total)
                    ->where('nilai_atas', '>=', $total)
                    ->first();

                $nilai = $range->nilai_akhir_aset ?? null;

                if ($nilai === 'TINGGI') {
                    $jumlahTinggi++;
                } elseif ($nilai === 'SEDANG') {
                    $jumlahSedang++;
                } elseif ($nilai === 'RENDAH') {
                    $jumlahRendah++;
                }
            }

            // simpan ke objek klasifikasi untuk dipakai di view
            $klasifikasi->jumlah_tinggi = $jumlahTinggi;
            $klasifikasi->jumlah_sedang = $jumlahSedang;
            $klasifikasi->jumlah_rendah = $jumlahRendah;

            // akumulasi global
            $totalTinggi += $jumlahTinggi;
            $totalSedang += $jumlahSedang;
            $totalRendah += $jumlahRendah;
        }

        // 5) Karena ini agregat lintas OPD:
        $namaOpd = 'SEMUA OPD';

        return view('bidang.aset.index', compact(
            'klasifikasis',
            'namaOpd',
            'totalTinggi',
            'totalSedang',
            'totalRendah',
        ));
    }



    public function showByKlasifikasi($id)
    {
        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        abort_unless($periodeAktifId, 404, 'Periode aktif tidak ditemukan');

        // Pastikan klasifikasi ada
        $klasifikasi = KlasifikasiAset::findOrFail($id);

        // Ambil aset untuk SEMUA OPD pada periode aktif, sekaligus relasi subklasifikasi
        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('periode_id', $periodeAktifId)
            ->with('subklasifikasiaset')
            ->get(['id', 'kode_aset', 'nama_aset', 'subklasifikasiaset_id', 'kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan']);

        // Ambil range sekali (hemat query)
        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        foreach ($asets as $aset) {
            // Total nilai keamanan informasi
            $total = (int)$aset->kerahasiaan
                + (int)$aset->integritas
                + (int)$aset->ketersediaan
                + (int)$aset->keaslian
                + (int)$aset->kenirsangkalan;

            // Tentukan range di memori
            $range = $ranges->first(function ($r) use ($total) {
                return $r->nilai_bawah <= $total && $r->nilai_atas >= $total;
            });

            $aset->nilai_akhir_aset = $range->nilai_akhir_aset ?? '-';
            $aset->warna_hexa       = $range->warna_hexa ?? '#999999'; // default abu-abu
        }

        // Karena lintas OPD, labelnya diset generik
        $namaOpd = 'SEMUA OPD';
        return view('bidang.aset.show_by_klasifikasi', compact('klasifikasi', 'asets', 'namaOpd'));
    }

    public function exportRekapPdf()
    {
        // Periode aktif wajib ada
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // Boleh ganti ke redirect/flash sesuai selera
            $klasifikasis = collect();
            $namaOpd = 'SEMUA OPD';
            $pdf = PDF::loadView('bidang.aset.export_rekap_pdf', compact('klasifikasis', 'namaOpd'))
                ->setPaper('A4', 'portrait');
            return $pdf->download('rekap_aset_' . date('Ymd_His') . '.pdf');
        }

        // Hitung jumlah aset per klasifikasi untuk SEMUA OPD (dibatasi periode aktif)
        $klasifikasis = KlasifikasiAset::withCount([
            'asets as jumlah_aset' => function ($query) use ($periodeAktifId) {
                $query->where('periode_id', $periodeAktifId);
            }
        ])->get();

        // Ambil range sekali (diasumsikan jumlah range sedikit)
        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        foreach ($klasifikasis as $klasifikasi) {
            // Ambil aset di klasifikasi ini untuk SEMUA OPD pada periode aktif
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('periode_id', $periodeAktifId)
                ->get(['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan']);

            $jumlahTinggi = 0;
            $jumlahSedang = 0;
            $jumlahRendah = 0;

            foreach ($asets as $aset) {
                $total = (int)$aset->kerahasiaan
                    + (int)$aset->integritas
                    + (int)$aset->ketersediaan
                    + (int)$aset->keaslian
                    + (int)$aset->kenirsangkalan;

                // Match range di memori
                $range = $ranges->first(function ($r) use ($total) {
                    return $r->nilai_bawah <= $total && $r->nilai_atas >= $total;
                });

                $nilai = $range->nilai_akhir_aset ?? null;

                if ($nilai === 'TINGGI')      $jumlahTinggi++;
                elseif ($nilai === 'SEDANG')  $jumlahSedang++;
                elseif ($nilai === 'RENDAH')  $jumlahRendah++;
            }

            $klasifikasi->jumlah_tinggi = $jumlahTinggi;
            $klasifikasi->jumlah_sedang = $jumlahSedang;
            $klasifikasi->jumlah_rendah = $jumlahRendah;
        }

        // Karena lintas OPD
        $namaOpd = 'SEMUA OPD';

        $pdf = PDF::loadView('bidang.aset.export_rekap_pdf', compact('klasifikasis', 'namaOpd'))
            ->setPaper('A4', 'portrait');

        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI :: PERSANDIAN DISKOMINFOS PROV BALI";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = $height - 30;
            $canvas->text($x, $y, $text, $font, $size);
        });
        return $pdf->download('asettikpemprovbali_' . date('Ymd_His') . '.pdf');
    }


    public function exportRekapKlasPdf($id)
    {
        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            abort(404, 'Periode aktif tidak ditemukan');
        }

        // Pastikan klasifikasi ada
        $klasifikasi = KlasifikasiAset::findOrFail($id);

        // Ambil semua aset pada klasifikasi ini & periode aktif (tanpa filter OPD)
        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('periode_id', $periodeAktifId)
            ->with('subklasifikasiaset') // tambah relasi lain kalau perlu di view
            ->get([
                'id',
                'kode_aset',
                'nama_aset',
                'subklasifikasiaset_id',
                'kerahasiaan',
                'integritas',
                'ketersediaan',
                'keaslian',
                'kenirsangkalan'
            ]);

        // Ambil range sekali untuk hemat query
        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        foreach ($asets as $aset) {
            $total = (int)$aset->kerahasiaan
                + (int)$aset->integritas
                + (int)$aset->ketersediaan
                + (int)$aset->keaslian
                + (int)$aset->kenirsangkalan;

            $match = $ranges->first(fn($r) => $r->nilai_bawah <= $total && $r->nilai_atas >= $total);

            $aset->nilai_akhir_aset = $match->nilai_akhir_aset ?? '-';
            $aset->warna_hexa       = $match->warna_hexa ?? '#999999';
        }

        // Karena semua OPD
        $namaOpd = 'SEMUA OPD';

        // Buat PDF
        $pdf = PDF::loadView('bidang.aset.export_rekap_klas_pdf', compact('klasifikasi', 'asets', 'namaOpd'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait'); // A4 dalam points

        // Render dulu biar page count tersedia
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Footer
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = 820; // posisi bawah halaman A4 portrait
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('asettikperklas_' . date('Ymd_His') . '.pdf');
    }



    public function pdf($id)
    {
        // Ambil aset apa pun OPD-nya + relasi yang dibutuhkan
        $aset = Aset::with(['klasifikasi', 'subklasifikasiaset', 'opd'])->findOrFail($id);

        $klasifikasi = $aset->klasifikasi;
        $subklasifikasis = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasi->id)->get();

        // Ambil daftar field tampilan per klasifikasi
        $fieldList = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : (json_decode($klasifikasi->tampilan_field_aset, true) ?? []);

        // Sembunyikan field teknis
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id',  'kategori_se'];
        $fieldList = array_values(array_diff($fieldList, $hiddenFields));

        // Hitung nilai keamanan informasi
        $total = (int)$aset->kerahasiaan
            + (int)$aset->integritas
            + (int)$aset->ketersediaan
            + (int)$aset->keaslian
            + (int)$aset->kenirsangkalan;

        $range = RangeAset::where('nilai_bawah', '<=', $total)
            ->where('nilai_atas', '>=', $total)
            ->first();

        $aset->nilai_akhir_aset = $range->nilai_akhir_aset ?? '-';
        $aset->warna_hexa       = $range->warna_hexa ?? '#999999';

        // Nama OPD pemilik aset (bukan OPD user login)
        $namaOpd = $aset->opd->namaopd ?? '—';

        // Buat PDF + footer (render → page_script)
        $pdf = PDF::loadView('bidang.aset.pdf', compact('aset', 'namaOpd', 'klasifikasi', 'fieldList', 'subklasifikasis'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait'); // A4 in points

        $dompdf = $pdf->getDomPDF();
        $dompdf->render(); // penting: render dulu untuk dapat page count

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            //$text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $text = "PERISAI :: PERSANDIAN DISKOMINFOS PROV BALI";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = 820; // posisi bawah A4 portrait
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('detilaset_' . date('Ymd_His') . '.pdf');
    }
}
