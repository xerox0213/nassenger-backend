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

    public function test_should_filter_by_piece_of_firstname()
    {
        $me = User::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
        User::factory()->create(['firstname' => 'Elena', 'lastname' => 'Gilbert']);
        User::factory()->create(['firstname' => 'Elenor', 'lastname' => 'Mino']);
        User::factory()->create(['firstname' => 'Eleana', 'lastname' => 'Ono']);

        $response = $this->actingAs($me)->getJson(route('members.index', ['full_name' => 'len']));

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 2)
                ->etc());
    }

    public function test_should_filter_by_lastname()
    {
        $me = User::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
        User::factory()->create(['firstname' => 'Tommy', 'lastname' => 'Shelby']);
        User::factory()->create(['firstname' => 'Arthur', 'lastname' => 'Shelby']);
        User::factory()->create(['firstname' => 'Meo', 'lastname' => 'Shelay']);
        User::factory()->create(['firstname' => 'Wow', 'lastname' => 'Shilanoy']);

        $response = $this->actingAs($me)->getJson(route('members.index', ['full_name' => 'Shelby']));

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 2)
                ->etc());
    }

    public function test_should_filter_by_piece_of_lastname()
    {
        $me = User::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
        User::factory()->create(['firstname' => 'Tommy', 'lastname' => 'Shelby']);
        User::factory()->create(['firstname' => 'Arthur', 'lastname' => 'Shelby']);
        User::factory()->create(['firstname' => 'Meo', 'lastname' => 'Shelay']);
        User::factory()->create(['firstname' => 'Wow', 'lastname' => 'Shilanoy']);

        $response = $this->actingAs($me)->getJson(route('members.index', ['full_name' => 'Shel']));

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 3)
                ->etc());
    }

    public function test_should_filter_by_firstname_and_lastname()
    {
        $me = User::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Salvatore']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Salomon']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Saloran']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Situmian']);

        $response = $this->actingAs($me)->getJson(route('members.index', ['full_name' => "Stefan Salvatore"]));

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 1)
                ->etc());
    }

    public function test_should_filter_by_firstname_and_piece_of_lastname()
    {
        $me = User::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Salvatore']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Salomon']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Saloran']);
        User::factory()->create(['firstname' => 'Stefan', 'lastname' => 'Situmian']);

        $response = $this->actingAs($me)->getJson(route('members.index', ['full_name' => "Stefan Sal"]));

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 3)
                ->etc());
    }
}
