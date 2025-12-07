<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;

use App\Models\Aset;

use App\Models\RangeAset;
use App\Models\KlasifikasiAset;
use App\Models\Periode;
use App\Models\SubKlasifikasiAset;
use Illuminate\Http\Request;
use PDF;
use App\Services\PdfFooter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\CalculatesAsetRange;
use App\Exports\AsetExport;
use App\Imports\AsetImport;
use App\Exports\AsetTemplateExport;

class AsetController extends Controller
{
    use CalculatesAsetRange;

    public function __construct()
    {
        $this->authorizeResource(Aset::class, 'aset');
    }

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
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        $klasifikasis = KlasifikasiAset::select('id', 'klasifikasiaset', 'kodeklas')
            ->with([
                'subklasifikasi:id,klasifikasi_aset_id,subklasifikasiaset,penjelasan'
            ])
            ->withCount([
                'asets as jumlah_aset' => function ($q) use ($opdId, $periodeAktifId) {
                    $q->where('opd_id', $opdId)->where('periode_id', $periodeAktifId);
                }
            ])
            ->orderBy('klasifikasiaset')
            ->get();

        $totalTinggi = 0;
        $totalSedang = 0;
        $totalRendah = 0;



        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        // Hitung jumlah aset berdasarkan nilai CIAAA (TINGGI, SEDANG, RENDAH)
        foreach ($klasifikasis as $klasifikasi) {
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('opd_id', $opdId)
                ->where('periode_id', $periodeAktifId)
                ->get();

            $summary = $this->summarizeRangeCounts($asets, $ranges);

            $klasifikasi->jumlah_tinggi = $summary['tinggi'];
            $klasifikasi->jumlah_sedang = $summary['sedang'];
            $klasifikasi->jumlah_rendah = $summary['rendah'];
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

    public function showByKlasifikasi(KlasifikasiAset $klasifikasiaset)
    {
        // kalau mau tetap pakai variabel $klasifikasi di bawah:
        $klasifikasi = $klasifikasiaset;

        $periodeAktif = Periode::where('status', 'open')->value('id');
        $opdId        = auth()->user()->opd_id;

        $subs  = $klasifikasi->subklasifikasi;
        $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
            ->where('opd_id', $opdId)
            ->where('periode_id', $periodeAktif)
            ->with(['subklasifikasiaset', 'opd:id,namaopd'])
            ->get();

        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        $this->applyRangeAttributes($asets, $ranges);
        $badges = $asets->mapWithKeys(function ($aset) {
            return [
                $aset->id => [
                    'c' => $this->badgeCIA($aset->kerahasiaan),
                    'i' => $this->badgeCIA($aset->integritas),
                    'a' => $this->badgeCIA($aset->ketersediaan),
                ],
            ];
        });

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        return view('opd.aset.show_by_klasifikasi', compact('klasifikasi', 'asets', 'namaOpd', 'subs', 'badges'));
    }

    public function exportExcel(KlasifikasiAset $klasifikasiaset)
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            return back()->with('error', 'Tidak ada Periode dengan status OPEN.');
        }

        $opdId = auth()->user()->opd_id;

        $asets = Aset::where('klasifikasiaset_id', $klasifikasiaset->id)
            ->where('opd_id', $opdId)
            ->where('periode_id', $periodeAktifId)
            ->with(['subklasifikasiaset', 'opd'])
            ->orderBy('nama_aset')
            ->get();

        // Susunan field mengikuti konfigurasi tampilan (sama seperti template)
        $fieldsCfg    = $klasifikasiaset->tampilan_field_aset;
        $fields       = is_array($fieldsCfg) ? $fieldsCfg : (json_decode($fieldsCfg ?? '[]', true) ?: []);
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $skipFields   = ['keaslian', 'kenirsangkalan'];
        $fields       = array_values(array_diff($fields, array_merge($hiddenFields, $skipFields)));

        $subs         = $klasifikasiaset->subklasifikasi->pluck('subklasifikasiaset')->toArray();
        $fieldOptions = $this->fieldOptionsByKlasifikasi($klasifikasiaset->klasifikasiaset, $fields);

        $fileName = 'aset_' . $klasifikasiaset->kodeklas . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new AsetExport($asets, $fields, $klasifikasiaset->klasifikasiaset, $subs, $fieldOptions), $fileName);
    }

    public function importExcel(Request $request, KlasifikasiAset $klasifikasiaset)
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            return back()->with('error', 'Tidak ada Periode dengan status OPEN.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        $opdId = auth()->user()->opd_id;

        $prefix = strtoupper($klasifikasiaset->kodeklas ?? substr($klasifikasiaset->klasifikasiaset, 0, 2)) . '-';

        // Susunan field yang dipakai di form (hilangkan field otomatis / disembunyikan)
        $fieldsCfg    = $klasifikasiaset->tampilan_field_aset;
        $fields       = is_array($fieldsCfg) ? $fieldsCfg : (json_decode($fieldsCfg ?? '[]', true) ?: []);
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $skipFields   = ['keaslian', 'kenirsangkalan']; // selalu 0 otomatis
        $fields       = array_values(array_diff($fields, array_merge($hiddenFields, $skipFields)));

        $importer = new AsetImport(
            $klasifikasiaset->id,
            $periodeAktifId,
            $opdId,
            $prefix,
            fn(int $opdParam, string $pref) => $this->generateKodeAsetForOpd($opdParam, $pref),
            $fields
        );

        try {
            Excel::import($importer, $request->file('file'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Import gagal diproses. Pastikan format file sudah sesuai.');
        }

        $failures = $importer->failures();
        if ($failures && $failures->isNotEmpty()) {
            $messages = $failures->map(function ($failure) {
                $col = $failure->attribute();
                $msg = implode('; ', $failure->errors());
                return "Baris {$failure->row()}: {$col} - {$msg}";
            })->implode(' | ');

            return back()->with('error', 'Sebagian baris gagal diimport: ' . $messages);
        }

        return back()->with('success', 'Import aset berhasil diproses.');
    }

    public function templateExcel(KlasifikasiAset $klasifikasiaset)
    {
        $fileName = 'template_aset_' . $klasifikasiaset->kodeklas . '.xlsx';

        $subs = $klasifikasiaset->subklasifikasi->pluck('subklasifikasiaset')->toArray();

        // Susunan field mengikuti konfigurasi tampilan form (minus field otomatis)
        $fieldsCfg    = $klasifikasiaset->tampilan_field_aset;
        $fields       = is_array($fieldsCfg) ? $fieldsCfg : (json_decode($fieldsCfg ?? '[]', true) ?: []);
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $skipFields   = ['keaslian', 'kenirsangkalan']; // diisi otomatis 0 pada penyimpanan
        $fields       = array_values(array_diff($fields, array_merge($hiddenFields, $skipFields)));

        // Ambil opsi CIA sesuai klasifikasi (sama seperti form create)
        $fieldOptions = $this->fieldOptionsByKlasifikasi($klasifikasiaset->klasifikasiaset, $fields);

        return Excel::download(new AsetTemplateExport($subs, $fields, $fieldOptions, $klasifikasiaset->klasifikasiaset), $fileName);
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

        // Sembunyikan field otomatis
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $fieldList = array_values(array_diff($fieldList, $hiddenFields));

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // ⬇️ Ambil opsi per-klasifikasi untuk semua field dinamis
        // perhatikan: nama properti klasifikasi yang dipakai di config
        $namaKlas = $klasifikasi->klasifikasiaset; // contoh: 'DATA & INFORMASI' / 'PERANGKAT LUNAK' / dst
        $fieldOptions = $this->fieldOptionsByKlasifikasi($namaKlas, $fieldList);

        return view('opd.aset.create', compact(
            'klasifikasi',
            'subklasifikasis',
            'fieldList',
            'namaOpd',
            'fieldOptions'
        ));
    }

    public function store(Request $request, KlasifikasiAset $klasifikasiaset)
    {
        $this->authorize('create', [Aset::class, $klasifikasiaset]);

        $periodeAktif = Periode::where('status', 'open')->value('id');
        if (!$periodeAktif) {
            return back()->withInput()->with('error', 'Tidak ada tahun anggaran aktif.');
        }

        $opdId = auth()->user()->opd_id ?? null;
        if (!$opdId) {
            return back()->withInput()->with('error', 'Akun tidak terkait OPD.');
        }

        // ---- Konfigurasi field dari klasifikasi ----
        $cfg = $klasifikasiaset->tampilan_field_aset;
        $fields = is_array($cfg) ? $cfg : (json_decode($cfg ?? '[]', true) ?: []);

        // ---- Aturan validasi dinamis ----
        $rules = [];

        if (in_array('nama_aset', $fields))                $rules['nama_aset'] = 'required|string|max:255';
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

        // subklasifikasi: harus ada & milik klasifikasi ini
        if (in_array('subklasifikasiaset_id', $fields)) {
            $rules['subklasifikasiaset_id'] = [
                'required',
                Rule::exists('sub_klasifikasi_asets', 'id')
                    ->where('klasifikasi_aset_id', $klasifikasiaset->id),
            ];
        }

        // CIAAA
        foreach (['kerahasiaan', 'integritas', 'ketersediaan'] as $ci) {
            if (in_array($ci, $fields)) {
                $rules[$ci] = 'required|in:1,2,3';
            }
        }

        // Personil
        if (in_array('status_personil', $fields))  $rules['status_personil'] = 'nullable|in:SDM,Pihak Ketiga';
        if (in_array('nip_personil', $fields))     $rules['nip_personil'] = 'nullable|string|max:50';
        if (in_array('jabatan_personil', $fields)) $rules['jabatan_personil'] = 'nullable|string|max:100';
        if (in_array('fungsi_personil', $fields))  $rules['fungsi_personil'] = 'nullable|string|max:100';
        if (in_array('unit_personil', $fields))    $rules['unit_personil'] = 'nullable|string|max:100';

        $validated = $request->validate($rules);

        // Field CIAAA yang disembunyikan di form tapi kolomnya wajib isi di DB
        if (!array_key_exists('keaslian', $validated)) {
            $validated['keaslian'] = 0;
        }
        if (!array_key_exists('kenirsangkalan', $validated)) {
            $validated['kenirsangkalan'] = 0;
        }

        // ---- Field tetap & anti-IDOR ----
        $validated['uuid']               = (string) Str::uuid();
        $validated['periode_id']         = $periodeAktif;
        $validated['klasifikasiaset_id'] = $klasifikasiaset->id; // FK integer
        $validated['opd_id']             = $opdId;

        Log::warning('Detail penyimpanan aset', [
            'uuid' => $validated['uuid'],
            'periode_id' => $validated['periode_id'],
            'klasifikasiaset_id' => $validated['klasifikasiaset_id'],
            'opd_id' => $validated['opd_id'],
        ]);
        Log::warning('----------Pesan peringatan--------------');


        // Prefix dari klasifikasi (misal: "SK-" atau berdasarkan kode klasifikasi)
        $prefix = strtoupper($klasifikasiaset->kodeklas ?? substr($klasifikasiaset->klasifikasiaset, 0, 2)) . '-';

        try {
            $aset = DB::transaction(function () use ($validated, $prefix, $opdId, $periodeAktif, $klasifikasiaset) {
                // 1) Dapatkan atau buat ASET KEY secara atomik: helper kini mengembalikan ['kode' => ..., 'id' => ...]
                //    Ini menghindari nested transaction dan lock ganda yang dapat menyebabkan deadlock.
                $res = $this->generateKodeAsetForOpd($opdId, $prefix); // returns ['kode' => 'SK-0001', 'id' => 123]
                $kode = $res['kode'];
                $keyId = $res['id'];

                // 2) Simpan baris ASSET untuk periode ini
                $data = $validated;
                $data['aset_key_id'] = $keyId;
                $data['kode_aset']   = $kode;

                return Aset::create($data);
            });

            return redirect()
                ->route('opd.aset.show_by_klasifikasi', ['klasifikasiaset' => $klasifikasiaset])
                ->with('success', 'Aset berhasil ditambahkan.');
        } catch (QueryException $e) {
            Log::warning('Gagal menyimpan aset (QueryException)', [
                'mysql_code' => $e->errorInfo[1] ?? null,
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

    public function edit(Aset $aset)
    {
        $this->authorize('update', $aset);

        $aset->load(['klasifikasi.subklasifikasi', 'subklasifikasiaset']);

        $klasifikasi     = $aset->klasifikasi;
        $subklasifikasis = $klasifikasi->subklasifikasi;

        // Field list dari DB → array
        $fieldListRaw = $klasifikasi->tampilan_field_aset;
        $fieldList    = is_array($fieldListRaw) ? $fieldListRaw : (json_decode($fieldListRaw ?? '[]', true) ?: []);

        // Sembunyikan field otomatis
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id', 'kode_aset', 'kategori_se'];
        $fieldList    = array_values(array_diff($fieldList, $hiddenFields));

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // === Tambahan penting: ambil opsi per-klasifikasi (sama seperti create)
        $namaKlas     = $klasifikasi->klasifikasiaset; // pastikan sesuai key di config
        $fieldOptions = $this->fieldOptionsByKlasifikasi($namaKlas, $fieldList);

        // Fail-safe: kalau kosong, pakai _DEFAULT_
        foreach ($fieldList as $f) {
            if (empty($fieldOptions[$f])) {
                $fieldOptions[$f] = config("aset_fields.$f._DEFAULT_", []);
            }
        }

        return view('opd.aset.edit', compact(
            'aset',
            'namaOpd',
            'klasifikasi',
            'fieldList',
            'subklasifikasis',
            'fieldOptions'   // ← KIRIM KE VIEW
        ));
    }

    public function update(Request $request, Aset $aset)
    {
        // Pastikan user berhak mengubah aset ini
        $this->authorize('update', $aset);

        // Ambil klasifikasi & konfigurasi field
        $aset->load('klasifikasi');
        $klasifikasi = $aset->klasifikasi;

        $cfg    = $klasifikasi->tampilan_field_aset;
        $fields = is_array($cfg) ? $cfg : (json_decode($cfg ?? '[]', true) ?: []);

        // Aturan validasi dinamis berdasar field aktif
        $rules = [];

        if (in_array('nama_aset', $fields))                $rules['nama_aset'] = 'required|string|max:255';
        if (in_array('subklasifikasiaset_id', $fields))    $rules['subklasifikasiaset_id'] = [
            'required',
            Rule::exists('sub_klasifikasi_asets', 'id')
                ->where('klasifikasi_aset_id', $klasifikasi->id), // anti-tamper
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

        // Hanya update field yang diizinkan di konfigurasi
        $allowedKeys = array_flip($fields);
        $payload     = array_intersect_key($validated, $allowedKeys);

        try {
            $aset->update($payload);

            // Redirect ke halaman klasifikasi (siap UUID jika binding UUID aktif)
            return redirect()
                ->route('opd.aset.show_by_klasifikasi', ['klasifikasiaset' => $klasifikasi])
                ->with('success', 'Aset berhasil diperbaharui.');
        } catch (QueryException $e) {
            Log::warning('Gagal memperbaharui aset (QueryException)', [
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

    public function destroy(Aset $aset)
    {
        // Cek hak akses (OPD pemilik)
        $this->authorize('delete', $aset);

        // Ambil model klasifikasi untuk redirect (pakai model agar siap UUID)
        $aset->loadMissing('klasifikasi');
        $klasifikasi = $aset->klasifikasi; // instance KlasifikasiAset

        try {
            $aset->delete();

            return redirect()
                ->route('opd.aset.show_by_klasifikasi', ['klasifikasiaset' => $klasifikasi])
                ->with('success', 'Aset berhasil dihapus.');
        } catch (QueryException $e) {
            Log::warning('Gagal menghapus aset (FK constraint?)', [
                'aset_id'    => $aset->id,
                'mysql_code' => $e->errorInfo[1] ?? null,
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'user_id'    => auth()->id(),
                'route'      => request()->path(),
            ]);

            return redirect()
                ->route('opd.aset.show_by_klasifikasi', ['klasifikasiaset' => $klasifikasi])
                ->with('error', 'Penghapusan gagal karena data masih digunakan.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('opd.aset.show_by_klasifikasi', ['klasifikasiaset' => $klasifikasi])
                ->with('error', 'Terjadi kesalahan. Penghapusan tidak dapat diproses.');
        }
    }

    public function bulkDestroy(Request $request, KlasifikasiAset $klasifikasiaset)
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            return back()->with('error', 'Tidak ada Periode dengan status OPEN.');
        }

        $opdId = auth()->user()->opd_id;

        $validated = $request->validate([
            'aset_ids' => 'required|array|min:1',
            'aset_ids.*' => 'integer',
        ]);

        $asets = Aset::whereIn('id', $validated['aset_ids'])
            ->where('opd_id', $opdId)
            ->where('klasifikasiaset_id', $klasifikasiaset->id)
            ->where('periode_id', $periodeAktifId)
            ->get();

        if ($asets->isEmpty()) {
            return back()->with('error', 'Aset tidak ditemukan atau bukan milik OPD Anda.');
        }

        foreach ($asets as $aset) {
            $this->authorize('delete', $aset);
        }

        DB::transaction(function () use ($asets) {
            $asets->each->delete();
        });

        return back()->with('success', $asets->count() . ' aset berhasil dihapus.');
    }

    public function exportRekapPdf_old()
    {

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        $opdId = auth()->user()->opd_id;

        $klasifikasis = KlasifikasiAset::select('id', 'klasifikasiaset', 'kodeklas')
            ->with([
                'subklasifikasi:id,klasifikasi_aset_id,subklasifikasiaset,penjelasan'
            ])
            ->withCount([
                'asets as jumlah_aset' => function ($q) use ($opdId, $periodeAktifId) {
                    $q->where('opd_id', $opdId)->where('periode_id', $periodeAktifId);
                }
            ])
            ->orderBy('klasifikasiaset')
            ->get();

        $rangesCache = RangeAset::orderBy('nilai_bawah')->get();


        // Hitung nilai aset per klasifikasi
        foreach ($klasifikasis as $klasifikasi) {
            $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
                ->where('opd_id', $opdId)
                ->where('periode_id', $periodeAktifId)
                ->get();

            $summary = $this->summarizeRangeCounts($asets, $rangesCache);

            $klasifikasi->jumlah_tinggi = $summary['tinggi'];
            $klasifikasi->jumlah_sedang = $summary['sedang'];
            $klasifikasi->jumlah_rendah = $summary['rendah'];
        }

        $ranges = RangeAset::select('nilai_akhir_aset', 'deskripsi')->orderBy('nilai_atas')->get();
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.aset.export_rekap_pdf', compact('klasifikasis', 'namaOpd', 'ranges'))
            ->setPaper('A4', 'portrait');
        PdfFooter::add_right_corner_footer($pdf);

        return $pdf->download('rekapaset_' . date('YmdHis') . '.pdf');
    }

    public function exportRekapPdf()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            return back()->with('error', 'Tidak ada Periode dengan status OPEN.');
        }

        // $opdId = Auth::user()->opd_id;

        // $asets = Aset::where('opd_id', $opdId)
        //     ->where('periode_id', $periodeAktifId)
        //     ->with('subklasifikasiaset')
        //     ->orderBy('kode_aset')
        //     ->get();



        // SORTING: nilai akhir DESC, lalu subklasifikasi ASC
        // $asets = $asets->sort(function ($a, $b) {
        //     $cmp = $b->nilai_akhir_aset <=> $a->nilai_akhir_aset;
        //     if ($cmp !== 0) {
        //         return $cmp;
        //     }
        //     return strcmp(
        //         $a->subklasifikasiaset->subklasifikasiaset ?? '',
        //         $b->subklasifikasiaset->subklasifikasiaset ?? ''
        //     );
        //     $klasA = $a->subklasifikasiaset->klasifikasiAset->klasifikasiaset ?? '';
        //     $klasB = $b->subklasifikasiaset->klasifikasiAset->klasifikasiaset ?? '';
        // })->values();

        $opdId   = Auth::user()->opd_id;
        $namaOpd = Auth::user()->opd->namaopd ?? '-';

        // === Ambil Data Aset + Relasi Lengkap ===
        $asets = Aset::where('opd_id', $opdId)
            ->where('periode_id', $periodeAktifId)
            ->with([
                'subklasifikasiaset.klasifikasi' // relasi yang benar
            ])
            ->orderBy('kode_aset')
            ->get();

        // === Hitung warna & nilai akhir ===
        $ranges = RangeAset::orderBy('nilai_bawah')->get();
        $this->applyRangeAttributes($asets, $ranges);

        // === SORTING GABUNGAN ===
        $asets = $asets->sort(function ($a, $b) {

            // 1️⃣ Sort nilai akhir aset DESC
            $cmp = $b->nilai_akhir_aset <=> $a->nilai_akhir_aset;
            if ($cmp !== 0) return $cmp;

            // 2️⃣ Sort nama klasifikasi ASC
            $klasA = $a->subklasifikasiaset->klasifikasi->klasifikasiaset ?? '';
            $klasB = $b->subklasifikasiaset->klasifikasi->klasifikasiaset ?? '';
            $cmp = strcmp($klasA, $klasB);
            if ($cmp !== 0) return $cmp;

            // 3️⃣ Sort nama subklasifikasi ASC
            $subA = $a->subklasifikasiaset->subklasifikasiaset ?? '';
            $subB = $b->subklasifikasiaset->subklasifikasiaset ?? '';
            $cmp = strcmp($subA, $subB);
            if ($cmp !== 0) return $cmp;

            // 4️⃣ Sort nama aset ASC
            return strcmp($a->nama_aset ?? '', $b->nama_aset ?? '');
        })->values();

        // $namaOpd = Auth::user()->opd->namaopd ?? '-';

        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        $this->applyRangeAttributes($asets, $ranges);

        $badges = $asets->mapWithKeys(function ($aset) {
            return [
                $aset->id => [
                    'c' => $this->badgeCIA($aset->kerahasiaan),
                    'i' => $this->badgeCIA($aset->integritas),
                    'a' => $this->badgeCIA($aset->ketersediaan),
                ],
            ];
        });

        //dd($asets->toArray());

        // return view(
        //     'opd.aset.export_rekap_pdf',
        //     compact('asets', 'namaOpd',  'badges')
        // );

        $pdf = Pdf::loadView('opd.aset.export_rekap_pdf', compact('asets', 'namaOpd',  'badges'))
            ->setPaper('A4', 'landscape');

        PdfFooter::add_right_corner_footer($pdf);

        return $pdf->stream('rekapaset_' . date('YmdHis') . '.pdf');
    }

    public function exportRekapKlasPdf(KlasifikasiAset $klasifikasiaset) //print rekap OPD
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            return back()->with('error', 'Tidak ada Periode dengan status OPEN.');
        }

        $opdId = Auth::user()->opd_id;

        $klasifikasi = $klasifikasiaset->fresh(['subklasifikasi']); // ambil sub-sekaligus
        $subs        = $klasifikasi->subklasifikasi;

        $asets = Aset::where('klasifikasiaset_id', $klasifikasi->id)
            ->where('opd_id', $opdId)
            ->where('periode_id', $periodeAktifId)
            ->with('subklasifikasiaset')
            ->orderBy('kode_aset')
            ->get();
        //$aset->nilai_akhir_aset

        $namaOpd = Auth::user()->opd->namaopd ?? '-';

        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        $this->applyRangeAttributes($asets, $ranges);

        // SORTING: nilai akhir DESC, lalu subklasifikasi ASC
        $asets = $asets->sort(function ($a, $b) {
            $cmp = $b->nilai_akhir_aset <=> $a->nilai_akhir_aset;
            if ($cmp !== 0) {
                return $cmp;
            }
            return strcmp(
                $a->subklasifikasiaset->subklasifikasiaset ?? '',
                $b->subklasifikasiaset->subklasifikasiaset ?? ''
            );
        })->values();

        $badges = $asets->mapWithKeys(function ($aset) {
            return [
                $aset->id => [
                    'c' => $this->badgeCIA($aset->kerahasiaan),
                    'i' => $this->badgeCIA($aset->integritas),
                    'a' => $this->badgeCIA($aset->ketersediaan),
                ],
            ];
        });

        $pdf = Pdf::loadView('opd.aset.export_rekap_klas_pdf', compact('klasifikasi', 'asets', 'namaOpd', 'subs', 'badges'))
            ->setPaper('A4', 'landscape');

        PdfFooter::add_right_corner_footer($pdf);
        $filename = 'rekapasetklas_' . now()->format('YmdHis') . '.pdf';
        return $pdf->stream($filename);
    }

    public function pdf(Aset $aset) //Profil Aset Informasi
    {
        // $aset otomatis di-resolve dari UUID (karena rute {aset:uuid})
        $aset->load(['klasifikasi.subklasifikasi', 'subklasifikasiaset']);

        $klasifikasi      = $aset->klasifikasi;
        $subklasifikasis  = $klasifikasi->subklasifikasi; // via relasi

        // Pastikan fieldList array (bisa tersimpan JSON di DB)
        $fieldListRaw = $klasifikasi->tampilan_field_aset;
        $fieldList    = is_array($fieldListRaw) ? $fieldListRaw : (json_decode($fieldListRaw ?? '[]', true) ?: []);

        // Sembunyikan field yang tidak perlu dicetak
        $hiddenFields = ['opd_id', 'klasifikasiaset_id', 'periode_id',  'kategori_se'];
        $fieldList    = array_values(array_diff($fieldList, $hiddenFields));

        $ranges = RangeAset::orderBy('nilai_bawah')->get();

        // Hitung nilai keamanan
        $this->applyRangeAttributes(collect([$aset]), $ranges);

        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        $pdf = PDF::loadView('opd.aset.pdf', compact(
            'aset',
            'namaOpd',
            'klasifikasi',
            'fieldList',
            'subklasifikasis',
            'ranges'
        ))->setPaper('A4', 'portrait'); // 'portrait' (bukan 'potrait')

        PdfFooter::add_right_corner_footer($pdf);

        return $pdf->stream('profilaset_' . strtolower(str_replace('-', '', $aset->kode_aset)) . '_' . date('YmdHis') . '.pdf');
    }


    /**
     * Menghasilkan kode_aset baru untuk OPD tertentu dengan prefix tertentu
     * dan mendaftarkannya ke tabel aset_keys (kode_aset unik global).
     *
     * Aturan:
     * - Kode milik satu OPD sepanjang waktu (aset_keys.unique: kode_aset)
     * - Nomor berurutan per OPD+prefix (misal SK-0001, SK-0002, ...)
     * - Anti race: lock & retry jika terjadi bentrok 1062
     */
    /**
     * Menghasilkan kode_aset baru untuk OPD tertentu dengan prefix tertentu
     * dan mendaftarkannya ke tabel aset_keys (kode_aset unik global).
     *
     * Mengembalikan array ['kode' => string, 'id' => int]
     *
     * Catatan: fungsi ini tidak membuka transaksi sendiri; caller boleh membungkusnya
     * jika perlu. Duplikasi ditangani oleh unique constraint + retry pada insert.
     */
    private function generateKodeAsetForOpd(int $opdId, string $prefix, int $pad = 4): array
    {
        $attempts = 0;

        // Ambil nomor terakhir (tanpa lock). Collisions ditangani oleh unique constraint & retry pada insert.
        // $lastKode = DB::table('aset_keys')
        //     ->where('opd_id', $opdId)
        //     ->where('kode_aset', 'like', $prefix . '%')
        //     ->orderByDesc('kode_aset')
        //     ->value('kode_aset');

        // $lastNum = 0;
        // if ($lastKode && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $lastKode, $m)) {
        //     $lastNum = (int) $m[1];
        // }



        // ambil kode terakhir
        $lastKode = DB::table('aset_keys')
            // ->where('opd_id', $opdId) // Temporary disable
            ->where('kode_aset', 'like', $prefix . '%')
            ->orderByDesc('kode_aset')
            ->value('kode_aset');

        // inisialisasi
        $lastNum = 0;

        // logging sebelum regex
        Log::info('Cek kode aset terakhir', [
            'opd_id' => $opdId,
            'prefix' => $prefix,
            'lastKode' => $lastKode,
        ]);

        // cek hasil regex
        if ($lastKode && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $lastKode, $m)) {
            $lastNum = (int) $m[1];

            Log::info('Regex match ditemukan', [
                'pattern' => '/^' . preg_quote($prefix, '/') . '(\d+)$/',
                'lastKode' => $lastKode,
                'lastNum' => $lastNum,
            ]);
        } else {
            Log::warning('Regex tidak cocok atau lastKode kosong', [
                'lastKode' => $lastKode,
                'prefix' => $prefix,
                'opd_id' => $opdId,
            ]);
        }

        // untuk referensi tambahan
        Log::debug('Hasil akhir perhitungan lastNum', ['lastNum' => $lastNum]);

        while (true) {
            $attempts++;
            $candidate = $prefix . str_pad(++$lastNum, $pad, '0', STR_PAD_LEFT);

            try {
                $id = DB::table('aset_keys')->insertGetId([
                    'opd_id'     => $opdId,
                    'kode_aset'  => $candidate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return ['kode' => $candidate, 'id' => (int) $id];
            } catch (QueryException $e) {
                // 1062 = duplicate (kemungkinan karena OPD lain baru saja mengambil kode itu)
                if (($e->errorInfo[1] ?? null) === 1062) {
                    // Naikkan nomor dan coba lagi. Batasi percobaan bila perlu.
                    if ($attempts > 20) {
                        throw $e;
                    }
                    continue;
                }
                throw $e;
            }
        }
    }
}
