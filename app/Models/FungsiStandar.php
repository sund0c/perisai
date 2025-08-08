<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FungsiStandar extends Model
{
    protected $table = 'fungsi_standar';
    protected $fillable = ['kategori_id', 'nama', 'urutan'];

    public function kategori()
    {
        return $this->belongsTo(StandarKategori::class, 'kategori_id');
    }

    public function indikator()
    {
        return $this->hasMany(StandarIndikator::class, 'fungsi_standar_id');
    }

    public function indikators()
    {
        return $this->hasMany(StandarIndikator::class, 'fungsi_standar_id');
    }
}
