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
            'uid' => (string) Str::uuid(),
            'user_id' => 1,
            'aset_id' => 1,
            'standar_kategori_id' => 2,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('ptkka_sessions')->insert([
            'id' => 2,
            'uid' => (string) Str::uuid(),
            'user_id' => 1,
            'aset_id' => 2,
            'standar_kategori_id' => 2,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('ptkka_sessions')->insert([
            'id' => 3,
            'uid' => (string) Str::uuid(),
            'user_id' => 2,
            'aset_id' => 3,
            'standar_kategori_id' => 2,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('ptkka_sessions')->insert([
            'id' => 4,
            'uid' => (string) Str::uuid(),
            'user_id' => 2,
            'aset_id' => 4,
            'standar_kategori_id' => 2,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
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
                'penjelasanopd' => 'Penjelasan 001',
                'catatanadmin' => null,
                'linkbuktidukung' => 'https://www.url001.com',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        for ($i = 1; $i <= 110; $i++) {
            $jawabanData[] = [
                'ptkka_session_id' => 2,
                'rekomendasi_standard_id' => $i,
                'jawaban' => 1,
                'penjelasanopd' => 'Penjelasan 002',
                'catatanadmin' => null,
                'linkbuktidukung' => 'https://www.url002.com',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        for ($i = 1; $i <= 110; $i++) {
            $jawabanData[] = [
                'ptkka_session_id' => 3,
                'rekomendasi_standard_id' => $i,
                'jawaban' => 1,
                'penjelasanopd' => 'Penjelasan 003',
                'catatanadmin' => null,
                'linkbuktidukung' => 'https://www.url003.com',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        for ($i = 1; $i <= 110; $i++) {
            $jawabanData[] = [
                'ptkka_session_id' => 4,
                'rekomendasi_standard_id' => $i,
                'jawaban' => 2,
                'penjelasanopd' => 'Penjelasan 004',
                'catatanadmin' => null,
                'linkbuktidukung' => 'https://www.url004.com',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('ptkka_jawabans')->insert($jawabanData);
    }
}
