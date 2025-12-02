<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Support\AsetFieldLabels;

class AsetExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected Collection $asets;
    protected array $fields;
    protected string $klasifikasiName;
    protected array $subOptions;
    protected array $fieldOptions;

    public function __construct(Collection $asets, array $fields, string $klasifikasiName, array $subOptions = [], array $fieldOptions = [])
    {
        $this->asets = $asets;
        $this->fields = $fields;
        $this->klasifikasiName = $klasifikasiName;
        $this->subOptions = $subOptions;
        $this->fieldOptions = $fieldOptions;
    }

    public function collection()
    {
        return $this->asets;
    }

    public function headings(): array
    {
        return array_map(fn($f) => AsetFieldLabels::label($f), $this->fields);
    }

    public function map($aset): array
    {
        $row = [];
        foreach ($this->fields as $field) {
            $row[] = $this->valueForField($aset, $field);
        }
        return $row;
    }

    private function valueForField($aset, string $field)
    {
        switch ($field) {
            case 'subklasifikasiaset_id':
                return optional($aset->subklasifikasiaset)->subklasifikasiaset ?? '';
            case 'kerahasiaan':
            case 'integritas':
            case 'ketersediaan':
                return $this->ciaLabel($field, $aset->{$field} ?? null);
            default:
                return $aset->{$field} ?? '';
        }
    }

    private function ciaLabel(string $field, $value): string
    {
        $options = config("aset_fields.$field." . $this->klasifikasiName)
            ?? config("aset_fields.$field._DEFAULT_")
            ?? [];

        foreach ($options as $opt) {
            if ((string) ($opt['value'] ?? '') === (string) $value) {
                return $opt['label'] ?? (string) $value;
            }
        }

        return (string) $value;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $workbook = $sheet->getParent();

                $listSheet = $workbook->getSheetByName('lists') ?: $workbook->createSheet();
                $listSheet->setTitle('lists');
                $workbook->setActiveSheetIndex(0);

                $formats        = ['Fisik', 'Dokumen Elektronik', 'Fisik dan Dokumen Elektronik'];
                $statusAktif    = ['Aktif', 'Tidak Aktif'];
                $kondisiAset    = ['Baik', 'Tidak Layak', 'Rusak'];
                $statusPersonil = ['SDM', 'Pihak Ketiga'];

                $rowStart = 2;
                $rowEnd   = max($this->asets->count() + 10, 200);

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
                        $this->optionsFor($cia),
                        $listSheet
                    );
                }

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
            },
        ];
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

    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intdiv($index, 26) - 1;
        }
        return $letter;
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
        $listSheet
    ): void {
        if ($idx === null || empty($options)) {
            return;
        }

        $col = $this->columnLetter($idx);
        $sheet = $event->sheet->getDelegate();

        $listCol = $col; // reuse column index to avoid collision
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

        $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
    }
}
