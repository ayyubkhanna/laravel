<?php

namespace Tests\Feature;

use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PersonTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_endpoint_index(): void
    {
        // Mengambil pengguna yang sudah ada
        $user = User::factory()->create(); // Pastikan Anda menggunakan factory yang sesuai

        // memasukan role
        $user->addRole('editor');

        // Membuat token untuk pengguna
        $token = $user->createToken('TestToken')->plainTextToken;
        
        // Menggunakan token dalam header Authorization
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/person?status=child');


        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
        ]);
    }

    public function test_endpoint_store(): void
    {
        // Mengambil pengguna yang sudah ada
        $user = User::factory()->create(); // Pastikan Anda menggunakan factory yang sesuai

        // memasukan role
        $user->addRole('editor');

        // Membuat token untuk pengguna
        $token = $user->createToken('TestToken')->plainTextToken;

        $posyandu = Posyandu::find(1);

        // Menggunakan token dalam header Authorization
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/person', [
            "name" => "Bellamy",
            "nik"=> "1234567891234562",
            "placeOfBirth"=> "Toaya",
            "dateOfBirth"=> "1997-09-28",
            "address"=> "Desa Toaya",
            "posyandu_id" => $posyandu->id
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
        ]);
    }
}
