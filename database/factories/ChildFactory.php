<?php

namespace Database\Factories;

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
        $faker = fake();
        return [
            'kia' => fake()->numberBetween(1,1000),
            'name' => fake()->name,
            'kk' => fake()->numberBetween(1, 1000),
            'nik' => fake()->numberBetween(1, 1000),
            'alamat' => fake()->address(),
            'orang_tua' => fake()->firstNameMale,
        ];
    }
}
