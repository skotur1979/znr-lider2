<?php

namespace App\Exports;

use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EmployeesExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public function collection()
    {
        return Employee::with('certificates')->get()->map(function ($e) {
            return [
                $e->name,
                // ➕ NOVO
                $e->job_title,
                $e->education,
                $e->place_of_birth,
                $e->name_of_parents,
                $e->address,
                $e->gender,
                $e->OIB,
                $e->phone,
                $e->email,
                $e->workplace,
                $e->organization_unit,
                $e->contract_type,
                optional($e->employeed_at)?->format('d.m.Y.'),
                optional($e->contract_ended_at)?->format('d.m.Y.'),
                optional($e->medical_examination_valid_from)?->format('d.m.Y.'),
                optional($e->medical_examination_valid_until)?->format('d.m.Y.'),
                $e->article,
                optional($e->occupational_safety_valid_from)?->format('d.m.Y.'),
                optional($e->fire_protection_valid_from)?->format('d.m.Y.'),
                optional($e->fire_protection_statement_at)?->format('d.m.Y.'),
                optional($e->evacuation_valid_from)?->format('d.m.Y.'),
                optional($e->first_aid_valid_from)?->format('d.m.Y.'),
                optional($e->first_aid_valid_until)?->format('d.m.Y.'),
                optional($e->toxicology_valid_from)?->format('d.m.Y.'),
                optional($e->toxicology_valid_until)?->format('d.m.Y.'),
                optional($e->employers_authorization_valid_from)?->format('d.m.Y.'),
                optional($e->employers_authorization_valid_until)?->format('d.m.Y.'),
                $e->certificates->map(function ($c) {
                    $od = $c->valid_from ? Carbon::parse($c->valid_from)->format('d.m.Y.') : '-';
                    $do = $c->valid_until ? Carbon::parse($c->valid_until)->format('d.m.Y.') : '-';
                    return "{$od} - {$do}";
                })->implode("\n"),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Ime i prezime',
            // ➕ NOVO
            'Zanimanje',
            'Školska sprema',
            'Datum i mjesto rođenja',
            'Ime oca/majke',
            'Adresa',
            'Spol',
            'OIB',
            'Telefon',
            'Email',
            'Radno mjesto',
            'Organizacijska jedinica',
            'Vrsta ugovora',
            'Datum zaposlenja',
            'Datum prekida ugovora',
            'Liječnički (od)',
            'Liječnički (do)',
            'Članak 3. točke',
            'ZNR (od)',
            'ZOP - Vrijedi od',
            'ZOP Izjava od',
            'Evakuacija (od)',
            'Prva pomoć (od)',
            'Prva pomoć (do)',
            'Toksikologija (od)',
            'Toksikologija (do)',
            'Ovlaštenik ZNR (od)',
            'Ovlaštenik ZNR (do)',
            'Edukacije / certifikati',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $today    = Carbon::today();
                $headings = $this->headings();
                $rowCount = Employee::count() + 1;               // +1 header
                $colCount = count($headings);

                // Auto wrap + autosize
                for ($col = 1; $col <= $colCount; $col++) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $sheet->getStyle("{$colLetter}1:{$colLetter}{$rowCount}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP)
                        ->setWrapText(true);
                    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                }

                // Dinamički pronađi kolone koje bojaš (one s "do") + "Edukacije / certifikati"
                $want = [
                    'Liječnički (do)',
                    'Prva pomoć (do)',
                    'Toksikologija (do)',
                    'Ovlaštenik ZNR (do)',
                    'Edukacije / certifikati',
                ];
                $map = [];
                foreach ($want as $label) {
                    $idx = array_search($label, $headings, true);
                    if ($idx !== false) {
                        $map[$label] = Coordinate::stringFromColumnIndex($idx + 1);
                    }
                }

                $dateCols = array_filter([
                    $map['Liječnički (do)'] ?? null,
                    $map['Prva pomoć (do)'] ?? null,
                    $map['Toksikologija (do)'] ?? null,
                    $map['Ovlaštenik ZNR (do)'] ?? null,
                ]);
                $certCol = $map['Edukacije / certifikati'] ?? null;

                for ($row = 2; $row <= $rowCount; $row++) {
                    // Datumske kolone ("do")
                    foreach ($dateCols as $col) {
                        $cell  = "{$col}{$row}";
                        $value = $sheet->getCell($cell)->getValue();
                        try {
                            $date = Carbon::createFromFormat('d.m.Y.', trim((string)$value));
                            if ($date->isPast()) {
                                $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                                      ->getStartColor()->setRGB('FF6347'); // crveno
                            } elseif ($date->diffInDays($today) <= 30) {
                                $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                                      ->getStartColor()->setRGB('FFFF00'); // žuto
                            }
                        } catch (\Exception $e) {
                            // ignore
                        }
                    }

                    // Certifikati (višeredni)
                    if ($certCol) {
                        $cell  = "{$certCol}{$row}";
                        $value = (string) $sheet->getCell($cell)->getValue();
                        $lines = preg_split("/\r\n|\r|\n/", $value);
                        foreach ($lines as $line) {
                            if (preg_match('/\d{2}\.\d{2}\.\d{4}\.\s*-\s*(\d{2}\.\d{2}\.\d{4}\.)/', $line, $m)) {
                                try {
                                    $date = Carbon::createFromFormat('d.m.Y.', trim($m[1]));
                                    if ($date->isPast()) {
                                        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                                              ->getStartColor()->setRGB('FF6347');
                                        break;
                                    } elseif ($date->diffInDays($today, false) <= 30) {
                                        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                                              ->getStartColor()->setRGB('FFFF00');
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    // ignore
                                }
                            }
                        }
                    }
                }
            },
        ];
    }
}