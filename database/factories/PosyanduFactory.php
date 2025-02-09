<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Posyandu>
 */
class PosyanduFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Bumi Jambu', 'Ranga Ranga']),
            'alamat' => $this->faker->randomElement(['Dusun 1', 'Dusun 3']),
            'deskripsi' => fake()->sentence(5)
        ];
    }
}
