<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandarIndikator extends Model
{
    protected $table = 'standar_indikator';
    protected $fillable = ['kategori_id', 'indikator', 'tujuan', 'urutan'];

    public function kategori()
    {
        return $this->belongsTo(StandarKategori::class, 'kategori_id');
    }

    public function fungsiStandar()
    {
        return $this->belongsTo(FungsiStandar::class, 'fungsi_standar_id');
    }

    public function rekomendasiStandards()
    {
        return $this->hasMany(RekomendasiStandard::class);
    }
    public function rekomendasi()
    {
        return $this->hasOne(\App\Models\RekomendasiStandard::class, 'standar_indikator_id');
    }
    public function rekomendasis()
    {
        return $this->hasMany(RekomendasiStandard::class, 'standar_indikator_id');
    }
}
