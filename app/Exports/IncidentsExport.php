<?php

namespace App\Exports;

use App\Models\Incident;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class IncidentsExport implements FromCollection, WithHeadings, WithStyles, WithDrawings
{
    protected Collection $records;
    protected array $drawings = [];

    public function __construct()
    {
        $this->records = Incident::all();

        foreach ($this->records as $index => $incident) {
            if ($incident->image_path && file_exists(public_path('storage/' . $incident->image_path))) {
                $drawing = new Drawing();
                $drawing->setName('Slika');
                $drawing->setPath(public_path('storage/' . $incident->image_path));
                $drawing->setHeight(60);
                $drawing->setCoordinates('K' . ($index + 2)); // redak +1 zbog zaglavlja
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $this->drawings[] = $drawing;
            }
        }
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->records as $incident) {
            $rows[] = [
                $incident->location,
                $incident->type_of_incident,
                $incident->permanent_or_temporary === 'Permanent' ? 'Stalni' : 'Privremeni',
                $incident->date_occurred ? Carbon::parse($incident->date_occurred)->format('d.m.Y.') : '',
                $incident->date_of_return ? Carbon::parse($incident->date_of_return)->format('d.m.Y.') : '',
                $incident->working_days_lost,
                $incident->causes_of_injury,
                $incident->accident_injury_type,
                $incident->injured_body_part,
                $incident->other,
                ' ', // Placeholder za sliku
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Lokacija',
            'Vrsta incidenta',
            'Vrsta zaposlenja',
            'Datum nastanka',
            'Datum povratka na posao',
            'Izgubljeni radni dani',
            'Uzrok ozljede',
            'Tip ozljede',
            'Ozlijeđeni dio tijela',
            'Napomena',
            'Slika',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(18);

        for ($i = 2; $i <= $this->records->count() + 1; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(65); // visina ćelije za sliku
        }
    }

    public function drawings(): array
    {
        return $this->drawings;
    }
}


