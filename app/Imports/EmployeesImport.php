<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\EmployeeCertificate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Carbon\Carbon;

class EmployeesImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
{
    $data = $row->toArray();

    // --- Robustno dohvaćanje imena ------------------------------
    $name =
        $this->v($data, ['prezime_i_ime', 'ime_i_prezime', 'name']) ?:
        trim(
            ($this->v($data, ['ime', 'first_name']) ?? '') . ' ' .
            ($this->v($data, ['prezime', 'last_name']) ?? '')
        );

    // Ako je red prazan (nema imena ni OIB-a) -> preskoči
    $oib = $this->v($data, ['oib']);
    if (!$name && !$oib) {
        return; // potpuno prazan red
    }

    // Ako baš nema imena, ne pokušavaj raditi insert da ne padne constraint
    if (!$name) {
        // Po želji: možeš tu poslati notifikaciju ili skupiti greške u session
        return;
    }

    // --- Update ili create po OIB-u (sprječava duplikate) --------
    $employee = \App\Models\Employee::updateOrCreate(
        ['OIB' => $oib],
        [
            'name'              => $name,
            'address'           => $this->v($data, ['adresa', 'address']),
            'gender'            => $this->v($data, ['spol', 'gender']),
            'phone'             => $this->v($data, ['telefon', 'mobitel', 'phone']),
            'email'             => $this->v($data, ['email']),
            'workplace'         => $this->v($data, ['radno_mjesto', 'workplace']),
            'organization_unit' => $this->v($data, ['organizacijska_jedinica', 'organization_unit']),
            'contract_type'     => $this->v($data, ['vrsta_ugovora', 'contract_type']),

            // ➕ RA-1 polja
            'job_title'         => $this->v($data, ['zanimanje', 'job_title']),
            'education'         => $this->v($data, ['skolska_sprema', 'školska_sprema', 'education']),
            'place_of_birth'    => $this->v($data, ['datum_i_mjesto_rodenja', 'datum_i_mjesto_rođenja', 'mjesto_rodenja', 'place_of_birth']),
            'name_of_parents'   => $this->v($data, ['ime_oca_majke', 'ime_oca_i_majke', 'name_of_parents']),

            'employeed_at'                       => $this->parseDate($this->v($data, ['datum_zaposlenja', 'employeed_at'])),
            'contract_ended_at'                  => $this->parseDate($this->v($data, ['datum_prekida_ugovora', 'contract_ended_at'])),
            'medical_examination_valid_from'     => $this->parseDate($this->v($data, ['lijecnicki_pregled_od', 'liječnički_pregled_od'])),
            'medical_examination_valid_until'    => $this->parseDate($this->v($data, ['lijecnicki_pregled_do', 'liječnički_pregled_do'])),
            'article'                            => $this->v($data, ['clanak_3_tocke', 'članak_3_tocke', 'članak_3_točke', 'article']),
            'occupational_safety_valid_from'     => $this->parseDate($this->v($data, ['znr_od', 'occupational_safety_valid_from'])),
            'fire_protection_valid_from'         => $this->parseDate($this->v($data, ['zop_od', 'fire_protection_valid_from'])),
            'fire_protection_statement_at'       => $this->parseDate($this->v($data, ['zop_izjava_od', 'fire_protection_statement_at'])),
            'evacuation_valid_from'              => $this->parseDate($this->v($data, ['evakuacija_od', 'evacuation_valid_from'])),
            'first_aid_valid_from'               => $this->parseDate($this->v($data, ['prva_pomoc_od', 'prva_pomoć_od', 'first_aid_valid_from'])),
            'first_aid_valid_until'              => $this->parseDate($this->v($data, ['prva_pomoc_do', 'prva_pomoć_do', 'first_aid_valid_until'])),
            'toxicology_valid_from'              => $this->parseDate($this->v($data, ['toksikologija_od', 'toxicology_valid_from'])),
            'toxicology_valid_until'             => $this->parseDate($this->v($data, ['toksikologija_do', 'toxicology_valid_until'])),
            'employers_authorization_valid_from' => $this->parseDate($this->v($data, ['ovlastenik_poslodavca_od', 'ovlaštenik_poslodavca_od', 'employers_authorization_valid_from'])),
            'employers_authorization_valid_until'=> $this->parseDate($this->v($data, ['ovlastenik_poslodavca_do', 'ovlaštenik_poslodavca_do', 'employers_authorization_valid_until'])),
            'user_id'                             => auth()->id(),
        ]
    );

        // Certifikati certifikat_1_naziv / _od / _do ... certifikat_10_*
        for ($i = 1; $i <= 10; $i++) {
            $title      = $this->v($data, ["certifikat_{$i}_naziv", "certifikat{$i}_naziv", "certifikat_{$i}_title"]);
            $valid_from = $this->parseDate($this->v($data, ["certifikat_{$i}_od", "certifikat{$i}_od", "certifikat_{$i}_from"]));
            $valid_until= $this->parseDate($this->v($data, ["certifikat_{$i}_do", "certifikat{$i}_do", "certifikat_{$i}_until"]));

            if ($title && $valid_from) {
                $employee->certificates()->create([
                    'title'       => $title,
                    'valid_from'  => $valid_from,
                    'valid_until' => $valid_until,
                ]);
            }
        }
    }

    private function v(array $data, array $keys)
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $data) && $data[$k] !== null && $data[$k] !== '') {
                return $data[$k];
            }
        }
        return null;
    }

    private function parseDate($value)
    {
        if (!$value || trim((string)$value) === '/' || trim((string)$value) === '') return null;

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}

