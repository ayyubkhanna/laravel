<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\Posyandu;
use App\Models\Pregnant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PregnantTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_pregnant_create(): void
    {
          // Mengambil pengguna yang sudah ada
          $user = User::factory()->create(); // Pastikan Anda menggunakan factory yang sesuai

          // memasukan role
          $user->addRole('editor');
  
          // Membuat token untuk pengguna
          $token = $user->createToken('TestToken')->plainTextToken;

        $person = Person::inRandomOrder()->first()->id;
  
          // Menggunakan token dalam header Authorization
          $response = $this->withHeaders([
              'Authorization' => "Bearer $token",
          ])->postJson('/api/pregnant', [
            'peopleId' => $person,
            'pregnancyStartDate' => fake()->date(),
            'estimatedDueDate' => fake()->date(),
            'husbandName' => fake()->name(),
            'status' => 'aktif',
          ]);

          $response->assertStatus(201);
    }

    public function test_pregnant_update(): void
    {
          // Mengambil pengguna yang sudah ada
        $user = User::whereHasRole('editor')->first();
  
          // Membuat token untuk pengguna
        $token = $user->createToken('TestToken')->plainTextToken;

        $person = Person::inRandomOrder()->first()->id;

        // Menggunakan token dalam header Authorization
        $updateAll = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->putJson('/api/pregnant/4', [
            'peopleId' => $person,
            'pregnancyStartDate' => fake()->date(),
            'estimatedDueDate' => fake()->date(),
            'husbandName' => fake()->name(),
            'status' => 'aktif',
        ]);

        $updateAll->assertStatus(200);

    }

    public function test_pregnant_update_status(): void
    {
          // Mengambil pengguna yang sudah ada
        $user = User::whereHasRole('editor')->first();
  
          // Membuat token untuk pengguna
        $token = $user->createToken('TestToken')->plainTextToken;

        $updateStatus = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->putJson('/api/pregnant/4', [
            'actualDeliveryDate' => fake()->date(),
            'status' => 'selesai'
        ]);  

        $updateStatus->assertStatus(200);

    }

    public function test_pregnant_delete(): void
    {
          // Mengambil pengguna yang sudah ada
        $user = User::whereHasRole('editor')->first();
  
          // Membuat token untuk pengguna
        $token = $user->createToken('TestToken')->plainTextToken;

        $updateStatus = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->deleteJson('/api/pregnant/4');  

        $updateStatus->assertStatus(200);

    }


}
