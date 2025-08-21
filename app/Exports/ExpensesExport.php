<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExpensesExport implements FromCollection, WithHeadings, WithColumnWidths, WithMapping
{
    public function collection()
    {
        return Expense::with('budget')->get();
    }

    public function headings(): array
    {
        return [
            'Godina',
            'Mjesec',
            'Naziv troška',
            'Iznos (€)',
            'Dobavljač',
            'Realizirano',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->budget?->godina ?? '',
            $expense->mjesec,
            $expense->naziv_troska,
            number_format($expense->iznos, 2, ',', '.'),
            $expense->dobavljac,
            $expense->realizirano ? 'Da' : 'Ne',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 30,
            'D' => 15,
            'E' => 20,
            'F' => 15,
        ];
    }
}
