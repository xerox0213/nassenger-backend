<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_should_reject_if_email_already_exists()
    {
        User::factory()->create($this->credentials);
        $response = $this->postJson(route('auth.register'), $this->credentials);
        $response->assertJsonValidationErrorFor('email');
    }

    public function test_should_reject_if_password_is_less_than_three_chars_long()
    {
        $this->credentials['password'] = 'il';
        $response = $this->postJson(route('auth.register'), $this->credentials);
        $response->assertJsonValidationErrorFor('password');
    }

    public function test_should_reject_if_first_name_is_less_than_three_chars_long() {
        $this->credentials['first_name'] = 'Da';
        $response = $this->postJson(route('auth.register'), $this->credentials);
        $response->assertJsonValidationErrorFor('first_name');
    }
}
