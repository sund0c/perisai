<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait CalculatesAsetRange
{
    /**
     * Hitung total CIAAA dan tempel nilai akhir + warna ke setiap aset.
     *
     * @param Collection $asets   Koleksi model Aset (atau objek serupa)
     * @param Collection $ranges  Koleksi RangeAset yang sudah diurutkan
     */
    private function applyRangeAttributes(Collection $asets, Collection $ranges): void
    {

        foreach ($asets as $aset) {
            // Map bobot: 1 => 1, 2 => 5, 3 => 15. Keaslian & kenirsangkalan diabaikan (0).
            $score = static fn(int $v): int => match ($v) {
                1 => 1,
                2 => 5,
                3 => 15,
                default => 0,
            };

            $total = $score((int) $aset->kerahasiaan)
                + $score((int) $aset->integritas)
                + $score((int) $aset->ketersediaan);

            $match = $ranges->first(fn($r) => $r->nilai_bawah <= $total && $r->nilai_atas >= $total);

            $aset->nilai_akhir_aset = $match->nilai_akhir_aset ?? '-';
            $aset->warna_hexa       = $match->warna_hexa ?? '#999999';
        }
    }

    protected function badgeCIA($nilai): string
    {
        $label = [
            '1' => 'R',
            '2' => 'S',
            '3' => 'T',
        ][$nilai] ?? '-';

        $warna = [
            '1' => 'background-color:#28a745;color:#fff;', // Hijau
            '2' => 'background-color:#ffc107;color:#000;', // Kuning
            '3' => 'background-color:#dc3545;color:#fff;', // Merah
        ][$nilai] ?? 'background-color:#ccc;color:#000;';

        return '<span style="padding:3px 8px; border-radius:4px; font-weight:bold; ' .
            $warna .
            '">' .
            $label .
            '</span>';
    }




    /**
     * Tambahkan atribut range lalu hitung ringkasan jumlah per kategori (TINGGI/SEDANG/RENDAH).
     *
     * @return array{tinggi:int, sedang:int, rendah:int}
     */
    private function summarizeRangeCounts(Collection $asets, Collection $ranges): array
    {
        $this->applyRangeAttributes($asets, $ranges);

        $counts = [
            'TINGGI' => 0,
            'SEDANG' => 0,
            'RENDAH' => 0,
        ];

        foreach ($asets as $aset) {
            $key = strtoupper((string) ($aset->nilai_akhir_aset ?? ''));
            if (isset($counts[$key])) {
                $counts[$key]++;
            }
        }

        return [
            'tinggi' => $counts['TINGGI'],
            'sedang' => $counts['SEDANG'],
            'rendah' => $counts['RENDAH'],
        ];
    }
}
