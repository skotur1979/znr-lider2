<?php

namespace App\Imports;

use App\Models\Miscellaneous;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MiscellaneousImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Normalizacija/trim
        $naziv         = $this->t($row['naziv'] ?? null);
        $kategorija    = $this->t($row['kategorija'] ?? null);
        $ispitao       = $this->t($row['ispitao'] ?? null);
        $brojIzvjestaja= $this->t($row['broj_izvjestaja'] ?? null);
        $napomena      = $this->t($row['napomena'] ?? null);

        // Obavezna polja
        if (!$naziv || empty($row['vrijedi_od']) || empty($row['vrijedi_do'])) {
            return null;
        }

        // Parsiranje datuma
        $od  = $this->parseDate($row['vrijedi_od']);
        $do  = $this->parseDate($row['vrijedi_do']);

        if (!$od || !$do) {
            return null;
        }

        // Ako su datumi zamijenjeni, rotiraj
        if (Carbon::parse($od)->gt(Carbon::parse($do))) {
            [$od, $do] = [$do, $od];
        }

        // Pokušaj pronaći kategoriju za trenutnog korisnika
        $userId = Auth::id() ?? 1;
        $categoryId = null;

        if ($kategorija) {
            $categoryId = Category::where('user_id', $userId)
                ->where('name', $kategorija)
                ->value('id');
        }

        return new Miscellaneous([
            'name'                     => $naziv,
            'category_id'              => $categoryId, // ostaje null ako kategorija ne postoji
            'examiner'                 => $ispitao ?: null,
            'report_number'            => $brojIzvjestaja ?: null,
            'examination_valid_from'   => $od,
            'examination_valid_until'  => $do,
            'remark'                   => $napomena ?: null,
            'user_id'                  => $userId,
        ]);
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        $v = trim((string) $value);
        $v = rtrim($v, '.');

        $formats = ['d.m.Y', 'Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.y'];

        foreach ($formats as $f) {
            try {
                return Carbon::createFromFormat($f, $v)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function t(?string $v): ?string
    {
        $v = is_string($v) ? trim($v) : $v;
        return $v === '' ? null : $v;
    }
}

