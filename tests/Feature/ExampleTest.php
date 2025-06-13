<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function AuthTest(): void
    {
        User::factory(2)->create();

        $response = $this->post('/login');

        $response->assertStatus(200)->assertJsonCount(2);
    }
}
