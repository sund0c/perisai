<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKlasifikasiAset extends Model
{
    use HasFactory;
    protected $table = 'sub_klasifikasi_asets';
    protected $fillable = ['klasifikasi_aset_id', 'subklasifikasiaset', 'penjelasan'];
    

    public function klasifikasi()
    {
        // return $this->belongsTo(KlasifikasiAset::class);
        return $this->belongsTo(KlasifikasiAset::class, 'klasifikasi_aset_id');
    }
}
