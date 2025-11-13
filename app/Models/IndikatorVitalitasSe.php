<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorVitalitasSe extends Model
{
    protected $table = 'indikator_vitalitasses';

    protected $fillable = [
        'kode',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'nilai_a',
        'nilai_b',
        'nilai_c',
        'nilai_d',
        'urutan',
    ];
}
