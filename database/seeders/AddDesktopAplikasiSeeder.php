<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KlasifikasiAset;
use App\Models\SubKlasifikasiAset;

class AddDesktopAplikasiSeeder extends Seeder
{
    public function run(): void
    {
        $perangkatLunak = KlasifikasiAset::where('klasifikasiaset', 'Perangkat Lunak')->first();

        SubKlasifikasiAset::create([
            'klasifikasi_aset_id' => $perangkatLunak->id,
            'subklasifikasiaset' => 'Aplikasi berbasis Desktop',
            'penjelasan' => 'Perangkat lunak yang diakses melalui desktop'
        ]);
    }
}
