<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PtkkaSessionSeeder extends Seeder
{
    public function run(): void
    {
        // ========== PTKKA SESSION ==========
        DB::table('ptkka_sessions')->insert([
            'id' => 1,
            'uid' => '7b243003-7e0d-4a6a-a0cc-c8e87c9a3216',
            'user_id' => 1,
            'aset_id' => 1,
            'standar_kategori_id' => 2,
            'status' => 0,
            'created_at' => '2025-08-07 18:21:40',
            'updated_at' => '2025-08-07 18:21:40',
        ]);

        // ========== PTKKA JAWABANS ==========
        $createdAt = Carbon::parse('2025-08-07 06:37:22');
        $updatedAt = Carbon::parse('2025-08-07 06:37:22');

        $jawabanData = [];

        for ($i = 1; $i <= 110; $i++) {
            $jawabanData[] = [
                'ptkka_session_id' => 1,
                'rekomendasi_standard_id' => $i,
                'jawaban' => 2,
                'penjelasanopd' => 'contoh penjelasan OPD',
                'catatanadmin' => null,
                'linkbuktidukung' => 'https://google.com',
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        }

        DB::table('ptkka_jawabans')->insert($jawabanData);
    }
}
