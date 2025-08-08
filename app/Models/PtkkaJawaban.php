<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtkkaJawaban extends Model
{
    protected $table = 'ptkka_jawabans';

    protected $fillable = [
        'ptkka_session_id',
        'rekomendasi_standard_id',
        'jawaban',
        'penjelasanopd',
        'catatanadmin',
        'linkbuktidukung',
    ];

    public function session()
    {
        return $this->belongsTo(PtkkaSession::class, 'ptkka_session_id');
    }

    public function rekomendasi()
    {
        return $this->belongsTo(RekomendasiStandard::class, 'rekomendasi_standard_id');
    }
}
