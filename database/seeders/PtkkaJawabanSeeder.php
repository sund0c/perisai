<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PtkkaJawabanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [];

        for ($i = 1; $i <= 110; $i++) {
            $data[] = [
                'ptkka_session_id' => 1,
                'rekomendasi_standard_id' => $i,
                'jawaban' => 2,
                'penjelasanopd' => 'contoh penjelasan',
                'catatanadmin' => null,
                'linkbuktidukung' => 'https://drive.google.com',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('ptkka_jawabans')->insert($data);
    }
}
