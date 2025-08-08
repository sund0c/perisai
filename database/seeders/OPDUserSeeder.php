<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Opd;
use Illuminate\Support\Facades\Hash;


class OPDUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diskominfos = Opd::create(['namaopd' => 'DISKOMINFOS']);
        $inspektorat = Opd::create(['namaopd' => 'INSPEKTORAT']);

        User::create([
            'name' => 'Nama PIC Inspektorat',
            'email' => 'inspektorat@baliprov.go.id',
            'password' => Hash::make('Password123@'),
            'role' => 'opd',
            'opd_id' => $inspektorat->id,
        ]);

        User::create([
            'name' => 'Nama PIC Diskominfos',
            'email' => 'diskominfos@baliprov.go.id',
            'password' => Hash::make('Password123@'),
            'role' => 'opd',
            'opd_id' => $diskominfos->id,
        ]);

        User::create([
            'name' => 'Nama ADMIN Sistem',
            'email' => 'admindiskominfos@baliprov.go.id',
            'password' => Hash::make('Password123@'),
            'role' => 'admin',
            'opd_id' => $diskominfos->id,
        ]);
    }
}
