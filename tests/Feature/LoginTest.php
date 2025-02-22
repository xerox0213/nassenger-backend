<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private array $credentials;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->credentials = [
            'email' => 'damon.salvatore@gmail.com',
            'password' => 'iloveyouelena'
        ];

        $this->user = User::factory()->create([
            'email' => $this->credentials['email'],
            'password' => Hash::make($this->credentials['password'])
        ]);
    }

    public function test_should_login()
    {
        $response = $this->postJson(route('auth.login', $this->credentials));
        $response->assertNoContent();
    }

    public function test_should_reject_if_email_is_wrong()
    {
        $this->credentials['email'] = 'stefan.salvatore@gmail.com';
        $response = $this->postJson(route('auth.login'), $this->credentials);
        $response->assertUnauthorized();
    }

    public function test_should_reject_if_password_is_wrong()
    {
        $this->credentials['password'] = 'iloveyoucaroline';
        $response = $this->postJson(route('auth.login'), $this->credentials);
        $response->assertUnauthorized();
    }
}
