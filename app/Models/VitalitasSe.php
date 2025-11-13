<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VitalitasSe extends Model
{
    protected $fillable = ['aset_id', 'jawaban', 'skor_total', 'uuid'];

    protected $casts = [
        'jawaban' => 'array',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }


    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
