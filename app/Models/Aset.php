<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Aset extends Model
{
    use HasFactory;

    // Primary key tetap bigint auto-increment (aman untuk FK)
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Kolom yang boleh diisi mass assignment.
     * (uuid jangan diisi manual—di-generate otomatis di booted())
     */
    protected $fillable = [
        'periode_id',
        'kode_aset',
        'nama_aset',
        'klasifikasiaset_id',
        'subklasifikasiaset_id',
        'spesifikasi_aset',
        'lokasi',
        'format_penyimpanan',
        'opd_id',
        'masa_berlaku',
        'penyedia_aset',
        'status_aktif',
        'kondisi_aset',
        'kerahasiaan',
        'integritas',
        'ketersediaan',
        'keaslian',
        'kenirsangkalan',
        'kategori_se',
        'status_personil',
        'nip_personil',
        'jabatan_personil',
        'fungsi_personil',
        'unit_personil',
        'keterangan',
        'link_pse',
        'aset_key_id',
    ];

    /**
     * Casting tipe data untuk konsistensi.
     */
    protected $casts = [
        'kerahasiaan'   => 'integer',
        'integritas'    => 'integer',
        'ketersediaan'  => 'integer',
        'keaslian'      => 'integer',
        'kenirsangkalan' => 'integer',
        // kalau enum disimpan sebagai string, biarkan default (string).
        // bisa juga buat Enum cast sendiri kalau perlu.
    ];

    /**
     * Auto-generate UUID saat create.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // (Opsional) Global scope multi-tenant:
        // static::addGlobalScope('opd', function ($q) {
        //     if (auth()->check()) {
        //         $q->where($q->getModel()->getTable().'.opd_id', auth()->user()->opd_id);
        //     }
        // });
    }

    /**
     * Gunakan UUID untuk route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /* ============================
     *           RELASI
     * ============================ */

    // Periode (tahun anggaran)
    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    // Klasifikasi Aset
    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiAset::class, 'klasifikasiaset_id');
    }

    // Sub Klasifikasi Aset
    public function subklasifikasiaset()
    {
        return $this->belongsTo(SubKlasifikasiAset::class, 'subklasifikasiaset_id');
    }

    // OPD pemilik aset
    public function opd()
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    // Nilai/objek kategori SE untuk aset (jika tabel kategori_ses punya aset_id)
    public function kategoriSe()
    {
        return $this->hasOne(KategoriSe::class, 'aset_id', 'id');
    }

    // Nilai/objek Vitaliyas SE untuk aset
    public function vitalitasSe()
    {
        return $this->hasOne(VitalitasSe::class, 'aset_id', 'id');
    }

    // Semua sesi PTKKA milik aset
    public function ptkkaSessions()
    {
        return $this->hasMany(PtkkaSession::class, 'aset_id', 'id');
    }

    // Sesi PTKKA terbaru dengan status = 1 (Pengajuan)
    public function ptkkaPengajuan()
    {
        return $this->hasOne(PtkkaSession::class, 'aset_id', 'id')
            ->where('status', 1)
            ->latestOfMany('updated_at')
            ->select('ptkka_sessions.*');
    }

    // Sesi PTKKA terbaru dengan status dalam {0,2,3}
    public function ptkkaTerakhir()
    {
        return $this->hasOne(PtkkaSession::class, 'aset_id', 'id')
            ->whereIn('status', [0, 2, 3])
            ->latestOfMany('updated_at')
            ->select('ptkka_sessions.*');
    }

    // Sesi PTKKA terbaru dengan status = 4 (Rampung)
    public function ptkkaTerakhirRampung()
    {
        return $this->hasOne(PtkkaSession::class, 'aset_id', 'id')
            ->where('status', 4)
            ->latestOfMany('updated_at')
            ->select('ptkka_sessions.*');
    }

    /* ============================
     *           SCOPES
     * ============================ */

    /**
     * Scope bantu untuk filter ke OPD user aktif (dipanggil eksplisit).
     * Gunakan ini kalau tidak mengaktifkan Global Scope.
     */
    public function scopeOwned($query, $user = null)
    {
        $user = $user ?: auth()->user();
        return $query->where($this->getTable() . '.opd_id', $user?->opd_id ?? 0);
    }
}
