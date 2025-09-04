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
            'name' => 'Nama user di Inspektorat',
            'email' => 'inspektorat@baliprov.go.id',
            'role' => 'opd',
            'opd_id' => $inspektorat->id,
        ]);

        User::create([
            'name' => 'Nama user di Diskominfos',
            'email' => 'diskominfos@baliprov.go.id',
            'role' => 'opd',
            'opd_id' => $diskominfos->id,
        ]);

        User::create([
            'name' => 'Nama staf Bidang 4',
            'email' => 'persandian@baliprov.go.id',
            'role' => 'bidang',
            'opd_id' => $diskominfos->id,
        ]);

        User::create([
            'name' => 'Nama admin Sistem',
            'email' => 'admin@baliprov.go.id',
            'role' => 'admin',
            'opd_id' => $diskominfos->id,
        ]);
    }
}
