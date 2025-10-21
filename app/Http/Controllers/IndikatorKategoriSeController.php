<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndikatorKategoriSe;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfFooter;


class IndikatorKategoriSeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $indikator = IndikatorKategoriSe::orderBy('urutan')->get();
        return view('admin.indikatorkategorise.index', compact('indikator'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:indikator_kategorises,kode',
            'pertanyaan' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'urutan' => 'required|integer',
        ]);

        IndikatorKategoriSe::create(array_merge($validated, [
            'nilai_a' => 5,
            'nilai_b' => 2,
            'nilai_c' => 1,
        ]));

        return redirect()->route('admin.indikatorkategorise.index')
            ->with('success', 'Indikator berhasil ditambahkan');
    }
    public function create()
    {
        return view('admin.indikatorkategorise.create');
    }

    public function edit($id)
    {
        $indikator = IndikatorKategoriSe::findOrFail($id);
        return view('admin.indikatorkategorise.edit', compact('indikator'));
    }

    public function update(Request $request, $id)
    {
        $indikator = IndikatorKategoriSe::findOrFail($id);

        $validated = $request->validate([
            'kode' => 'required|unique:indikator_kategorises,kode,' . $id,
            'pertanyaan' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'urutan' => 'required|integer',
        ]);

        $indikator->update(array_merge($validated, [
            'nilai_a' => 5,
            'nilai_b' => 2,
            'nilai_c' => 1,
        ]));

        return redirect()->route('admin.indikatorkategorise.index')
            ->with('success', 'Indikator berhasil diperbarui');
    }

    public function destroy($id)
    {
        $indikator = IndikatorKategoriSe::findOrFail($id);
        $indikator->delete();

        return redirect()->route('admin.indikatorkategorise.index')
            ->with('success', 'Indikator berhasil dihapus');
    }

    public function exportPDF()
    {
        $indikator = IndikatorKategoriSe::all();
        $pdf = Pdf::loadView('admin.indikatorkategorise.export_pdf', compact('indikator'))
            ->setPaper('A4', 'potrait');
        PdfFooter::add_default($pdf);
        return $pdf->download('indikator_kategorise.pdf');
    }
}
