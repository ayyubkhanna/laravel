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
            'posyandu_id' => fake()->randomElement([1, 2]),
            'name' => fake()->name,
            'nik' => fake()->numberBetween(1, 1000),
            'alamat' => "Dusun " . fake()->numberBetween(1, 5),
            'awal_kehamilan' => fake()->date(),
            'perkiraan_hamil' => fake()->date(),
            'nama_suami' => fake()->firstNameMale,
        ];
    }
}
