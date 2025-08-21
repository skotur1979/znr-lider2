<?php

namespace App\Exports;

use App\Models\FirstAidKit;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FirstAidKitsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $dateColumnIndexes = [];

    public function collection(): Collection
    {
        $kits = FirstAidKit::with('items')->get();
        $rows = collect();
        $rowIndex = 2; // počinje od 2 jer je 1 heading

        foreach ($kits as $kit) {
            foreach ($kit->items as $item) {
                $validUntil = $item->valid_until ? Carbon::parse($item->valid_until) : null;

                // Spremi redni broj reda + validUntil za obojavanje kasnije
                $this->dateColumnIndexes[] = [
                    'row' => $rowIndex,
                    'date' => $validUntil,
                ];
                $rowIndex++;

                $rows->push([
                    'location'      => $kit->location,
                    'inspected_at'  => $kit->inspected_at ? Carbon::parse($kit->inspected_at)->format('d.m.Y.') : '',
                    'note'          => $kit->note ?? '',
                    'material_type' => $item->material_type,
                    'purpose'       => $item->purpose,
                    'valid_until'   => $validUntil ? $validUntil->format('d.m.Y.') : '',
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Lokacija ormarića',
            'Pregled obavljen',
            'Napomena',
            'Vrsta materijala',
            'Namjena',
            'Vrijedi do',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Širine stupaca
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(16);

                // Bojanje datuma prema roku
                foreach ($this->dateColumnIndexes as $entry) {
                    $row = $entry['row'];
                    $date = $entry['date'];
                    $color = null;

                    if ($date) {
                        $today = now();
                        if ($date->isPast()) {
                            $color = 'FF6347'; // crveno
                        } elseif ($date->diffInDays($today) <= 30) {
                            $color = 'FFFF00'; // žuto
                        }
                    }

                    if ($color) {
                        $sheet->getStyle("F{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB(substr($color, 2));
                    }
                }
            },
        ];
    }
}


