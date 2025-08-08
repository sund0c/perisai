<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class KategoriSeSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID aset perangkat lunak (kamu bisa filter by klasifikasi kalau mau)
        $asetIds = DB::table('asets')->take(2)->pluck('id');

        foreach ($asetIds as $asetId) {
            $jawaban = [];

            // Buat struktur jawaban lengkap dari I1 s/d I10
            for ($i = 1; $i <= 10; $i++) {
                $kode = 'I' . $i;
                $jawaban[$kode] = [
                    'jawaban' => 'C',
                    'keterangan' => null,
                ];
            }

            DB::table('kategori_ses')->insert([
                'aset_id' => $asetId,
                'jawaban' => json_encode($jawaban),
                'skor_total' => 10 * 1, // karena semua 'A' → 5 poin * 10 indikator
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
