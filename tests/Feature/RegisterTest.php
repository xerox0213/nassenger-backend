<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private array $credentials = [
        'first_name' => 'Damon',
        'last_name' => 'Salvatore',
        'email' => 'damon.salvatore@gmail.com',
        'password' => 'iloveyouelena'
    ];

    public function test_should_register()
    {
        $response = $this->postJson(route('auth.register'), $this->credentials);
        $response->assertNoContent();
    }
}
