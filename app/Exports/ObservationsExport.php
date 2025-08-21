<?php

namespace App\Exports;

use App\Models\Observation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ObservationsExport implements FromCollection, WithHeadings, WithStyles, WithDrawings
{
    protected Collection $observations;
    protected array $drawings = [];

    public function __construct()
    {
        $this->observations = Observation::all();

        foreach ($this->observations as $index => $observation) {
            if ($observation->picture_path && file_exists(public_path('storage/' . $observation->picture_path))) {
                $drawing = new Drawing();
                $drawing->setName('Slika');
                $drawing->setPath(public_path('storage/' . $observation->picture_path));
                $drawing->setHeight(60);
                $drawing->setCoordinates('K' . ($index + 2));
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $this->drawings[] = $drawing;
            }
        }
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->observations as $observation) {
            $statusHr = match ($observation->status) {
                'Not started' => 'Nije započeto',
                'In progress' => 'U tijeku',
                'Complete' => 'Završeno',
                default => $observation->status,
            };

            $rows[] = [
                $observation->incident_date ? Carbon::parse($observation->incident_date)->format('d.m.Y.') : '',
                $observation->observation_type,
                $observation->location,
                $observation->item,
                $observation->potential_incident_type,
                $observation->action,
                $observation->responsible,
                $observation->target_date ? Carbon::parse($observation->target_date)->format('d.m.Y.') : '',
                $statusHr,
                $observation->comments,
                ' ',
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Datum',
            'Vrsta zapažanja',
            'Lokacija',
            'Opis',
            'Vrsta opasnosti',
            'Potrebna radnja',
            'Odgovorna osoba',
            'Rok za provedbu',
            'Status',
            'Komentar',
            'Slika',
        ];
    }

    public function styles(Worksheet $sheet)
{
    $sheet->getStyle('A1:K1')->getFont()->setBold(true);

    $columns = ['A' => 13, 'B' => 18, 'C' => 15, 'D' => 20, 'E' => 28, 'F' => 25, 'G' => 22, 'H' => 15, 'I' => 15, 'J' => 25, 'K' => 18];
    foreach ($columns as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }

    $today = Carbon::today();
    for ($i = 2; $i <= $this->observations->count() + 1; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(60);

        // 🎯 Rok za provedbu (H)
        $targetCell = 'H' . $i;
        $value = $sheet->getCell($targetCell)->getValue();
        try {
            $date = Carbon::createFromFormat('d.m.Y.', $value);
            if ($date->lt($today)) {
                $sheet->getStyle($targetCell)->getFill()->setFillType('solid')->getStartColor()->setARGB('FF6347');
            } elseif ($date->lte($today->copy()->addDays(30))) {
                $sheet->getStyle($targetCell)->getFill()->setFillType('solid')->getStartColor()->setARGB('FFFF00');
            }
        } catch (\Exception $e) {
            // nije datum
        }

        // 🟢 Status (I)
        $statusCell = 'I' . $i;
        $status = $sheet->getCell($statusCell)->getValue();

        if ($status === 'Nije započeto') {
            $sheet->getStyle($statusCell)->getFill()->setFillType('solid')->getStartColor()->setARGB('FF6347');
        } elseif ($status === 'U tijeku') {
            $sheet->getStyle($statusCell)->getFill()->setFillType('solid')->getStartColor()->setARGB('FFFF00');
        } elseif ($status === 'Završeno') {
            $sheet->getStyle($statusCell)->getFill()->setFillType('solid')->getStartColor()->setARGB('9ACD32');
        }
    }
}

    public function drawings(): array
    {
        return $this->drawings;
    }
}


