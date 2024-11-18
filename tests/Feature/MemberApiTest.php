<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MemberApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_filter_by_firstname()
    {
        $me = User::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
        User::factory()->create(['firstname' => 'Elena', 'lastname' => 'Gilbert']);
        User::factory()->create(['firstname' => 'Elena', 'lastname' => 'Mino']);
        User::factory()->create(['firstname' => 'Elena', 'lastname' => 'Ono']);

        $response = $this->actingAs($me)->getJson(route('members.index', ['full_name' => 'Elena']));

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 3)
                ->etc());
    }
}
