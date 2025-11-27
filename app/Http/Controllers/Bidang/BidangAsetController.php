<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;


use App\Models\Aset;
use App\Models\RangeAset;
use App\Models\KlasifikasiAset;
use App\Models\Periode;
use App\Models\SubKlasifikasiAset;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfFooter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Traits\CalculatesAsetRange;

class BidangAsetController extends Controller
{
    use CalculatesAsetRange;

    /**
     * Ambil opsi field sesuai klasifikasi (fallback ke _DEFAULT_ bila kosong).
     */
    private function fieldOptionsByKlasifikasi(string $klasifikasi, array $fields): array
    {
        $map = config('aset_fields');
        $out = [];

        foreach ($fields as $field) {
            $all = Arr::get($map, $field, []);
            $specific = Arr::get($all, $klasifikasi);
            $out[$field] = $specific ?: Arr::get($all, '_DEFAULT_', []);
        }

        return $out;
    }

    public function index()
    {
        // 1) Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // fallback kalau belum ada periode open
            return view('bidang.aset.index', [
                'klasifikasis' => collect(),
                'namaOpd'      => 'SEMUA OPD',
                'totalTinggi'  => 0,
                'totalSedang'  => 0,
                'totalRendah'  => 0,
            ]);
        }

        // 2) Hitung jumlah aset per klasifikasi untuk SEMUA OPD (dibatasi periode aktif saja)
        $klasifikasis = KlasifikasiAset::withCount([
            'asets as jumlah_aset' => function ($query) use ($periodeAktifId) {
                $query->where('periode_id', $periodeAktifId);
            }
        ])->orderBy('klasifikasiaset')->get();

        $rangesCache = RangeAset::orderBy('nilai_bawah')->get();

        // 3) Siapkan total global
        $totalTinggi = 0;
        $totalSedang = 0;
        $totalRendah = 0;

        // 4) Loop tiap klasifikasi → ambil aset (semua OPD) di periode aktif
        foreach ($klasifikasis as $klasifikasi) {
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('periode_id', $periodeAktifId)
                ->get(['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan']); // hemat kolom

            $summary = $this->summarizeRangeCounts($asets, $rangesCache);

            // simpan ke objek klasifikasi untuk dipakai di view
            $klasifikasi->jumlah_tinggi = $summary['tinggi'];
            $klasifikasi->jumlah_sedang = $summary['sedang'];
            $klasifikasi->jumlah_rendah = $summary['rendah'];

            // akumulasi global
            $totalTinggi += $summary['tinggi'];
            $totalSedang += $summary['sedang'];
            $totalRendah += $summary['rendah'];
        }

        // 5) Karena ini agregat lintas OPD:
        $namaOpd = 'SEMUA OPD';
        $ranges = RangeAset::select('nilai_akhir_aset', 'deskripsi')->orderBy('nilai_atas')->get();

        return view('bidang.aset.index', compact(
            'klasifikasis',
            'namaOpd',
            'totalTinggi',
            'totalSedang',
            'totalRendah',
            'ranges'
        ));
    }



    public function showByKlasifikasi($id)
    {
        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        abort_unless($periodeAktifId, 404, 'Periode aktif tidak ditemukan');

        // Pastikan klasifikasi ada
        $klasifikasi = KlasifikasiAset::findOrFail($id);
        $subs  = $klasifikasi->subklasifikasi;

        // Ambil aset untuk SEMUA OPD pada periode aktif, sekaligus relasi subklasifikasi
        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('periode_id', $periodeAktifId)
            ->with('subklasifikasiaset')
            ->get(['id', 'kode_aset', 'nama_aset', 'subklasifikasiaset_id', 'kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan']);


        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('periode_id', $periodeAktifId)
            ->with([
                'subklasifikasiaset', // sesuaikan nama kolom
                'opd:id,namaopd',
            ])
            ->get([
                'id',
                'opd_id', // PENTING: harus di-select agar relasi opd() terikat
                'uuid',
                'kode_aset',
                'nama_aset',
                'subklasifikasiaset_id',
                'kerahasiaan',
                'integritas',
                'ketersediaan',
                'keaslian',
                'kenirsangkalan'
            ]);



        // Ambil range sekali (hemat query)
        $ranges = RangeAset::orderBy('nilai_bawah')->get();
        $this->applyRangeAttributes($asets, $ranges);

        // Karena lintas OPD, labelnya diset generik
        //$namaOpd = 'SEMUA OPD';
        return view('bidang.aset.show_by_klasifikasi', compact('klasifikasi', 'asets', 'subs'));
    }

    public function edit(Aset $aset)
    {
        // Bidang dapat mengedit aset lintas OPD, jadi tanpa policy OPD.
        $aset->load(['klasifikasi.subklasifikasi', 'subklasifikasiaset', 'opd']);

        $klasifikasi     = $aset->klasifikasi;
        $subklasifikasis = $klasifikasi->subklasifikasi;

        $fieldListRaw = $klasifikasi->tampilan_field_aset;
        $fieldList    = is_array($fieldListRaw) ? $fieldListRaw : (json_decode($fieldListRaw ?? '[]', true) ?: []);

        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $fieldList    = array_values(array_diff($fieldList, $hiddenFields));

        $namaOpd = $aset->opd->namaopd ?? 'SEMUA OPD';

        $namaKlas     = $klasifikasi->klasifikasiaset;
        $fieldOptions = $this->fieldOptionsByKlasifikasi($namaKlas, $fieldList);

        foreach ($fieldList as $f) {
            if (empty($fieldOptions[$f])) {
                $fieldOptions[$f] = config("aset_fields.$f._DEFAULT_", []);
            }
        }

        return view('bidang.aset.edit', compact(
            'aset',
            'namaOpd',
            'klasifikasi',
            'fieldList',
            'subklasifikasis',
            'fieldOptions'
        ));
    }

    public function exportRekapPdf()
    {
        // Periode aktif wajib ada
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // Boleh ganti ke redirect/flash sesuai selera
            $klasifikasis = collect();
            $namaOpd = 'SEMUA OPD';
            $pdf = PDF::loadView('bidang.aset.export_rekap_pdf', compact('klasifikasis', 'namaOpd'))
                ->setPaper('A4', 'portrait');
            return $pdf->download('rekap_aset_' . date('Ymd_His') . '.pdf');
        }

        // Hitung jumlah aset per klasifikasi untuk SEMUA OPD (dibatasi periode aktif)
        $klasifikasis = KlasifikasiAset::withCount([
            'asets as jumlah_aset' => function ($query) use ($periodeAktifId) {
                $query->where('periode_id', $periodeAktifId);
            }
        ])->get();

        // Ambil range sekali (diasumsikan jumlah range sedikit)
        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        foreach ($klasifikasis as $klasifikasi) {
            // Ambil aset di klasifikasi ini untuk SEMUA OPD pada periode aktif
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('periode_id', $periodeAktifId)
                ->get(['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan']);

            $summary = $this->summarizeRangeCounts($asets, $ranges);

            $klasifikasi->jumlah_tinggi = $summary['tinggi'];
            $klasifikasi->jumlah_sedang = $summary['sedang'];
            $klasifikasi->jumlah_rendah = $summary['rendah'];
        }

        // Karena lintas OPD
        $namaOpd = 'SEMUA OPD';
        $ranges = RangeAset::select('nilai_akhir_aset', 'deskripsi')->orderBy('nilai_atas')->get();
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('bidang.aset.export_rekap_pdf', compact('klasifikasis', 'namaOpd', 'ranges'))
            ->setPaper('A4', 'portrait');

        // Use centralized footer
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('asettikpemprovbali_' . date('Ymd_His') . '.pdf');
    }


    public function exportRekapKlasPdf($id)
    {
        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            abort(404, 'Periode aktif tidak ditemukan');
        }

        // Pastikan klasifikasi ada
        $klasifikasi = KlasifikasiAset::findOrFail($id);
        $subs  = $klasifikasi->subklasifikasi;


        // Ambil semua aset pada klasifikasi ini & periode aktif (tanpa filter OPD)
        $asets = Aset::where('klasifikasiaset_id', $id)
            ->where('periode_id', $periodeAktifId)
            ->with(
                'subklasifikasiaset',
                'opd:id,namaopd'
            ) // tambah relasi lain kalau perlu di view
            ->get([
                'id',
                'opd_id', // PENTING: tanpa ini relasi opd() tidak bisa dipetakan
                'kode_aset',
                'nama_aset',
                'subklasifikasiaset_id',
                'kerahasiaan',
                'integritas',
                'ketersediaan',
                'keaslian',
                'kenirsangkalan'
            ]);

        // Ambil range sekali untuk hemat query
        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        $this->applyRangeAttributes($asets, $ranges);

        // Karena semua OPD
        $namaOpd = 'SEMUA OPD';

        // Buat PDF
        $pdf = PDF::loadView('bidang.aset.export_rekap_klas_pdf', compact('klasifikasi', 'asets', 'namaOpd', 'subs'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait'); // A4 dalam points
        PdfFooter::add_right_corner_footer($pdf);

        return $pdf->download('asettikperklas_' . date('Ymd_His') . '.pdf');
    }



    public function pdf($id)
    {
        // Ambil aset apa pun OPD-nya + relasi yang dibutuhkan
        $aset = Aset::with(['klasifikasi', 'subklasifikasiaset', 'opd'])->findOrFail($id);

        $klasifikasi = $aset->klasifikasi;
        $subklasifikasis = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasi->id)->get();

        // Ambil daftar field tampilan per klasifikasi
        $fieldList = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : (json_decode($klasifikasi->tampilan_field_aset, true) ?? []);

        // Sembunyikan field teknis
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id',  'kategori_se'];
        $fieldList = array_values(array_diff($fieldList, $hiddenFields));

        $ranges = RangeAset::orderBy('nilai_bawah')->get();
        $this->applyRangeAttributes(collect([$aset]), $ranges);

        // Nama OPD pemilik aset (bukan OPD user login)
        $namaOpd = $aset->opd->namaopd ?? '—';

        // Buat PDF + footer (render → page_script)
        $pdf = PDF::loadView('bidang.aset.pdf', compact('aset', 'namaOpd', 'klasifikasi', 'fieldList', 'subklasifikasis', 'ranges'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait'); // A4 in points

        PdfFooter::add_right_corner_footer($pdf);

        return $pdf->download('detilaset_' . date('Ymd_His') . '.pdf');
    }

    public function update(Request $request, Aset $aset)
    {
        //dd($request);
        $aset->load('klasifikasi');
        $klasifikasi = $aset->klasifikasi;

        $cfg    = $klasifikasi->tampilan_field_aset;
        $fields = is_array($cfg) ? $cfg : (json_decode($cfg ?? '[]', true) ?: []);

        $rules = [];

        if (in_array('nama_aset', $fields))                $rules['nama_aset'] = 'required|string|max:255';
        if (in_array('subklasifikasiaset_id', $fields))    $rules['subklasifikasiaset_id'] = [
            'required',
            Rule::exists('sub_klasifikasi_asets', 'id')
                ->where('klasifikasi_aset_id', $klasifikasi->id),
        ];
        if (in_array('spesifikasi_aset', $fields))         $rules['spesifikasi_aset'] = 'required|string|max:255';
        if (in_array('lokasi', $fields))                   $rules['lokasi'] = 'required|string|max:255';
        if (in_array('link_pse', $fields))                 $rules['link_pse'] = 'nullable|string';
        if (in_array('link_url', $fields))                 $rules['link_url'] = 'nullable|url|starts_with:https://,http://';
        if (in_array('keterangan', $fields))               $rules['keterangan'] = 'nullable|string';
        if (in_array('format_penyimpanan', $fields))       $rules['format_penyimpanan'] = 'required|in:Fisik,Dokumen Elektronik,Fisik dan Dokumen Elektronik';
        if (in_array('masa_berlaku', $fields))             $rules['masa_berlaku'] = 'required|string|max:100';
        if (in_array('penyedia_aset', $fields))            $rules['penyedia_aset'] = 'required|string|max:255';
        if (in_array('status_aktif', $fields))             $rules['status_aktif'] = 'required|in:Aktif,Tidak Aktif';
        if (in_array('kondisi_aset', $fields))             $rules['kondisi_aset'] = 'required|in:Baik,Tidak Layak,Rusak';

        foreach (['kerahasiaan', 'integritas', 'ketersediaan'] as $ci) {
            if (in_array($ci, $fields)) $rules[$ci] = 'required|in:0,1,2,3'; // 0 untuk N/A
        }

        if (in_array('status_personil', $fields))  $rules['status_personil'] = 'nullable|in:SDM,Pihak Ketiga';
        if (in_array('nip_personil', $fields))     $rules['nip_personil'] = 'nullable|string|max:50';
        if (in_array('jabatan_personil', $fields)) $rules['jabatan_personil'] = 'nullable|string|max:100';
        if (in_array('fungsi_personil', $fields))  $rules['fungsi_personil'] = 'nullable|string|max:100';
        if (in_array('unit_personil', $fields))    $rules['unit_personil'] = 'nullable|string|max:100';

        $validated = $request->validate($rules);

        $allowedKeys = array_flip($fields);
        $payload     = array_intersect_key($validated, $allowedKeys);

        try {
            $aset->update($payload);

            return redirect()
                ->route('bidang.aset.show_by_klasifikasi', $klasifikasi->id)
                ->with('success', 'Aset berhasil diperbaharui.');
        } catch (QueryException $e) {
            Log::warning('Bidang gagal memperbaharui aset', [
                'mysql_code' => $e->errorInfo[1] ?? null,
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
}
