<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriSe extends Model
{
    protected $fillable = ['aset_id', 'jawaban', 'skor_total'];

    protected $casts = [
        'jawaban' => 'array',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class);
    }
}
