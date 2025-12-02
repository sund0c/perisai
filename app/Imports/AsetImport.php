<?php

namespace App\Imports;

use App\Models\Aset;
use App\Models\SubKlasifikasiAset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Support\AsetFieldLabels;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AsetImport implements ToCollection, WithHeadingRow, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    protected int $klasifikasiId;
    protected int $periodeId;
    protected int $opdId;
    protected string $kodePrefix;
    protected $kodeGenerator;
    protected array $fields;

    public function __construct(int $klasifikasiId, int $periodeId, int $opdId, string $kodePrefix, callable $kodeGenerator, array $fields = [])
    {
        $this->klasifikasiId = $klasifikasiId;
        $this->periodeId = $periodeId;
        $this->opdId = $opdId;
        $this->kodePrefix = $kodePrefix;
        $this->kodeGenerator = $kodeGenerator;
        $this->fields = $fields;
    }

    public function collection(Collection $rows)
    {
        $fieldSet = array_flip($this->fields);
        $requiredIfPresent = [
            'nama_aset',
            'subklasifikasiaset_id',
            'spesifikasi_aset',
            'lokasi',
            'format_penyimpanan',
            'masa_berlaku',
            'penyedia_aset',
            'status_aktif',
            'kondisi_aset',
            'kerahasiaan',
            'integritas',
            'ketersediaan',
        ];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // heading on row 1

            $namaAset = trim((string) ($this->val($row, 'nama_aset') ?? ''));

            $subValue = $this->val($row, 'subklasifikasiaset_id');
            $subValue = is_string($subValue) ? trim($subValue) : $subValue;

            if ($this->isFieldRequired('nama_aset', $fieldSet) && $namaAset === '') {
                $this->failures()->push(new \Maatwebsite\Excel\Validators\Failure(
                    $rowNumber,
                    'nama_aset',
                    ['Nama Aset wajib diisi'],
                    $row->toArray()
                ));
                continue;
            }

            if ($this->isFieldRequired('subklasifikasiaset_id', $fieldSet) && ($subValue === null || $subValue === '')) {
                $this->failures()->push(new \Maatwebsite\Excel\Validators\Failure(
                    $rowNumber,
                    'subklasifikasiaset_id',
                    ['Sub Klasifikasi wajib diisi'],
                    $row->toArray()
                ));
                continue;
            }

            $sub = null;
            if ($subValue !== null && $subValue !== '') {
                if (is_numeric($subValue)) {
                    $sub = SubKlasifikasiAset::where('klasifikasi_aset_id', $this->klasifikasiId)
                        ->where('id', (int) $subValue)
                        ->first();
                }

                if (!$sub) {
                    $sub = SubKlasifikasiAset::where('klasifikasi_aset_id', $this->klasifikasiId)
                        ->whereRaw('LOWER(subklasifikasiaset) = ?', [strtolower((string) $subValue)])
                        ->first();
                }
            }

            if (!$sub) {
                $this->failures()->push(new \Maatwebsite\Excel\Validators\Failure(
                    $rowNumber,
                    'subklasifikasiaset_id',
                    ["Sub Klasifikasi '{$subValue}' tidak ditemukan pada klasifikasi ini"],
                    $row->toArray()
                ));
                continue;
            }

            // Validasi kolom yang wajib diisi berdasarkan field konfigurasi
            $missingRequired = [];
            foreach ($requiredIfPresent as $field) {
                if (!$this->isFieldRequired($field, $fieldSet)) {
                    continue;
                }
                if ($field === 'subklasifikasiaset_id') {
                    continue; // sudah dicek di atas
                }
                $value = $this->val($row, $field);
                if ($value === null || trim((string) $value) === '') {
                    $missingRequired[] = $field;
                }
            }

            if (!empty($missingRequired)) {
                $this->failures()->push(new \Maatwebsite\Excel\Validators\Failure(
                    $rowNumber,
                    implode(',', $missingRequired),
                    ['Beberapa kolom wajib masih kosong'],
                    $row->toArray()
                ));
                continue;
            }

            $kodeData = call_user_func($this->kodeGenerator, $this->opdId, $this->kodePrefix);

            $data = [
                'nama_aset' => $namaAset,
                'keterangan' => $this->val($row, 'keterangan'),
                'lokasi' => $this->val($row, 'lokasi'),
                'format_penyimpanan' => $this->val($row, 'format_penyimpanan'),
                'masa_berlaku' => $this->val($row, 'masa_berlaku'),
                'penyedia_aset' => $this->val($row, 'penyedia_aset'),
                'status_aktif' => $this->val($row, 'status_aktif'),
                'spesifikasi_aset' => $this->val($row, 'spesifikasi_aset'),
                'kondisi_aset' => $this->val($row, 'kondisi_aset'),
                'status_personil' => $this->val($row, 'status_personil'),
                'nip_personil' => $this->val($row, 'nip_personil'),
                'jabatan_personil' => $this->val($row, 'jabatan_personil'),
                'fungsi_personil' => $this->val($row, 'fungsi_personil'),
                'unit_personil' => $this->val($row, 'unit_personil'),
                'link_pse' => $this->val($row, 'link_pse'),
                'link_url' => $this->val($row, 'link_url'),
                'kategori_se' => $this->val($row, 'kategori_se'),
            ];

            $allowedData = array_intersect_key($data, $fieldSet);
            unset(
                $allowedData['kerahasiaan'],
                $allowedData['integritas'],
                $allowedData['ketersediaan'],
                $allowedData['subklasifikasiaset_id']
            );

            DB::table('asets')->insert([
                'uuid' => (string) Str::uuid(),
                'aset_key_id' => $kodeData['id'],
                'kode_aset' => $kodeData['kode'],
                'nama_aset' => $namaAset,
                'subklasifikasiaset_id' => $sub->id,
                'klasifikasiaset_id' => $this->klasifikasiId,
                'opd_id' => $this->opdId,
                'periode_id' => $this->periodeId,
                'kerahasiaan' => $this->mapLevel($this->val($row, 'kerahasiaan'), 'cia'),
                'integritas' => $this->mapLevel($this->val($row, 'integritas'), 'cia'),
                'ketersediaan' => $this->mapLevel($this->val($row, 'ketersediaan'), 'cia'),
                'keaslian' => 0,
                'kenirsangkalan' => 0,
                'created_at' => now(),
                'updated_at' => now(),
                ...$allowedData,
            ]);
        }
    }

    private function isFieldRequired(string $field, array $fieldSet): bool
    {
        return array_key_exists($field, $fieldSet);
    }

    private function val($row, string $field)
    {
        $label = AsetFieldLabels::label($field);
        $candidates = [
            $field,
            AsetFieldLabels::slug($field),
            strtolower($label),
            $label,
        ];

        foreach ($candidates as $key) {
            if ($key === null || $key === '') {
                continue;
            }
            if (isset($row[$key])) {
                return $row[$key];
            }
        }

        return null;
    }

    private function mapLevel($value, string $type): int
    {
        $val = is_numeric($value) ? (int) $value : null;
        $text = strtolower(trim((string) $value));

        if ($type === 'cia') {
            if (str_contains($text, 'rendah')) {
                return 1;
            }
            if (str_contains($text, 'sedang')) {
                return 2;
            }
            if (str_contains($text, 'tinggi')) {
                return 3;
            }
            return $val ?? 0;
        }

        // boolean-ish
        return match ($text) {
            'ya', 'iya', 'y' => 1,
            'tidak', 'no', 't', 'n' => 0,
            default => $val ?? 0,
        };
    }
}
