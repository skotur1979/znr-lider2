<?php

namespace App\Imports;

use App\Models\Fire;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class FireImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['mjesto']) || empty($row['datum_servisa_od']) || empty($row['vrijedi_do'])) {
            return null;
        }

        return new Fire([
            'place' => $row['mjesto'],
            'type' => $row['tip_aparata'],
            'factory_number/year_of_production' => $row['tvor_br_god_proiz'] ?? null,
            'serial_label_number' => $row['serijski_broj_naljepnice'] ?? null,
            'examination_valid_from' => $this->parseDate($row['datum_servisa_od']),
            'examination_valid_until' => $this->parseDate($row['vrijedi_do']),
            'service' => $row['serviser'],
            'regular_examination_valid_from' => $this->parseDate($row['datum_redovnog_pregleda']),
            'visible' => $row['uocljivost_i_dostupnost'],
            'remark' => $row['uoceni_nedostatci'],
            'action' => $row['postupci_otklanjanja'],
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
