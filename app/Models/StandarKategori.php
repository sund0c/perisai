<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandarKategori extends Model
{
    protected $table = 'standar_kategori';

    protected $fillable = ['nama'];
    public function indikator()
    {
        return $this->hasMany(\App\Models\StandarIndikator::class, 'kategori_id');
    }
    public function fungsi()
    {
        return $this->hasMany(FungsiStandar::class, 'kategori_id');
    }
}
