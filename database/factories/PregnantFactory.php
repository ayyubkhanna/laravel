<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pregnant>
 */
class PregnantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peopleId' => fake()->randomElement([1, 2]),
            'pregnancyStartDate' => fake()->date(),
            'estimatedDueDate' => fake()->date(),
            'husbandName' => fake()->name(),
            'status' => 'aktif',
        ];
    }
}
