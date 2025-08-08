<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangeSe extends Model
{
    use HasFactory;

    protected $table = 'range_ses';

    protected $fillable = [
        'nilai_akhir_aset',
        'warna_hexa',
        'nilai_bawah',
        'nilai_atas',
        'deskripsi',
    ];
}
