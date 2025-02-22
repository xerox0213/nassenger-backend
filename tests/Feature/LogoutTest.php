<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_logout()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->deleteJson(route('auth.logout'));
        $response->assertNoContent();
        $this->assertGuest();
    }
}
