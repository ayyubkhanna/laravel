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

        $data = [
                'Bumi Jambu', 
                'Ranga Ranga'
        ];

        
        return [
            'name' => $this->faker->randomElement($data),
            'alamat' => $this->faker->randomElement(['Dusun 1', 'Dusun 3']),
            'deskripsi' => fake()->sentence(5)
        ];
    }
}
