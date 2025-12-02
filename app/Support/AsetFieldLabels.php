<?php

namespace App\Support;

use Illuminate\Support\Str;

class AsetFieldLabels
{
    public static function map(): array
    {
        return [
            'nama_aset'               => 'Nama Aset',
            'subklasifikasiaset_id'   => 'Sub Klasifikasi Aset',
            'keterangan'              => 'Keterangan / Fungsi',
            'lokasi'                  => 'Lokasi',
            'format_penyimpanan'      => 'Format Penyimpanan',
            'masa_berlaku'            => 'Masa Berlaku',
            'kerahasiaan'             => 'Tingkat Kerahasiaan (C)',
            'integritas'              => 'Tingkat Integritas (I)',
            'ketersediaan'            => 'Tingkat Ketersediaan (A)',
            'link_pse'                => 'Link PSE',
            'link_url'                => 'Link URL',
            'penyedia_aset'           => 'Penyedia Aset',
            'status_aktif'            => 'Status Aktif',
            'spesifikasi_aset'        => 'Spesifikasi Aset',
            'kondisi_aset'            => 'Kondisi Aset',
            'status_personil'         => 'Status Personil',
            'nip_personil'            => 'Nama Personil',
            'jabatan_personil'        => 'Jabatan Personil',
            'fungsi_personil'         => 'Bidang/Bagian',
            'unit_personil'           => 'Seksi/Tim',
            'kategori_se'             => 'Kategori Se',
            'periode_id'              => 'Periode Id',
            'kode_aset'               => 'Kode Aset',
            'klasifikasiaset_id'      => 'Klasifikasiaset Id',
            'opd_id'                  => 'Opd Id',
            'keaslian'                => 'Tingkat Keaslian (N/A)',
            'kenirsangkalan'          => 'Tingkat Kenirsangkalan (N/A)',
        ];
    }

    public static function label(string $field): string
    {
        return static::map()[$field] ?? $field;
    }

    public static function slug(string $field): string
    {
        return Str::slug(static::label($field), '_');
    }
}
