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
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class AsetController extends Controller
{
    public function index()
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        // Ambil klasifikasi + jumlah aset total per klasifikasi
        // $klasifikasis = KlasifikasiAset::withCount(['asets as jumlah_aset' => function ($query) use ($opdId, $periodeAktifId) {
        //     $query->where('opd_id', $opdId)
        //         ->where('periode_id', $periodeAktifId);
        // }])->get();


        $klasifikasis = KlasifikasiAset::select('id', 'klasifikasiaset', 'kodeklas')
            ->with([
                'subklasifikasi:id,klasifikasi_aset_id,subklasifikasiaset'
            ])
            ->withCount([
                'asets as jumlah_aset' => function ($q) use ($opdId, $periodeAktifId) {
                    $q->where('opd_id', $opdId)->where('periode_id', $periodeAktifId);
                }
            ])
            ->orderBy('klasifikasiaset')
            ->get();



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

        $canSync = !Aset::where('opd_id', $opdId)
            ->where('periode_id', $periodeAktifId)
            ->exists();
        $ranges = RangeAset::select('nilai_akhir_aset', 'deskripsi')->orderBy('nilai_atas')->get();

        return view('opd.aset.index', compact(
            'klasifikasis',
            'namaOpd',
            'totalTinggi',
            'totalSedang',
            'totalRendah',
            'canSync',
            'ranges'
        ));
    }







    public function showByKlasifikasi($id)
    {
        $periodeAktif = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        $klasifikasi = KlasifikasiAset::findOrFail($id);
        $subs = $klasifikasi->subklasifikasi;
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
        return view('opd.aset.show_by_klasifikasi', compact('klasifikasi', 'asets', 'namaOpd', 'subs'));
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
            $rules['spesifikasi_aset'] = 'required|string|max:255';
        }
        if (in_array('lokasi', $fields)) {
            $rules['lokasi'] = 'required|string|max:255';
        }
        if (in_array('keterangan', $fields)) {
            $rules['keterangan'] = 'nullable|string';
        }
        if (in_array('format_penyimpanan', $fields)) {
            $rules['format_penyimpanan'] = 'required|in:Fisik,Dokumen Elektronik,Fisik dan Dokumen Elektronik';
        }
        if (in_array('masa_berlaku', $fields)) {
            $rules['masa_berlaku'] = 'required|string|max:100'; // karena "12 Bulan" bukan format date
        }
        if (in_array('penyedia_aset', $fields)) {
            $rules['penyedia_aset'] = 'required|string|max:255';
        }
        if (in_array('status_aktif', $fields)) {
            $rules['status_aktif'] = 'required|in:Aktif,Tidak Aktif';
        }
        if (in_array('kondisi_aset', $fields)) {
            $rules['kondisi_aset'] = 'required|in:Baik,Tidak Layak,Rusak';
        }

        // Validasi CIAAA
        foreach (['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan'] as $ci) {
            if (in_array($ci, $fields)) {
                $rules[$ci] = 'required|in:1,2,3';
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

        try {
            $aset = Aset::create($validated);
            return redirect()
                ->route('opd.aset.show_by_klasifikasi', $klasifikasiId)
                ->with('success', 'Aset berhasil ditambahkan.');
        } catch (QueryException $e) {
            Log::warning('Gagal menyimpan aset (QueryException)', [
                'mysql_code' => $e->errorInfo[1] ?? null,   // 1062, 1452, dst.
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'user_id'    => auth()->id(),
                'route'      => request()->path(),
            ]);
            return back()->withInput()->with('error', 'Gagal menyimpan. Silakan periksa kembali isian Anda.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba lagi atau hubungi admin.');
        }
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
            $rules['spesifikasi_aset'] = 'required|string|max:255';
        }
        if (in_array('lokasi', $fields)) {
            $rules['lokasi'] = 'required|string|max:255';
        }
        if (in_array('keterangan', $fields)) {
            $rules['keterangan'] = 'nullable|string';
        }
        if (in_array('format_penyimpanan', $fields)) {
            $rules['format_penyimpanan'] = 'required|in:Fisik,Dokumen Elektronik,Fisik dan Dokumen Elektronik';
        }
        if (in_array('masa_berlaku', $fields)) {
            $rules['masa_berlaku'] = 'required|string|max:100'; // karena "12 Bulan" bukan format date
        }
        if (in_array('penyedia_aset', $fields)) {
            $rules['penyedia_aset'] = 'required|string|max:255';
        }
        if (in_array('status_aktif', $fields)) {
            $rules['status_aktif'] = 'required|in:Aktif,Tidak Aktif';
        }
        if (in_array('kondisi_aset', $fields)) {
            $rules['kondisi_aset'] = 'required|in:Baik,Tidak Layak,Rusak';
        }

        // Validasi CIAAA
        foreach (['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan'] as $ci) {
            if (in_array($ci, $fields)) {
                $rules[$ci] = 'required|in:1,2,3';
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


        try {
            $aset->update(array_intersect_key($validated, array_flip($fields)));
            return redirect()
                ->route('opd.aset.show_by_klasifikasi', $aset->klasifikasiaset_id)
                ->with('success', 'Aset berhasil diperbaharui.');
        } catch (QueryException $e) {
            Log::warning('Gagal memperbaharui aset (QueryException)', [
                'mysql_code' => $e->errorInfo[1] ?? null,   // 1062, 1452, dst.
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'user_id'    => auth()->id(),
                'route'      => request()->path(),
            ]);
            return back()->withInput()->with('error', 'Gagal memperbaharui. Silakan periksa kembali isian Anda.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba lagi atau hubungi admin.');
        }
    }

    public function destroy($id)
    {
        $aset = Aset::findOrFail($id);
        $klasifikasiId = $aset->klasifikasiaset_id;

        try {
            $aset->delete();

            return redirect()
                ->route('opd.aset.show_by_klasifikasi', $aset->klasifikasiaset_id)
                ->with('success', 'Aset berhasil dihapus.');
        } catch (QueryException $e) {
            // 1451 umumnya FK constraint violation
            Log::warning('Gagal menghapus aset (FK constraint?)', [
                'aset_id'    => $aset->id,
                'mysql_code' => $e->errorInfo[1] ?? null,
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'user_id'    => auth()->id(),
                'route'      => request()->path(),
            ]);

            return redirect()
                ->route('opd.aset.show_by_klasifikasi', $aset->klasifikasiaset_id) // sesuaikan nama route
                ->with('error', 'Penghapusan gagal karena data masih digunakan.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('opd.aset.show_by_klasifikasi', $aset->klasifikasiaset_id)
                ->with('error', 'Terjadi kesalahan. Penghapusan tidak dapat diproses.');
        }
    }


    public function exportRekapPdf()
    {

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        // $klasifikasis = KlasifikasiAset::withCount(['asets as jumlah_aset' => function ($query) use ($opdId, $periodeAktifId) {
        //     $query->where('opd_id', $opdId)
        //         ->where('periode_id', $periodeAktifId);
        // }])->get();


        $klasifikasis = KlasifikasiAset::select('id', 'klasifikasiaset', 'kodeklas')
            ->with([
                'subklasifikasi:id,klasifikasi_aset_id,subklasifikasiaset'
            ])
            ->withCount([
                'asets as jumlah_aset' => function ($q) use ($opdId, $periodeAktifId) {
                    $q->where('opd_id', $opdId)->where('periode_id', $periodeAktifId);
                }
            ])
            ->orderBy('klasifikasiaset')
            ->get();


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

        $ranges = RangeAset::select('nilai_akhir_aset', 'deskripsi')->orderBy('nilai_atas')->get();
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.aset.export_rekap_pdf', compact('klasifikasis', 'namaOpd', 'ranges'))
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

        return $pdf->download('rekapaset_' . date('YmdHis') . '.pdf');
    }

    public function exportRekapKlasPdf($id)
    {
        $periodeAktif = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        $klasifikasi = KlasifikasiAset::findOrFail($id);
        $subs = $klasifikasi->subklasifikasi;
        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('opd_id', $opdId)
            ->where('periode_id', $periodeAktif)
            ->with('subklasifikasiaset')
            ->get();
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.aset.export_rekap_klas_pdf', compact('klasifikasi', 'asets', 'namaOpd', 'subs'))
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
        return $pdf->download('rekapasetklas_' . date('YmdHis') . '.pdf');
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
        $ranges = RangeAset::select('nilai_akhir_aset', 'deskripsi')->orderBy('nilai_atas')->get();
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

        $pdf = PDF::loadView('opd.aset.pdf', compact('aset', 'namaOpd', 'klasifikasi', 'fieldList', 'subklasifikasis', 'ranges'))
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

        return $pdf->download('nilaiaset_' . strtolower(str_replace('-', '', $aset->kode_aset)) . '_' . date('YmdHis') . '.pdf');
    }
}
