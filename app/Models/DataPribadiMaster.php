<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPribadiMaster extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_pribadi_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipe',
        'kode',
        'deskripsi',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tipe' => 'string',
    ];

    /**
     * Get all possible values for tipe enum
     *
     * @return array
     */
    public static function getTipeOptions(): array
    {
        return ['spesifik', 'umum'];
    }

    /**
     * Get all possible values for status enum
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return ['aktif', 'nonaktif'];
    }
}
