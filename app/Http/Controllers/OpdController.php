<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function index()
    {
        $opds = Opd::all();
        return view('admin.opd.index', compact('opds'));
    }

    public function create()
    {
        return view('admin.opd.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaopd' => 'required|unique:opds,namaopd',
        ]);

        Opd::create($request->only('namaopd'));

        return redirect()->route('opd.index')->with('success', 'OPD berhasil ditambahkan.');
    }

    public function edit(Opd $opd)
    {
        return view('admin.opd.edit', compact('opd'));
    }

    public function update(Request $request, Opd $opd)
    {
        $request->validate([
            'namaopd' => 'required|unique:opds,namaopd,' . $opd->id,
        ]);

        $opd->update($request->only('namaopd'));

        return redirect()->route('opd.index')->with('success', 'OPD berhasil diperbarui.');
    }

    public function destroy(Opd $opd)
    {
        $opd->delete();
        return redirect()->route('opd.index')->with('success', 'OPD berhasil dihapus.');
    }
}
