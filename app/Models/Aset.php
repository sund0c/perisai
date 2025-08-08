<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_id',
        'kode_aset',
        'nama_aset',
        'klasifikasiaset_id',
        'subklasifikasiaset_id',
        'spesifikasi_aset',
        'lokasi',
        'format_penyimpanan',
        'opd_id',
        'masa_berlaku',
        'penyedia_aset',
        'status_aktif',
        'kondisi_aset',
        'kerahasiaan',
        'integritas',
        'ketersediaan',
        'keaslian',
        'kenirsangkalan',
        'kategori_se',
        'status_personil',
        'nip_personil',
        'jabatan_personil',
        'fungsi_personil',
        'unit_personil',
        'keterangan',
    ];

    // Relasi ke Periode (Tahun Anggaran)
    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    // Relasi ke Klasifikasi Aset
    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiAset::class, 'klasifikasiaset_id');
    }


    public function subklasifikasiaset()
    {
        return $this->belongsTo(SubKlasifikasiAset::class, 'subklasifikasiaset_id');
    }


    // Relasi ke OPD
    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function kategoriSe()
    {
        return $this->hasOne(\App\Models\KategoriSe::class);
    }

    public function ptkkaSessions()
    {
        return $this->hasMany(PtkkaSession::class);
    }

    public function ptkkaTerakhir()
    {
        return $this->hasOne(PtkkaSession::class)->latestOfMany();
    }
}
