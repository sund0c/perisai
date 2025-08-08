<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    // Menampilkan semua periode
    public function index()
    {
        $periodes = Periode::orderBy('tahun', 'desc')->get();
        return view('admin.periodes.index', compact('periodes'));
    }

    // Tampilkan form tambah
    public function create()
    {
        return view('admin.periodes.create');
    }

    // Simpan data baru
    public function store(Request $request)
{
    $request->validate([
        'tahun' => 'required|digits:4|unique:periodes,tahun',
        'status' => 'required|in:open,closed',
        'kunci' => 'required|in:open,locked',
    ]);

    if ($request->status === 'open') {
        // Tutup semua yang lain
        Periode::query()->update(['status' => 'closed']);
    }

    Periode::create($request->all());

    return redirect()->route('periodes.index')->with('success', 'Periode berhasil ditambahkan.');
}

    // Tampilkan form edit
    public function edit(Periode $periode)
    {
        return view('admin.periodes.edit', compact('periode'));
    }

    // Simpan update
   public function update(Request $request, Periode $periode)
{
    $request->validate([
        'tahun' => 'required|digits:4|unique:periodes,tahun,' . $periode->id,
        'status' => 'required|in:open,closed',
        'kunci' => 'required|in:open,locked',
    ]);

    if ($request->status === 'open') {
        // Tutup semua periode lain terlebih dahulu
        Periode::where('id', '!=', $periode->id)->update(['status' => 'closed']);
    }

    $periode->update($request->all());

    return redirect()->route('periodes.index')->with('success', 'Periode berhasil diperbarui.');
}


    // Hapus periode
    public function destroy(Periode $periode)
    {
        $periode->delete();
        return redirect()->route('admin.periodes.index')->with('success', 'Periode berhasil dihapus.');
    }

    // Aktifkan periode (menutup yang lain)
    public function activate(Periode $periode)
    {
        Periode::query()->update(['status' => 'closed']);
        $periode->update(['status' => 'open']);

        return redirect()->route('periodes.index')->with('success', 'Periode diaktifkan.');
    }
}
