<?php

namespace App\Exports;

use App\Models\Miscellaneous;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MiscellaneousExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $data;

    public function collection(): Collection
    {
        $this->data = Miscellaneous::withTrashed()->get();

        return $this->data->map(function ($record) {
            return [
                'Naziv' => $record->name,
                'Kategorija' => optional($record->category)->name ?? '',
                'Ispitao' => $record->examiner,
                'Broj izvještaja' => $record->report_number,
                'Vrijedi od' => optional($record->examination_valid_from)->format('d.m.Y.'),
                'Vrijedi do' => optional($record->examination_valid_until)->format('d.m.Y.'),
                'Napomena' => $record->remark,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Naziv',
            'Kategorija',
            'Ispitao',
            'Broj izvještaja',
            'Vrijedi od',
            'Vrijedi do',
            'Napomena',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(35);
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $startRow = 2;
                foreach ($this->data as $index => $record) {
                    $row = $index + $startRow;
                    $validUntil = $record->examination_valid_until;

                    if (!$validUntil) {
                        continue;
                    }

                    $today = Carbon::today();
                    $diffDays = $today->diffInDays($validUntil, false);

                    $fillColor = null;

                    if ($diffDays < 0) {
                        $fillColor = 'FF6347'; // crveno
                    } elseif ($diffDays <= 30) {
                        $fillColor = 'FFFF00'; // žuto
                    }

                    if ($fillColor) {
                        $sheet->getStyle("F{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
                    }
                }
            }
        ];
    }
}
