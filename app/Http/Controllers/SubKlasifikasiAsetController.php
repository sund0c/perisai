<?php

namespace App\Http\Controllers;

use App\Models\SubKlasifikasiAset;
use App\Models\KlasifikasiAset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfFooter;


class SubKlasifikasiAsetController extends Controller
{
    public function index($klasifikasiaset_id)
    {
        $klasifikasi = KlasifikasiAset::findOrFail($klasifikasiaset_id);
        $subklasifikasi = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasiaset_id)->get();


        return view('admin.subklasifikasiaset.index', compact('klasifikasi', 'subklasifikasi'));
    }

    public function create($klasifikasiaset_id)
    {
        $klasifikasi = KlasifikasiAset::findOrFail($klasifikasiaset_id);
        return view('admin.subklasifikasiaset.create', compact('klasifikasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'klasifikasi_aset_id' => ['required', Rule::exists('klasifikasi_asets', 'id')],
            'subklasifikasiaset' => 'required|string|max:255',
            'penjelasan' => 'nullable|string'
        ]);

        SubKlasifikasiAset::create($request->only([
            'klasifikasi_aset_id',
            'subklasifikasiaset',
            'penjelasan',
        ]));

        return redirect()->route('subklasifikasiaset.index', $request->klasifikasi_aset_id)
            ->with('success', 'Subklasifikasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $sub = SubKlasifikasiAset::findOrFail($id);
        $klasifikasi = KlasifikasiAset::findOrFail($sub->klasifikasi_aset_id);
        return view('admin.subklasifikasiaset.edit', compact('sub', 'klasifikasi'));
    }

    public function update(Request $request, $id)
    {
        $sub = SubKlasifikasiAset::findOrFail($id);

        $request->validate([
            'subklasifikasiaset' => 'required|string|max:255',
            'penjelasan' => 'nullable|string'
        ]);

        $sub->update($request->only(['subklasifikasiaset', 'penjelasan']));

        return redirect()->route('subklasifikasiaset.index', $sub->klasifikasi_aset_id)->with('success', 'Subklasifikasi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $sub = SubKlasifikasiAset::findOrFail($id);
        $klasifikasi_id = $sub->klasifikasi_aset_id;
        $sub->delete();

        return redirect()->route('subklasifikasiaset.index', $klasifikasi_id)->with('success', 'Subklasifikasi berhasil dihapus.');
    }

    public function exportPDF($klasifikasiaset_id)
    {
        $klasifikasis = KlasifikasiAset::findOrFail($klasifikasiaset_id);
        $subklasifikasi = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasiaset_id)->get();

        //$klasifikasis = KlasifikasiAset::with('subklasifikasi')->get();
        $pdf = Pdf::loadView('admin.subklasifikasiaset.export_pdf', compact('klasifikasis', 'subklasifikasi'))
            ->setPaper('A4', 'potrait');
    PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('subklasifikasi_aset.pdf');
    }
}
