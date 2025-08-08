<?php

namespace App\Http\Controllers;

use App\Models\StandarKategori;
use App\Models\RekomendasiStandard;
use App\Models\StandarIndikator;
use App\Models\FungsiStandar;
use Illuminate\Http\Request;
use PDF;


class SkController extends Controller
{
    // Menampilkan semua kategori
    public function indexKategori()
    {
        $kategoris = StandarKategori::all();
        return view('admin.sk.kategori_index', compact('kategoris'));
    }

    public function kategoriPDF()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $kategoris = StandarKategori::all();

        $pdf = PDF::loadView('admin.sk.kategoripdf', compact('kategoris', 'namaOpd'))
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

        return $pdf->download('kategoripdf' . date('Ymd_His') . '.pdf');
    }

    // Form tambah
    public function createKategori()
    {
        return view('admin.sk.kategori_create');
    }

    // Simpan kategori baru
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        StandarKategori::create($request->only('nama'));
        return redirect()->route('sk.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Form edit
    public function editKategori($id)
    {
        $kategori = StandarKategori::findOrFail($id);
        return view('admin.sk.kategori_edit', compact('kategori'));
    }

    // Update
    public function updateKategori(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $kategori = StandarKategori::findOrFail($id);
        $kategori->update($request->only('nama'));
        return redirect()->route('sk.index')->with('success', 'Kategori berhasil diupdate.');
    }

    // Hapus
    public function destroyKategori($id)
    {
        $kategori = StandarKategori::findOrFail($id);
        $kategori->delete();
        return redirect()->route('sk.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function indexFungsi($kategoriId)
    {
        $kategori = StandarKategori::with('fungsi')->findOrFail($kategoriId);
        $fungsiStandar = $kategori->fungsi;

        return view('admin.sk.fungsi_index', compact('kategori', 'fungsiStandar'));
    }

    public function fungsiPDF($kategoriId)
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $kategori = StandarKategori::with('fungsi')->findOrFail($kategoriId);
        $fungsiStandar = $kategori->fungsi;

        $pdf = PDF::loadView('admin.sk.fungsipdf', compact('namaOpd', 'kategori', 'fungsiStandar'))
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

        return $pdf->download('fungsipdf' . date('Ymd_His') . '.pdf');
    }

    public function createFungsi($kategoriId)
    {
        $kategori = StandarKategori::findOrFail($kategoriId);
        return view('admin.sk.fungsi_create', compact('kategori'));
    }
    public function storeFungsi(Request $request, $kategoriId)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer',
        ]);

        FungsiStandar::create([
            'kategori_id' => $kategoriId,
            'nama' => $request->nama,
            'urutan' => $request->urutan
        ]);

        return redirect()->route('sk.fungsistandar.index', $kategoriId)->with('success', 'Fungsi berhasil ditambahkan');
    }
    public function editFungsi($id)
    {
        $fungsi = FungsiStandar::findOrFail($id);
        return view('admin.sk.fungsi_edit', compact('fungsi'));
    }
    public function updateFungsi(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer',
        ]);

        $fungsi = FungsiStandar::findOrFail($id);
        $fungsi->update([
            'nama' => $request->nama,
            'urutan' => $request->urutan
        ]);

        return redirect()->route('sk.fungsistandar.index', $fungsi->kategori_id)->with('success', 'Fungsi berhasil diperbarui');
    }
    public function destroyFungsi($id)
    {
        $fungsi = FungsiStandar::findOrFail($id);
        $kategoriId = $fungsi->kategori_id;
        $fungsi->delete();

        return redirect()->route('sk.fungsistandar.index', $kategoriId)->with('success', 'Fungsi berhasil dihapus');
    }

    public function indexIndikator($fungsi)
    {
        $fungsiStandar = FungsiStandar::findOrFail($fungsi);
        //$indikators = $fungsiStandar->indikator()->get();
        $indikators = StandarIndikator::where('fungsi_standar_id', $fungsi)
            ->orderBy('urutan')
            ->get();


        return view('admin.sk.indikator_index', compact('fungsiStandar', 'indikators'));
    }

    public function indikatorPDF($fungsi)
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // $fungsiStandar = FungsiStandar::findOrFail($fungsi);
        $indikators = StandarIndikator::where('fungsi_standar_id', $fungsi)
            ->orderBy('urutan')
            ->get();
        $fungsiStandar = FungsiStandar::with('kategori')->findOrFail($fungsi);


        $pdf = PDF::loadView('admin.sk.indikatorpdf', compact('namaOpd', 'indikators', 'fungsiStandar'))
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

        return $pdf->download('indikatorpdf' . date('Ymd_His') . '.pdf');
    }

    public function createIndikator($fungsi)
    {
        $fungsiStandar = FungsiStandar::findOrFail($fungsi);
        return view('admin.sk.indikator_create', compact('fungsiStandar'));
    }
    public function storeIndikator(Request $request, $fungsi)
    {
        $fungsiStandar = FungsiStandar::findOrFail($fungsi);

        $request->validate([
            'indikator' => 'required|string',
            'tujuan' => 'nullable|string',
            'urutan' => 'required|integer',
        ]);

        $fungsiStandar->indikator()->create([
            'indikator' => $request->indikator,
            'tujuan' => $request->tujuan,
            'urutan' => $request->urutan,
        ]);

        return redirect()->route('sk.indikator.index', $fungsi)
            ->with('success', 'Indikator berhasil ditambahkan.');
    }
    public function editIndikator($id)
    {
        $indikator = StandarIndikator::with('fungsiStandar')->findOrFail($id);
        return view('admin.sk.indikator_edit', compact('indikator'));
    }
    public function updateIndikator(Request $request, $id)
    {
        $indikator = StandarIndikator::findOrFail($id);

        $request->validate([
            'indikator' => 'required|string',
            'tujuan' => 'nullable|string',
            'urutan' => 'required|integer',
        ]);

        $indikator->update([
            'indikator' => $request->indikator,
            'tujuan' => $request->tujuan,
            'urutan' => $request->urutan,
        ]);

        return redirect()->route('sk.indikator.index', $indikator->fungsi_standar_id)
            ->with('success', 'Indikator berhasil diperbarui.');
    }
    public function destroyIndikator($id)
    {
        $indikator = StandarIndikator::findOrFail($id);
        $fungsiId = $indikator->fungsi_standar_id;
        $indikator->delete();

        return redirect()->route('sk.indikator.index', $fungsiId)
            ->with('success', 'Indikator berhasil dihapus.');
    }
    public function indexRekomendasi($id)
    {
        $indikator = StandarIndikator::findOrFail($id);
        $rekomendasis = $indikator->rekomendasiStandards()->orderBy('id', 'asc')->get();

        return view('admin.sk.rekomendasi_index', compact('indikator', 'rekomendasis'));
    }
    public function rekomendasiPDF($id)
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // $fungsiStandar = FungsiStandar::findOrFail($fungsi);

        $fungsiStandar = FungsiStandar::with('kategori')->findOrFail($id);


        $indikator = StandarIndikator::findOrFail($id);
        $rekomendasis = $indikator->rekomendasiStandards()->orderBy('id', 'asc')->get();

        $pdf = PDF::loadView('admin.sk.rekomendasipdf', compact('namaOpd', 'indikator', 'rekomendasis', 'fungsiStandar'))
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

        return $pdf->download('rekomendasipdf' . date('Ymd_His') . '.pdf');
    }



    public function createRekomendasi($id)
    {
        $indikator = StandarIndikator::findOrFail($id);
        return view('admin.sk.rekomendasi_create', compact('indikator'));
    }
    public function storeRekomendasi(Request $request, $id)
    {
        $request->validate([
            'rekomendasi' => 'required|string',
            'buktidukung' => 'nullable|string',
        ]);

        RekomendasiStandard::create([
            'standar_indikator_id' => $id,
            'rekomendasi' => $request->rekomendasi,
            'buktidukung' => $request->buktidukung,
        ]);

        return redirect()->route('sk.rekomendasi.index', $id)->with('success', 'Rekomendasi berhasil ditambahkan.');
    }
    public function editRekomendasi($id)
    {
        $rekomendasi = RekomendasiStandard::findOrFail($id);
        $indikator = $rekomendasi->indikator; // relasi belongsTo
        return view('admin.sk.rekomendasi_edit', compact('rekomendasi', 'indikator'));
    }
    public function updateRekomendasi(Request $request, $id)
    {
        $request->validate([
            'rekomendasi' => 'required|string',
            'buktidukung' => 'nullable|string',
        ]);

        $rekomendasi = RekomendasiStandard::findOrFail($id);
        $rekomendasi->update([
            'rekomendasi' => $request->rekomendasi,
            'buktidukung' => $request->buktidukung,
        ]);

        return redirect()->route('sk.rekomendasi.index', $rekomendasi->standar_indikator_id)->with('success', 'Rekomendasi berhasil diperbarui.');
    }
    public function destroyRekomendasi($id)
    {
        $rekomendasi = RekomendasiStandard::findOrFail($id);
        $indikatorId = $rekomendasi->standar_indikator_id;
        $rekomendasi->delete();

        return redirect()->route('sk.rekomendasi.index', $indikatorId)->with('success', 'Rekomendasi berhasil dihapus.');
    }
}
