<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;

use App\Models\Aset;
use App\Models\RangeAset;
use App\Models\KlasifikasiAset;
use App\Models\Periode;
use App\Models\SubKlasifikasiAset;
use App\Models\KonfigurasiField;
use Illuminate\Http\Request;
use PDF;

class AsetController extends Controller
{
    public function index()
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        // Ambil klasifikasi + jumlah aset total per klasifikasi
        $klasifikasis = KlasifikasiAset::withCount(['asets as jumlah_aset' => function ($query) use ($opdId, $periodeAktifId) {
            $query->where('opd_id', $opdId)
                ->where('periode_id', $periodeAktifId);
        }])->get();

        // Hitung jumlah aset berdasarkan nilai CIAAA (TINGGI, SEDANG, RENDAH)
        foreach ($klasifikasis as $klasifikasi) {
            $totalTinggi = $klasifikasis->sum('jumlah_tinggi');
            $totalSedang = $klasifikasis->sum('jumlah_sedang');
            $totalRendah = $klasifikasis->sum('jumlah_rendah');
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('opd_id', $opdId)
                ->where('periode_id', $periodeAktifId)
                ->get();

            $jumlahTinggi = 0;
            $jumlahSedang = 0;
            $jumlahRendah = 0;

            foreach ($asets as $aset) {
                $total = collect([
                    $aset->kerahasiaan,
                    $aset->integritas,
                    $aset->ketersediaan,
                    $aset->keaslian,
                    $aset->kenirsangkalan,
                ])->map(fn($v) => intval($v))->sum();

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

            $klasifikasi->jumlah_tinggi = $jumlahTinggi;
            $klasifikasi->jumlah_sedang = $jumlahSedang;
            $klasifikasi->jumlah_rendah = $jumlahRendah;
        }

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        return view('opd.aset.index', compact(
            'klasifikasis',
            'namaOpd',
            'totalTinggi',
            'totalSedang',
            'totalRendah',
        ));
    }


    public function showByKlasifikasi($id)
    {
        $periodeAktif = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        $klasifikasi = KlasifikasiAset::findOrFail($id);

        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('opd_id', $opdId)
            ->where('periode_id', $periodeAktif)
            ->with('subklasifikasiaset')
            ->get();

        foreach ($asets as $aset) {
            // Total nilai keamanan informasi
            $total = collect([
                $aset->kerahasiaan,
                $aset->integritas,
                $aset->ketersediaan,
                $aset->keaslian,
                $aset->kenirsangkalan,
            ])->map(fn($v) => intval($v))->sum();

            // Cari nilai_akhir_aset dan warna
            $range = \App\Models\RangeAset::where('nilai_bawah', '<=', $total)
                ->where('nilai_atas', '>=', $total)
                ->first();

            $aset->nilai_akhir_aset = $range->nilai_akhir_aset ?? '-';
            $aset->warna_hexa = $range->warna_hexa ?? '#999999'; // default abu-abu
        }

        $namaOpd = auth()->user()->opd->namaopd ?? '-';
        return view('opd.aset.show_by_klasifikasi', compact('klasifikasi', 'asets', 'namaOpd'));
    }

    public function create($klasifikasiId)
    {
        $klasifikasi = KlasifikasiAset::findOrFail($klasifikasiId);
        $subklasifikasis = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasiId)->get();

        // Ambil semua field yang dikonfigurasi untuk ditampilkan
        $fieldList = $klasifikasi->tampilan_field_aset;
        if (!is_array($fieldList)) {
            $fieldList = json_decode($fieldList, true) ?? [];
        }

        // Hapus field yang seharusnya disembunyikan karena otomatis
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $fieldList = array_diff($fieldList, $hiddenFields);

        $namaOpd = auth()->user()->opd->namaopd ?? '-';


        return view('opd.aset.create', compact('klasifikasi', 'subklasifikasis', 'fieldList', 'namaOpd'));
    }


    public function store(Request $request, $klasifikasiId)
    {
        // Ambil konfigurasi field dari klasifikasi
        $klasifikasi = \App\Models\KlasifikasiAset::findOrFail($klasifikasiId);
        $fields = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : json_decode($klasifikasi->tampilan_field_aset, true);

        // Buat aturan validasi berdasarkan field yang aktif
        $rules = [];

        if (in_array('nama_aset', $fields)) {
            $rules['nama_aset'] = 'required|string|max:255';
        }
        if (in_array('subklasifikasiaset_id', $fields)) {
            $rules['subklasifikasiaset_id'] = 'required|exists:sub_klasifikasi_asets,id';
        }
        if (in_array('spesifikasi_aset', $fields)) {
            $rules['spesifikasi_aset'] = 'nullable|string|max:255';
        }
        if (in_array('lokasi', $fields)) {
            $rules['lokasi'] = 'nullable|string|max:255';
        }
        if (in_array('keterangan', $fields)) {
            $rules['keterangan'] = 'nullable|string';
        }
        if (in_array('format_penyimpanan', $fields)) {
            $rules['format_penyimpanan'] = 'nullable|in:Fisik,Dokumen Elektronik,Fisik dan Dokumen Elektronik';
        }
        if (in_array('masa_berlaku', $fields)) {
            $rules['masa_berlaku'] = 'nullable|string|max:100'; // karena "12 Bulan" bukan format date
        }
        if (in_array('penyedia_aset', $fields)) {
            $rules['penyedia_aset'] = 'nullable|string|max:255';
        }
        if (in_array('status_aktif', $fields)) {
            $rules['status_aktif'] = 'required|in:Aktif,Tidak Aktif';
        }
        if (in_array('kondisi_aset', $fields)) {
            $rules['kondisi_aset'] = 'nullable|in:Baik,Tidak Layak,Rusak';
        }

        // Validasi CIAAA
        foreach (['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan'] as $ci) {
            if (in_array($ci, $fields)) {
                $rules[$ci] = 'nullable|in:1,2,3';
            }
        }

        // Validasi personil
        if (in_array('status_personil', $fields)) {
            $rules['status_personil'] = 'nullable|in:SDM,Pihak Ketiga';
        }
        if (in_array('nip_personil', $fields)) {
            $rules['nip_personil'] = 'nullable|string|max:50';
        }
        if (in_array('jabatan_personil', $fields)) {
            $rules['jabatan_personil'] = 'nullable|string|max:100';
        }
        if (in_array('fungsi_personil', $fields)) {
            $rules['fungsi_personil'] = 'nullable|string|max:100';
        }
        if (in_array('unit_personil', $fields)) {
            $rules['unit_personil'] = 'nullable|string|max:100';
        }

        // Lakukan validasi
        $validated = $request->validate($rules);


        // Tambahkan field tetap (wajib disimpan meskipun tidak dari form)
        $validated['periode_id'] = Periode::where('status', 'open')->value('id');
        $validated['klasifikasiaset_id'] = $klasifikasiId;
        $validated['opd_id'] = auth()->user()->opd_id;

        // Generate kode aset
        $prefix = strtoupper($klasifikasi->kodeklas);
        $jumlahAsetSebelumnya = Aset::where('klasifikasiaset_id', $klasifikasiId)->count();
        $nomorUrut = str_pad($jumlahAsetSebelumnya + 1, 4, '0', STR_PAD_LEFT);
        $validated['kode_aset'] = $prefix . '-' . $nomorUrut;

        // Buat record baru
        Aset::create($validated);

        return redirect()
            ->route('opd.aset.show_by_klasifikasi', $klasifikasiId)
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Ambil data aset beserta relasinya
        $aset = Aset::with('klasifikasi', 'subklasifikasiaset')->findOrFail($id);

        // Ambil klasifikasi dari relasi aset
        $klasifikasi = $aset->klasifikasi;

        // Ambil subklasifikasi terkait
        $subklasifikasis = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasi->id)->get();

        // Ambil field-field yang akan ditampilkan
        $fieldList = $klasifikasi->tampilan_field_aset;
        if (!is_array($fieldList)) {
            $fieldList = json_decode($fieldList, true) ?? [];
        }

        // Saring field tersembunyi
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $fieldList = array_diff($fieldList, $hiddenFields);

        // Ambil nama OPD
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        return view('opd.aset.edit', compact('aset', 'namaOpd', 'klasifikasi', 'fieldList', 'subklasifikasis'));
    }

    public function update(Request $request, $id)
    {
        $aset = Aset::findOrFail($id);
        // Ambil konfigurasi field yang ditampilkan dari tabel klasifikasi_asets
        $klasifikasi = \App\Models\KlasifikasiAset::find($aset->klasifikasiaset_id);
        $fields = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : json_decode($klasifikasi->tampilan_field_aset, true);

        // Buat aturan validasi berdasarkan field yang aktif
        $rules = [];

        if (in_array('nama_aset', $fields)) {
            $rules['nama_aset'] = 'required|string|max:255';
        }
        if (in_array('subklasifikasiaset_id', $fields)) {
            $rules['subklasifikasiaset_id'] = 'required|exists:sub_klasifikasi_asets,id';
        }
        if (in_array('spesifikasi_aset', $fields)) {
            $rules['spesifikasi_aset'] = 'nullable|string|max:255';
        }
        if (in_array('lokasi', $fields)) {
            $rules['lokasi'] = 'nullable|string|max:255';
        }
        if (in_array('keterangan', $fields)) {
            $rules['keterangan'] = 'nullable|string';
        }
        if (in_array('format_penyimpanan', $fields)) {
            $rules['format_penyimpanan'] = 'nullable|in:Fisik,Dokumen Elektronik,Fisik dan Dokumen Elektronik';
        }
        if (in_array('masa_berlaku', $fields)) {
            $rules['masa_berlaku'] = 'nullable|string|max:100'; // karena "12 Bulan" bukan format date
        }
        if (in_array('penyedia_aset', $fields)) {
            $rules['penyedia_aset'] = 'nullable|string|max:255';
        }
        if (in_array('status_aktif', $fields)) {
            $rules['status_aktif'] = 'required|in:Aktif,Tidak Aktif';
        }
        if (in_array('kondisi_aset', $fields)) {
            $rules['kondisi_aset'] = 'nullable|in:Baik,Tidak Layak,Rusak';
        }

        // Validasi CIAAA
        foreach (['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan'] as $ci) {
            if (in_array($ci, $fields)) {
                $rules[$ci] = 'nullable|in:1,2,3';
            }
        }

        // Validasi personil
        if (in_array('status_personil', $fields)) {
            $rules['status_personil'] = 'nullable|in:SDM,Pihak Ketiga';
        }
        if (in_array('nip_personil', $fields)) {
            $rules['nip_personil'] = 'nullable|string|max:50';
        }
        if (in_array('jabatan_personil', $fields)) {
            $rules['jabatan_personil'] = 'nullable|string|max:100';
        }
        if (in_array('fungsi_personil', $fields)) {
            $rules['fungsi_personil'] = 'nullable|string|max:100';
        }
        if (in_array('unit_personil', $fields)) {
            $rules['unit_personil'] = 'nullable|string|max:100';
        }

        // Lakukan validasi
        $validated = $request->validate($rules);

        // Update data aset hanya pada field yang aktif
        $aset->update(array_intersect_key($validated, array_flip($fields)));



        return redirect()
            ->route('opd.aset.show_by_klasifikasi', $aset->klasifikasiaset_id)
            ->with('success', 'Aset berhasil diperbarui.');
        // dd($aset->klasifikasiaset_id);

    }

    public function destroy($id)
    {
        $aset = Aset::findOrFail($id);
        $klasifikasiId = $aset->klasifikasiaset_id;

        try {
            $aset->delete();
            $status = 'success';
            $pesan = 'Penghapusan Aset Berhasil';
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error 1451 untuk foreign key constraint
            $status = 'error';
            $pesan = 'Penghapusan Aset Gagal karena data sudah terpakai';
        } catch (\Throwable $e) {
            // Penanganan error lain
            $status = 'error';
            $pesan = 'Penghapusan Aset Gagal karena data sudah terpakai';
        }

        return redirect()
            ->route('opd.aset.show_by_klasifikasi', $klasifikasiId)
            ->with($status, $pesan);
    }


    public function exportRekapPdf()
    {

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        $klasifikasis = KlasifikasiAset::withCount(['asets as jumlah_aset' => function ($query) use ($opdId, $periodeAktifId) {
            $query->where('opd_id', $opdId)
                ->where('periode_id', $periodeAktifId);
        }])->get();

        // Hitung nilai aset per klasifikasi
        foreach ($klasifikasis as $klasifikasi) {
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('opd_id', $opdId)
                ->where('periode_id', $periodeAktifId)
                ->get();

            $jumlahTinggi = 0;
            $jumlahSedang = 0;
            $jumlahRendah = 0;

            foreach ($asets as $aset) {
                $total = collect([
                    $aset->kerahasiaan,
                    $aset->integritas,
                    $aset->ketersediaan,
                    $aset->keaslian,
                    $aset->kenirsangkalan,
                ])->map(fn($v) => intval($v))->sum();

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

            $klasifikasi->jumlah_tinggi = $jumlahTinggi;
            $klasifikasi->jumlah_sedang = $jumlahSedang;
            $klasifikasi->jumlah_rendah = $jumlahRendah;
        }

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.aset.export_rekap_pdf', compact('klasifikasis', 'namaOpd'))
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

        return $pdf->download('rekap_aset_' . date('Ymd_His') . '.pdf');
    }

    public function exportRekapKlasPdf($id)
    {
        $periodeAktif = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        $klasifikasi = KlasifikasiAset::findOrFail($id);

        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('opd_id', $opdId)
            ->where('periode_id', $periodeAktif)
            ->with('subklasifikasiaset')
            ->get();
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.aset.export_rekap_klas_pdf', compact('klasifikasi', 'asets', 'namaOpd'))
            ->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI  :: Page $pageNumber of $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2; // Center horizontally
            $y = $height - 30; // 30 px from bottom
            $canvas->text($x, $y, $text, $font, $size);
        });
        return $pdf->download('rekap_aset_klas_' . date('Ymd_His') . '.pdf');
    }

    public function pdf($id)
    {
        $aset = Aset::with('klasifikasi', 'subklasifikasiaset')->findOrFail($id);

        $klasifikasi = $aset->klasifikasi;
        $subklasifikasis = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasi->id)->get();

        $fieldList = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : json_decode($klasifikasi->tampilan_field_aset, true) ?? [];

        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $fieldList = array_diff($fieldList, $hiddenFields);

        // Hitung nilai keamanan
        $total = collect([
            $aset->kerahasiaan,
            $aset->integritas,
            $aset->ketersediaan,
            $aset->keaslian,
            $aset->kenirsangkalan,
        ])->map(fn($v) => intval($v))->sum();

        $range = \App\Models\RangeAset::where('nilai_bawah', '<=', $total)
            ->where('nilai_atas', '>=', $total)
            ->first();

        $aset->nilai_akhir_aset = $range->nilai_akhir_aset ?? '-';
        $aset->warna_hexa = $range->warna_hexa ?? '#999999';

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.aset.pdf', compact('aset', 'namaOpd', 'klasifikasi', 'fieldList', 'subklasifikasis'))
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

        return $pdf->download('detilaset_' . date('Ymd_His') . '.pdf');
    }
}
