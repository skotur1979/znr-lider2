<?php

namespace App\Imports;

use App\Models\Machine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Imports\MachinesImport;

class MachinesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
{
    if (empty($row['naziv']) || empty($row['vrijedi_od']) || empty($row['lokacija'])) {
        return null; // Preskoči red ako su ključna polja prazna
    }

    return new Machine([
        'name' => $row['naziv'],
        'manufacturer' => $row['proizvodac'],
        'factory_number' => $row['tvornicki_broj'],
        'inventory_number' => $row['inventarni_broj'],
        'examination_valid_from' => $this->parseDate($row['vrijedi_od']),
        'examination_valid_until' => $this->parseDate($row['vrijedi_do']),
        'examined_by' => $row['ispitao'] ?? null,
        'report_number' => $row['broj_izvjestaja'] ?? null,
        'location' => $row['lokacija'],
        'remark' => $row['napomena'] ?? null,
        'user_id' => auth()->id(),
    ]);
}
    private function parseDate($value)
    {
        if (is_null($value) || trim((string)$value) === '/' || trim((string)$value) === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
