<?php

namespace Database\Seeders;

use App\Models\Aset;
use App\Models\Periode;
use App\Models\Opd;
use App\Models\KlasifikasiAset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AsetbanyakSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // --- VALIDASI DATA DASAR ---
        $periodeId = Periode::where('status', 'open')->value('id');
        if (!$periodeId) {
            $this->command->warn('Tidak ada Periode OPEN. Seeder dilewati.');
            return;
        }

        $opdIds = Opd::pluck('id')->all();
        if (empty($opdIds)) {
            $opdIds = [1];
        }

        $klasList = KlasifikasiAset::with([
            'subklasifikasi' => fn($q) => $q->select('id', 'klasifikasi_aset_id', 'subklasifikasiaset')
        ])->select('id', 'klasifikasiaset')->get();

        if ($klasList->isEmpty()) {
            $this->command->warn('Tabel klasifikasi kosong. Seeder dilewati.');
            return;
        }

        // --- MAPPING + HELPER ---
        $prefixMap = [
            'DATA DAN INFORMASI'   => 'DI',
            'PERANGKAT KERAS'      => 'PK',
            'PERANGKAT LUNAK'      => 'PL',
            'SDM DAN PIHAK KETIGA' => 'SK',
            'SARANA PENDUKUNG'     => 'SP',
        ];

        $lastNumberCache = [];
        $getNextCode = function (string $prefix) use (&$lastNumberCache) {
            if (!array_key_exists($prefix, $lastNumberCache)) {
                $latest = Aset::where('kode_aset', 'like', $prefix . '-%')
                    ->orderBy('kode_aset', 'desc')->value('kode_aset');
                $lastNumberCache[$prefix] = ($latest && preg_match('/^' . $prefix . '\-(\d{5})$/', $latest, $m))
                    ? (int)$m[1] : 0;
            }
            $lastNumberCache[$prefix]++;
            return sprintf('%s-%05d', $prefix, $lastNumberCache[$prefix]);
        };

        $formatPenyimpanan = ['Fisik', 'Dokumen Elektronik', 'Fisik dan Dokumen Elektronik'];
        $statusAktif       = ['Aktif', 'Tidak Aktif'];
        $statusPersonil    = ['SDM', 'Pihak Ketiga'];

        // --- TRANSAKSI & LOOP ---
        DB::beginTransaction();
        try {
            for ($i = 0; $i < 50; $i++) {
                // pilih klasifikasi + sub
                $klas = $klasList->random();
                if ($klas->subklasifikasi->isEmpty()) {
                    $i--;
                    continue;
                }
                $sub = $klas->subklasifikasi->random();

                // prefix & kode
                $namaKlas = strtoupper(trim($klas->klasifikasiaset));
                $prefix   = $prefixMap[$namaKlas] ?? 'XX';
                $kodeAset = $getNextCode($prefix);

                // pilih OPD acak
                $opdId = Arr::random($opdIds);

                // buat entry di aset_keys
                $asetKeyId = DB::table('aset_keys')->insertGetId([
                    'opd_id'     => $opdId,
                    'kode_aset'  => $kodeAset,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // buat entry di asets
                Aset::create([
                    'aset_key_id'           => $asetKeyId,
                    'periode_id'            => $periodeId,
                    'kode_aset'             => $kodeAset,
                    'nama_aset'             => $faker->words(3, true),
                    'keterangan'            => $faker->optional()->sentence(),
                    'klasifikasiaset_id'    => $klas->id,
                    'subklasifikasiaset_id' => $sub->id,
                    'spesifikasi_aset'      => $faker->optional()->sentence(6),
                    'lokasi'                => $faker->city(),
                    'format_penyimpanan'    => $faker->optional()->randomElement($formatPenyimpanan),
                    'opd_id'                => $opdId,
                    'masa_berlaku'          => $faker->optional()->date('Y-m-d'),
                    'penyedia_aset'         => $faker->company(),
                    'status_aktif'          => $faker->randomElement($statusAktif),
                    'kondisi_aset'          => $faker->optional()->randomElement(['Baik']),
                    'kerahasiaan'           => $faker->numberBetween(1, 3),
                    'integritas'            => $faker->numberBetween(1, 3),
                    'ketersediaan'          => $faker->numberBetween(1, 3),
                    'keaslian'              => $faker->numberBetween(1, 3),
                    'kenirsangkalan'        => $faker->numberBetween(1, 3),
                    'kategori_se'           => null,
                    'status_personil'       => $faker->randomElement($statusPersonil),
                    'nip_personil'          => $faker->optional()->numerify('197#########'),
                    'jabatan_personil'      => $faker->optional()->jobTitle(),
                    'fungsi_personil'       => $faker->optional()->jobTitle(),
                    'unit_personil'         => $faker->optional()->companySuffix(),
                    'uuid'                  => (string) Str::uuid(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
