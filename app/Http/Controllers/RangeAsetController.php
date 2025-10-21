<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RangeAset;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfFooter;

class RangeAsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rangeAsets = RangeAset::all();
        return view('admin.range_aset.index', compact('rangeAsets'));
    }

    public function create()
    {
        return view('admin.range_aset.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nilai_akhir_aset' => 'required',
            'warna_hexa' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'nilai_bawah' => 'required|numeric',
            'nilai_atas' => 'required|numeric',
            'deskripsi' => 'nullable',
        ]);
        $request->merge([
            'nilai_akhir_aset' => strtoupper($request->input('nilai_akhir_aset')),
        ]);
        RangeAset::create($request->all());

        return redirect()->route('rangeaset.index')->with('success', 'Range Aset ditambahkan.');
    }

    public function edit($id)
    {
        $rangeAset = RangeAset::findOrFail($id);
        return view('admin.range_aset.edit', compact('rangeAset'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nilai_akhir_aset' => 'required',
            'warna_hexa'       => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'nilai_bawah'      => 'required|numeric',
            'nilai_atas'       => 'required|numeric',
            'deskripsi'        => 'nullable',
        ]);

        $request->merge([
            'nilai_akhir_aset' => strtoupper($request->input('nilai_akhir_aset')),
        ]);

        $rangeAset = RangeAset::findOrFail($id);
        $rangeAset->update($request->all());

        return redirect()->route('rangeaset.index')->with('success', 'Range Aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rangeAset = RangeAset::findOrFail($id);
        $rangeAset->delete();

        return redirect()->route('rangeaset.index')->with('success', 'Range Aset berhasil dihapus.');
    }

    public function exportPDF()
    {
        $rangeAsets = RangeAset::all();
        $pdf = Pdf::loadView('range_aset.export_pdf', compact('rangeAsets'))
            ->setPaper('A4', 'potrait');
        PdfFooter::add_default($pdf);
        return $pdf->download('range_aset.pdf');
    }
}
