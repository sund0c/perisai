<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;

use App\Models\Opd;
use App\Models\RangeAset;
use App\Models\Periode;
use App\Models\Aset;
use App\Models\SubKlasifikasiAset;
use App\Models\KlasifikasiAset;
use Illuminate\Http\Request;
use App\Traits\CalculatesAsetRange;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfFooter;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;




class BidangOpdController extends Controller
{
    use CalculatesAsetRange;

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
        $opds = Opd::all();
        return view('bidang.opd.index', compact('opds'));
    }

    public function view($idopd)
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        abort_unless($periodeAktifId, 404, 'Periode aktif tidak ditemukan');
        $opd = Opd::select('namaopd')->find($idopd);
        $asetopd = Aset::where('opd_id', $idopd)
            ->where('periode_id', $periodeAktifId)
            ->leftJoin('sub_klasifikasi_asets', 'sub_klasifikasi_asets.id', '=', 'asets.subklasifikasiaset_id')
            ->leftJoin('klasifikasi_asets', 'klasifikasi_asets.id', '=', 'asets.klasifikasiaset_id')
            ->orderBy('sub_klasifikasi_asets.subklasifikasiaset', 'asc')
            ->orderBy('klasifikasi_asets.klasifikasiaset', 'asc')
            ->orderBy('asets.nama_aset', 'asc')
            ->select('asets.*')
            ->with(['subklasifikasiaset', 'klasifikasi', 'opd'])
            ->get();

        $ranges = RangeAset::orderBy('nilai_bawah')->get();
        $this->applyRangeAttributes($asetopd, $ranges);
        return view('bidang.opd.view', compact('asetopd', 'opd'));
    }

    public function pdf($id)
    {
        $aset = Aset::with(['klasifikasi', 'subklasifikasiaset', 'opd'])->findOrFail($id);
        $klasifikasi = $aset->klasifikasi;
        $subklasifikasis = SubKlasifikasiAset::where('klasifikasi_aset_id', $klasifikasi->id)->get();

        $fieldList = is_array($klasifikasi->tampilan_field_aset)
            ? $klasifikasi->tampilan_field_aset
            : (json_decode($klasifikasi->tampilan_field_aset, true) ?? []);
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id',  'kategori_se'];
        $fieldList = array_values(array_diff($fieldList, $hiddenFields));

        $ranges = RangeAset::orderBy('nilai_bawah')->get();
        $this->applyRangeAttributes(collect([$aset]), $ranges);
        $namaOpd = $aset->opd->namaopd ?? '—';
        $pdf = PDF::loadView('bidang.aset.pdf', compact('aset', 'namaOpd', 'klasifikasi', 'fieldList', 'subklasifikasis', 'ranges'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait'); // A4 in points
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('detilaset_' . date('Ymd_His') . '.pdf');
    }

    public function edit(Aset $aset)
    {

        $aset->load(['klasifikasi.subklasifikasi', 'subklasifikasiaset', 'opd']);

        $klasifikasi     = $aset->klasifikasi;
        $subklasifikasis = $klasifikasi->subklasifikasi;

        $fieldListRaw = $klasifikasi->tampilan_field_aset;
        $fieldList    = is_array($fieldListRaw) ? $fieldListRaw : (json_decode($fieldListRaw ?? '[]', true) ?: []);

        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $fieldList    = array_values(array_diff($fieldList, $hiddenFields));

        $namaOpd = $aset->opd->namaopd ?? 'SEMUA OPD';
        $idopd = $aset->opd->id;

        $namaKlas     = $klasifikasi->klasifikasiaset;
        $fieldOptions = $this->fieldOptionsByKlasifikasi($namaKlas, $fieldList);

        foreach ($fieldList as $f) {
            if (empty($fieldOptions[$f])) {
                $fieldOptions[$f] = config("aset_fields.$f._DEFAULT_", []);
            }
        }


        return view('bidang.opd.edit', compact(
            'aset',
            'idopd',
            'namaOpd',
            'klasifikasi',
            'fieldList',
            'subklasifikasis',
            'fieldOptions'
        ));
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
            if (in_array($ci, $fields)) $rules[$ci] = 'required|in:1,2,3';
        }

        if (in_array('status_personil', $fields))  $rules['status_personil'] = 'nullable|in:SDM,Pihak Ketiga';
        if (in_array('nip_personil', $fields))     $rules['nip_personil'] = 'nullable|string|max:50';
        if (in_array('jabatan_personil', $fields)) $rules['jabatan_personil'] = 'nullable|string|max:100';
        if (in_array('fungsi_personil', $fields))  $rules['fungsi_personil'] = 'nullable|string|max:100';
        if (in_array('unit_personil', $fields))    $rules['unit_personil'] = 'nullable|string|max:100';

        $validated = $request->validate($rules);

        // Pastikan field CIAAA tersembunyi tetap punya nilai saat tidak dikirim dari form
        if (in_array('keaslian', $fields) && !array_key_exists('keaslian', $validated)) {
            $validated['keaslian'] = $aset->keaslian ?? 0;
        }
        if (in_array('kenirsangkalan', $fields) && !array_key_exists('kenirsangkalan', $validated)) {
            $validated['kenirsangkalan'] = $aset->kenirsangkalan ?? 0;
        }

        $allowedKeys = array_flip($fields);
        $payload     = array_intersect_key($validated, $allowedKeys);

        try {
            $aset->update($payload);

            return redirect()
                ->route('bidang.opd.view', $aset->opd->id)
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
