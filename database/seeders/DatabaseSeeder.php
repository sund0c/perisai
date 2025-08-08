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
            OPDUserSeeder::class,
            KlasifikasiAsetSeeder::class,
            SubKlasifikasiAsetSeeder::class,
            RangeAsetSeeder::class,
            RangeSeSeeder::class,
            PeriodeSeeder::class,
            AsetSeeder::class,
            IndikatorKategoriSeSeeder::class,
            KategoriStandardSeeder::class,
            FungsiStandardSeeder::class,
            IndikatorStandardSeeder::class,
            RekomendasiStandardSeeder::class,
            KategoriSeSeeder::class,
            PtkkaSessionSeeder::class,


        ]);
    }
}
