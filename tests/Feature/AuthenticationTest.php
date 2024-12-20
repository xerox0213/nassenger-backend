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

        $response->assertStatus(204);
    }

    public function test_register_should_not_register_the_user_if_he_already_exists()
    {
        $user = User::factory()->create();
        $credentials = $user->only(['firstname', 'lastname', 'email', 'password']);

        $response = $this->postJson(route('auth.register'), $credentials);

        $response
            ->assertStatus(422)
            ->assertInvalid('email');
    }

    public function test_register_should_not_register_the_user_if_credentials_are_invalid()
    {
        $credentials = [
            'firstname' => '',
            'lastname' => '',
            'email' => 'damon.salvatore',
            'password' => ''
        ];

        $response = $this->postJson(route('auth.register'), $credentials);

        $response
            ->assertStatus(422)
            ->assertInvalid(['firstname', 'lastname', 'email', 'password']);
    }

    public function test_login_should_authenticate_the_user_if_he_exists()
    {
        $password = '123';
        $user = User::factory()->create(['password' => $password]);
        $credentials = $user->only(['email']);
        $credentials['password'] = $password;

        $response = $this->postJson(route('auth.login'), $credentials);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'avatar' => $user->avatar
                ]
            ]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_should_not_authenticate_the_user_if_he_does_not_exist()
    {
        $credentials = [
            'email' => 'damon.salvatore@gmail.com',
            'password' => 'elena123'
        ];

        $response = $this->postJson(route('auth.login'), $credentials);

        $response->assertStatus(401);
        $this->assertGuest();
    }

    public function test_login_should_not_authenticate_the_user_if_credentials_are_invalid()
    {
        $credentials = [
            'email' => 'damon.salvatore',
            'password' => ''
        ];

        $response = $this->postJson(route('auth.login'), $credentials);

        $response
            ->assertStatus(422)
            ->assertInvalid(['email', 'password']);
        $this->assertGuest();
    }

    public function test_logout_should_logout_the_authenticated_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('auth.logout'));

        $this->assertGuest();
    }
}
