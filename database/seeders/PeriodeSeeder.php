<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('periodes')->insert([
            ['tahun' => 2025, 'status' => 'open','kunci' => 'open', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
