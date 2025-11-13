<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PeriodeSeeder::class,
            //OPDUserSeeder::class,
            KlasifikasiAsetSeeder::class,
            SubKlasifikasiAsetSeeder::class,
            RangeAsetSeeder::class,
            RangeSeSeeder::class,

            //AsetSeeder::class,
            IndikatorKategoriSeSeeder::class,
            KategoriStandardSeeder::class,
            FungsiStandardSeeder::class,
            IndikatorStandardSeeder::class,
            RekomendasiStandardSeeder::class,
            DataPribadiMasterSeeder::class,
            //KategoriSeSeeder::class,
            // PtkkaSessionSeeder::class,
            //AsetbanyakSeeder::class,

            RolePermissionSeeder::class,
            AddDesktopAplikasiSeeder::class
        ]);
    }
}
