<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Support\AsetFieldLabels;

class AsetTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    private array $subOptions;
    private array $fields;
    private array $fieldOptions;
    private string $klasifikasiName;

    public function __construct(array $subOptions = [], array $fields = [], array $fieldOptions = [], string $klasifikasiName = '')
    {
        $this->subOptions = $subOptions;
        $this->fields = $fields ?: [
            'nama_aset',
            'subklasifikasiaset_id',
            'keterangan',
            'lokasi',
            'format_penyimpanan',
            'masa_berlaku',
            'kerahasiaan',
            'integritas',
            'ketersediaan',
        ];
        $this->fieldOptions = $fieldOptions;
        $this->klasifikasiName = $klasifikasiName;
    }

    public function collection()
    {
        $row = [];
        foreach ($this->fields as $field) {
            $row[] = $this->sampleValue($field);
        }

        return new Collection([$row]);
    }

    public function headings(): array
    {
        return array_map(fn($f) => AsetFieldLabels::label($f), $this->fields);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $workbook = $sheet->getParent();

                // Hidden sheet for long dropdown values (avoids 255-char limit in validation list)
                $listSheet = $workbook->getSheetByName('lists') ?: $workbook->createSheet();
                $listSheet->setTitle('lists');
                $workbook->setActiveSheetIndex(0); // keep template as active

                $levels          = ['Rendah', 'Sedang', 'Tinggi'];
                $formats         = ['Fisik', 'Dokumen Elektronik', 'Fisik dan Dokumen Elektronik'];
                $statusAktif     = ['Aktif', 'Tidak Aktif'];
                $kondisiAset     = ['Baik', 'Tidak Layak', 'Rusak'];
                $statusPersonil  = ['SDM', 'Pihak Ketiga'];

                $rowStart = 2;
                $rowEnd   = 500;

                $colIndex = array_flip($this->fields); // field => index (0-based)

                $apply = function (?int $idx, array $options) use ($event, $rowStart, $rowEnd) {
                    if ($idx === null) {
                        return;
                    }
                    $col = $this->columnLetter($idx);
                    $this->applyDropdown($event, $col . $rowStart . ':' . $col . $rowEnd, $options);
                };

                $apply($colIndex['subklasifikasiaset_id'] ?? null, $this->subOptions);
                $apply($colIndex['format_penyimpanan'] ?? null, $formats);
                $apply($colIndex['status_aktif'] ?? null, $statusAktif);
                $apply($colIndex['kondisi_aset'] ?? null, $kondisiAset);
                $apply($colIndex['status_personil'] ?? null, $statusPersonil);

                foreach (['kerahasiaan', 'integritas', 'ketersediaan'] as $cia) {
                    $this->applyDropdownFromSheet(
                        $event,
                        $colIndex[$cia] ?? null,
                        $rowStart,
                        $rowEnd,
                        $this->optionsFor($cia) ?: $levels,
                        $listSheet,
                        $cia
                    );
                }

                // Wrap text & row height untuk CIA (label panjang)
                foreach (['kerahasiaan', 'integritas', 'ketersediaan'] as $cia) {
                    if (!isset($colIndex[$cia])) {
                        continue;
                    }
                    $col = $this->columnLetter($colIndex[$cia]);
                    $sheet->getStyle($col . '1:' . $col . $rowEnd)
                        ->getAlignment()
                        ->setWrapText(true);
                }
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(40);
            },
        ];
    }

    private function sampleValue(string $field): string
    {
        $sampleSub = $this->subOptions[0] ?? 'Sub Klasifikasi';
        return match ($field) {
            'nama_aset' => 'Contoh Nama Aset',
            'keterangan' => 'Keterangan opsional',
            'link_pse' => 'https://pse.contoh',
            'subklasifikasiaset_id' => $sampleSub,
            'link_url' => 'https://url.contoh',
            'lokasi' => 'Lokasi opsional',
            'format_penyimpanan' => 'Fisik/Dokumen Elektronik/Fisik dan Dokumen Elektronik',
            'masa_berlaku' => '2025-12-31',
            'kerahasiaan' => $this->optionsFor('kerahasiaan')[0] ?? 'Rendah: ...',
            'integritas' => $this->optionsFor('integritas')[0] ?? 'Rendah: ...',
            'ketersediaan' => $this->optionsFor('ketersediaan')[0] ?? 'Rendah: ...',
            'penyedia_aset' => 'Contoh Penyedia',
            'status_aktif' => 'Aktif/Tidak Aktif',
            'spesifikasi_aset' => 'Spesifikasi singkat',
            'kondisi_aset' => 'Baik/Tidak Layak/Rusak',
            'status_personil' => 'SDM/Pihak Ketiga',
            'nip_personil' => '1980xxxxxxxx',
            'jabatan_personil' => 'Jabatan Contoh',
            'fungsi_personil' => 'Fungsi Contoh',
            'unit_personil' => 'Unit Contoh',
            default => '',
        };
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter   = chr($index % 26 + 65) . $letter;
            $index    = intdiv($index, 26) - 1;
        }
        return $letter;
    }

    private function optionsFor(string $field): array
    {
        $labels = [];

        if (isset($this->fieldOptions[$field])) {
            $labels = array_values(array_filter(array_map(function ($opt) {
                return $opt['label'] ?? null;
            }, $this->fieldOptions[$field])));
        }

        if (empty($labels)) {
            $cfg = config("aset_fields.$field." . $this->klasifikasiName)
                ?? config("aset_fields.$field._DEFAULT_")
                ?? [];
            $labels = array_values(array_filter(array_map(fn($opt) => $opt['label'] ?? null, $cfg)));
        }

        return $labels;
    }

    private function applyDropdown(AfterSheet $event, string $cells, array $options): void
    {
        if (empty($options)) {
            return;
        }
        $sheet = $event->sheet->getDelegate();

        [$start, $end] = explode(':', $cells);
        $parse = function (string $coord) {
            preg_match('/([A-Z]+)(\d+)/', $coord, $m);
            return [$m[1] ?? 'A', (int) ($m[2] ?? 1)];
        };
        [$colStart, $rowStart] = $parse($start);
        [, $rowEnd] = $parse($end);

        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

        for ($row = $rowStart; $row <= $rowEnd; $row++) {
            $coord = $colStart . $row;
            $sheet->getCell($coord)->setDataValidation(clone $validation);
        }
    }

    private function applyDropdownFromSheet(
        AfterSheet $event,
        ?int $idx,
        int $rowStart,
        int $rowEnd,
        array $options,
        $listSheet,
        string $key
    ): void {
        if ($idx === null || empty($options)) {
            return;
        }

        $col = $this->columnLetter($idx);
        $sheet = $event->sheet->getDelegate();

        // Write options to hidden sheet
        $listCol = $this->columnLetter($idx); // reuse column index to avoid collision
        foreach ($options as $i => $opt) {
            $listSheet->setCellValue($listCol . ($i + 1), $opt);
        }

        $range = "'lists'!$" . $listCol . '$1:$' . $listCol . '$' . count($options);

        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1($range);

        for ($row = $rowStart; $row <= $rowEnd; $row++) {
            $coord = $col . $row;
            $sheet->getCell($coord)->setDataValidation(clone $validation);
        }

        // Hide lists sheet
        $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
    }
}
