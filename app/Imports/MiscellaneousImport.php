<?php

namespace App\Imports;

use App\Models\Miscellaneous;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Auth;

class MiscellaneousImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['naziv']) || empty($row['kategorija']) || empty($row['vrijedi_od']) || empty($row['vrijedi_do'])) {
            return null; // preskoči red ako fale obavezna polja
        }

        $categoryId = Category::where('name', trim($row['kategorija']))->value('id');

        if (!$categoryId) {
            return null; // preskoči ako kategorija ne postoji
        }

        return new Miscellaneous([
            'name' => $row['naziv'],
            'category_id' => $categoryId,
            'examiner' => $row['ispitao'] ?? null,
            'report_number' => $row['broj_izvjestaja'] ?? null,
            'examination_valid_from' => $this->parseDate($row['vrijedi_od']),
            'examination_valid_until' => $this->parseDate($row['vrijedi_do']),
            'remark' => $row['napomena'] ?? null,
            'user_id' => Auth::id() ?? 1, // ako nema korisnika, default na ID 1
        ]);
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            // Ako je numerički (Excel format), konvertiraj
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Inače probaj ručno parsirati
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
