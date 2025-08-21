<?php

namespace Database\Factories;

use App\Models\MedicalExamination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalExamination>
 */
class MedicalExaminationFactory extends Factory
{

    protected $model = MedicalExamination::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'employee_id'   => fake()->numberBetween(1,20),
            'description'   => fake()->paragraph(2),
            'start_at'      => fake()->dateTimeBetween('-3 years','-2 years'),
            'end_at'        => fake()->dateTimeBetween('-2 years','now')
        ];
    }
}
