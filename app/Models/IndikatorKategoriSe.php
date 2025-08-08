<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorKategoriSe extends Model
{
    protected $table = 'indikator_kategorises';

    protected $fillable = [
        'kode',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'nilai_a',
        'nilai_b',
        'nilai_c',
        'urutan',
    ];
}
