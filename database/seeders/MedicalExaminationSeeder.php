<?php

namespace Database\Seeders;

use App\Models\MedicalExamination;
use Database\Factories\MedicalExaminationFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MedicalExamination::factory()->count(70)->create();
    }
}
