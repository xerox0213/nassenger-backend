<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_should_register_the_user_if_he_does_not_already_exist()
    {
        $credentials = [
            'firstname' => 'Damon',
            'lastname' => 'Salvatore',
            'email' => 'damon.salvatore@gmail.com',
            'password' => 'elena123'
        ];

        $response = $this->postJson(route('auth.register'), $credentials);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => null
            ]);
    }
}