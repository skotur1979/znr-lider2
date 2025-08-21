<?php

namespace App\Exports;

use App\Models\Chemical;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ChemicalFormattedExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    public function collection()
    {
        return Chemical::all()->map(function ($chemical) {
            return [
                $chemical->product_name,
                $chemical->cas_number,
                $chemical->ufi_number,
                is_array($chemical->hazard_pictograms) ? implode(', ', $chemical->hazard_pictograms) : $chemical->hazard_pictograms,
                is_array($chemical->h_statements) ? implode(', ', $chemical->h_statements) : $chemical->h_statements,
                is_array($chemical->p_statements) ? implode(', ', $chemical->p_statements) : $chemical->p_statements,
                $chemical->usage_location,
                $chemical->annual_quantity,
                $chemical->gvi_kgvi,
                $chemical->voc,
                $chemical->stl_hzjz ? \Carbon\Carbon::parse($chemical->stl_hzjz)->format('d.m.Y') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Naziv',
            'CAS broj',
            'UFI broj',
            'Piktogrami',
            'H oznake',
            'P oznake',
            'Mjesto upotrebe',
            'Količina (kg/l)',
            'GVI / KGVI',
            'VOC',
            'STL – HZJZ',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 12,
            'C' => 14,
            'D' => 20,
            'E' => 25,
            'F' => 25,
            'G' => 18,
            'H' => 12,
            'I' => 12,
            'J' => 14,
            'K' => 15,
        ];
    }
}
