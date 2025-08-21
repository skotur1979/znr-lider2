<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{

    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'                              => fake()->name(),
            'address'                           => fake()->address(),
            'email'                             => fake()->unique()->email(),
            'phone'                             => fake()->phoneNumber(),
            'workplace'                         => fake()->jobTitle(),
            'remark'                            => fake()->paragraph(2),
            'is_first_aid_trained'              => fake()->randomElement([true,false]),
            'is_handling_flammable_materials'   => fake()->randomElement([true,false]),
            'safeway_working_at'                => fake()->dateTimeBetween('-1 year','now'),
            'zop_passed_at'                     => fake()->dateTimeBetween('-2 years','now'),
            'zop_statement_at'                  => fake()->dateTimeBetween('-2 years','now'),
            'toxicology_valid_at'               => fake()->dateTimeBetween('-1 year','now'),
            'employeed_at'                      => fake()->dateTimeBetween('-5 years', '-1 year')
        ];
    }
}
