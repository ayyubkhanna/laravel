<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_login_success(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'editor@app.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => ['id', 'name', 'email'],
            'token'
        ]);
    }

    public function test_login_wrong()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'editor@app.com',
            'password' => 'lksjdkjs'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => false,
            'message' => 'Email or Password is wrong'
        ]);
    }

    public function test_login_validation()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'editor@app.com',
            'password' => ''
        ]);

        $response->assertStatus(422);

    }
    
}
