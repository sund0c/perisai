<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlasifikasiAset extends Model
{
    use HasFactory;
    protected $table = 'klasifikasi_asets'; // ← tambahkan ini
    protected $fillable = ['klasifikasiaset', 'kodeklas'];

    protected $casts = [
        'tampilan_field_aset' => 'array',
    ];

    public function subklasifikasi()
    {
        return $this->hasMany(SubKlasifikasiAset::class, 'klasifikasi_aset_id');
    }

    public function asets()
    {
        return $this->hasMany(Aset::class, 'klasifikasiaset_id');
    }
}
