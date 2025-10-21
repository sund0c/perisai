<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RangeSe;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfFooter;

class RangeSeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rangeSes = RangeSe::all();
        return view('admin.range_se.index', compact('rangeSes'));
    }

    public function create()
    {
        return view('admin.range_se.create');
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
        RangeSe::create($request->all());

        return redirect()->route('rangese.index')->with('success', 'Range Aset ditambahkan.');
    }

    public function edit($id)
    {
        $rangese = RangeSe::findOrFail($id);
        return view('admin.range_se.edit', compact('rangese'));
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

        $rangeSe = RangeSe::findOrFail($id);
        $rangeSe->update($request->all());

        return redirect()->route('rangese.index')->with('success', 'Range Kategori SE berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rangeSe = RangeSe::findOrFail($id);
        $rangeSe->delete();

        return redirect()->route('rangese.index')->with('success', 'Range Kategori SE berhasil dihapus.');
    }

    public function exportPDF()
    {
        $rangeSes = RangeSe::all();
        $pdf = Pdf::loadView('admin.range_se.export_pdf', compact('rangeSes'))
            ->setPaper('A4', 'potrait');
    PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('range_se.pdf');
    }
}
