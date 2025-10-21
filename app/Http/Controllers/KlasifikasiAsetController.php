<?php

namespace App\Http\Controllers;

use App\Models\KlasifikasiAset;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfFooter;
use Illuminate\Support\Facades\Schema;


class KlasifikasiAsetController extends Controller
{
    public function index()
    {
        $klasifikasis = KlasifikasiAset::all();
        return view('admin.klasifikasiaset.index', compact('klasifikasis'));
    }

    public function create()
    {
        return view('admin.klasifikasiaset.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'klasifikasiaset' => 'required|string|max:255',
        ]);

        KlasifikasiAset::create($request->only('klasifikasiaset'));
        return redirect()->route('klasifikasiaset.index')->with('success', 'Klasifikasi berhasil ditambahkan.');
    }

    public function edit(KlasifikasiAset $klasifikasiaset)
    {
        return view('admin.klasifikasiaset.edit', compact('klasifikasiaset'));
    }

    public function update(Request $request, KlasifikasiAset $klasifikasiaset)
    {
        $request->validate([
            'klasifikasiaset' => 'required|string|max:255',
        ]);

        $klasifikasiaset->update($request->only('klasifikasiaset'));
        return redirect()->route('klasifikasiaset.index')->with('success', 'Klasifikasi berhasil diupdate.');
    }

    public function destroy(KlasifikasiAset $klasifikasiaset)
    {
        $klasifikasiaset->delete();
        return redirect()->route('klasifikasiaset.index')->with('success', 'Klasifikasi berhasil dihapus.');
    }

    public function exportPDF()
    {
        $klasifikasis = KlasifikasiAset::with('subklasifikasi')->get();
        $pdf = Pdf::loadView('klasifikasiaset.export_pdf', compact('klasifikasis'))
            ->setPaper('A4', 'potrait');
    PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('klasifikasi_aset.pdf');
    }

    public function aturField1($id)
    {
        $klasifikasi = KlasifikasiAset::findOrFail($id);

        $daftarFieldAset = [
            'kode_aset',
            'nama_aset',
            'lokasi',
            'format_penyimpanan',
            'pemilik',
            'masa_berlaku',
            'penyedia_aset',
            'status_aset',
            'kerahasiaan',
            'integritas',
            'ketersediaan',
            'keaslian',
            'kenirsangkalan',
            'kategori',
            'status_personil',
            'nip_personil',
            'jabatan_personil',
            'fungsi',
            'unit'
        ];

        $selectedFields = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : json_decode($klasifikasi->tampilan_field_aset ?? '[]', true);

        return view('admin.klasifikasiaset.field', compact('klasifikasi', 'daftarFieldAset', 'selectedFields'));
    }

    public function aturField($id)
    {
        $klasifikasi = KlasifikasiAset::findOrFail($id);

        // Ambil semua kolom dari tabel 'asets'
        $semuaKolom = Schema::getColumnListing('asets');

        // Kolom yang tidak perlu ditampilkan (exclude)
        $kolomDikecualikan = ['id', 'created_at', 'updated_at'];

        // Filter kolom agar hanya yang relevan untuk ditampilkan
        $daftarFieldAset = array_values(array_diff($semuaKolom, $kolomDikecualikan));

        // Ambil field yang sudah dipilih sebelumnya
        $selectedFields = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : json_decode($klasifikasi->tampilan_field_aset ?? '[]', true);

        return view('admin.klasifikasiaset.field', compact('klasifikasi', 'daftarFieldAset', 'selectedFields'));
    }


    public function simpanField(Request $request, $id)
    {
        $klasifikasi = KlasifikasiAset::findOrFail($id);
        $klasifikasi->tampilan_field_aset = $request->input('fields', []);
        $klasifikasi->save();

        return redirect()->route('klasifikasiaset.index')->with('success', 'Konfigurasi tampilan field berhasil disimpan.');
    }
}
