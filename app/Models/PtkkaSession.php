<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtkkaSession extends Model
{
    protected $table = 'ptkka_sessions';

    protected $fillable = [
        'user_id',
        'aset_id',
        'standar_kategori_id',
        'is_closed',
        'uid'
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategori()
    {
        return $this->belongsTo(StandarKategori::class, 'standar_kategori_id');
    }

    public function jawabans()
    {
        return $this->hasMany(PtkkaJawaban::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(\App\Models\PtkkaStatusLog::class);
    }

    public function latestStatusLog()
    {
        return $this->hasOne(\App\Models\PtkkaStatusLog::class)->latestOfMany('changed_at');
    }
}
