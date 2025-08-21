<?php

namespace App\Exports;

use App\Models\Machine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class MachinesExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Machine::all()->map(function ($item) {
            return [
                'Naziv' => $item->name,
                'Proizvođač' => $item->manufacturer,
                'Tvornički broj' => $item->factory_number,
                'Inventarni broj' => $item->inventory_number,
                'Datum ispitivanja' => optional($item->examination_valid_from)->format('d.m.Y.'),
                'Ispitivanje vrijedi do' => optional($item->examination_valid_until)->format('d.m.Y.'),
                'Ispitao' => $item->examined_by,
                'Broj izvještaja' => $item->report_number,
                'Lokacija' => $item->location,
                'Napomena' => $item->note,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Naziv',
            'Proizvođač',
            'Tvornički broj',
            'Inventarni broj',
            'Datum ispitivanja',
            'Ispitivanje vrijedi do',
            'Ispitao',
            'Broj izvještaja',
            'Lokacija',
            'Napomena',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setWrapText(true);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $today = Carbon::today();
        $rowCount = Machine::count() + 1;

        // Kolona F: "Ispitivanje vrijedi do"
        for ($row = 2; $row <= $rowCount; $row++) {
            $cell = "F{$row}";
            $value = $sheet->getCell($cell)->getValue();

            try {
                $date = Carbon::createFromFormat('d.m.Y.', $value);
                if ($date->lt($today)) {
                    $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FF6347');
                } elseif ($date->lte($today->copy()->addDays(30))) {
                    $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFFF00');
                }
            } catch (\Exception $e) {
                continue;
            }
        }


        return [];
    }
}

