<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtkkaStatusLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ptkka_session_id',
        'from_status',
        'to_status',
        'user_id',
        'catatan',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(PtkkaSession::class, 'ptkka_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
