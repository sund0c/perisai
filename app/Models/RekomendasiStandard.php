<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekomendasiStandard extends Model
{
    protected $fillable = ['standar_indikator_id', 'rekomendasi', 'buktidukung'];

    public function fungsi() // <- ini yang dibutuhkan oleh 'indikator.fungsi'
    {
        return $this->belongsTo(FungsiStandar::class, 'fungsi_standar_id');
    }


    public function indikator()
    {
        return $this->belongsTo(StandarIndikator::class, 'standar_indikator_id');
    }

    public function jawabans()
    {
        return $this->hasMany(PtkkaJawaban::class, 'rekomendasi_standard_id');
    }
}
