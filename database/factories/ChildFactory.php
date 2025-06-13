<?php

namespace Database\Factories;

use App\Models\Posyandu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Child>
 */
class ChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'posyandu_id' => fake()->randomElement([1,2]),
            'kia' => fake()->numberBetween(1,1000),
            'name' => fake()->name,
            'nik' => fake()->numberBetween(1, 1000),
            'alamat' => "Dusun " . fake()->numberBetween(1, 5),
            'orang_tua' => fake()->firstNameMale,
        ];
    }
}
