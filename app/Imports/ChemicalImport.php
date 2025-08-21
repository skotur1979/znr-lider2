<?php

namespace App\Imports;

use App\Models\Chemical;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ChemicalImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Chemical([
            'product_name'       => $row['naziv_proizvoda'] ?? null,
            'cas_number'         => $row['cas_broj'] ?? null,
            'ufi_number'         => $row['ufi_broj'] ?? null,
            'hazard_pictograms'  => $this->parseJson($row['piktogrami_opasnosti'] ?? ''),
            'h_statements'       => $this->parseJson($row['h_oznake'] ?? ''),
            'p_statements'       => $this->parseJson($row['p_oznake'] ?? ''),
            'usage_location'     => $row['mjesto_upotrebe'] ?? null,
            'annual_quantity'    => $row['godisnja_kolicina'] ?? null,
            'gvi_kgvi'           => $row['gvi_kgvi'] ?? null,
            'voc'                => $row['voc'] ?? null,
            'stl_hzjz'           => $this->parseDate($row['stl_hzjz'] ?? null),
        ]);
    }

    private function parseJson($value)
    {
        return $value ? array_map('trim', explode(',', $value)) : [];
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === '/') return null;

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return Carbon::parse(str_replace('.', '-', $value))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
