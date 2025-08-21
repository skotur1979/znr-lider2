<?php

namespace App\Exports;

use App\Models\Fire;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class FiresExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Fire::all()->map(function ($fire) {
            return [
                'Mjesto' => $fire->place,
                'Tip aparata' => $fire->type,
                'Tvor. broj' => $fire->getAttribute('factory_number/year_of_production'),
                'Serijski br.' => $fire->serial_label_number,
                'Servis od' => optional($fire->examination_valid_from)->format('d.m.Y.'),
                'Vrijedi do' => optional($fire->examination_valid_until)->format('d.m.Y.'),
                'Serviser' => $fire->service,
                'Redovni pregled' => optional($fire->regular_examination_valid_from)->format('d.m.Y.'),
                'Vidljivost' => $fire->visible,
                'Nedostatci' => $fire->remark,
                'Otklanjanje' => $fire->action,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Mjesto',
            'Tip aparata',
            'Tvor. broj',
            'Serijski br.',
            'Servis od',
            'Vrijedi do',
            'Serviser',
            'Redovni pregled',
            'Vidljivost',
            'Nedostatci',
            'Otklanjanje',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getAlignment()->setWrapText(true);
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $today = Carbon::today();
        $rowCount = Fire::count() + 1;

        for ($row = 2; $row <= $rowCount; $row++) {
            $cell = "F{$row}";
            $value = $sheet->getCell($cell)->getValue();
            try {
                $date = Carbon::createFromFormat('d.m.Y.', $value);
                if ($date->lt($today)) {
                    $sheet->getStyle($cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('FF6347'); // crveno
                } elseif ($date->lte($today->copy()->addDays(30))) {
                    $sheet->getStyle($cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('FFFF00'); // žuto
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return [];
    }
}
