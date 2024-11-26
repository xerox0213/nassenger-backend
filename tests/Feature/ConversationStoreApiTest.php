<?php

namespace Tests\Feature;

use App\Enums\ConversationType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ConversationStoreApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_store_new_individual_conversation()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $userIds = [$me->id, $user1->id];

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.type', ConversationType::INDIVIDUAL)
                ->etc()
            );

        $conversationId = $response->json('data.id');

        $this->assertNotNull($me->conversations()->find($conversationId));
        $this->assertTrue($me->conversations()->find($conversationId)->pivot->is_admin == 0);

        $this->assertNotNull($me->conversations()->find($conversationId));
        $this->assertTrue($user1->conversations()->find($conversationId)->pivot->is_admin == 0);
    }

    public function test_should_store_new_group_conversation()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $userIds = [$me->id, $user1->id, $user2->id];

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.type', ConversationType::GROUP)
                ->etc());

        $conversationId = $response->json('data.id');

        $this->assertNotNull($me->conversations()->find($conversationId));
        $this->assertTrue($me->conversations()->find($conversationId)->pivot->is_admin == 1);

        $this->assertNotNull($user1->conversations()->find($conversationId));
        $this->assertTrue($user1->conversations()->find($conversationId)->pivot->is_admin == 0);

        $this->assertNotNull($user2->conversations()->find($conversationId));
        $this->assertTrue($user2->conversations()->find($conversationId)->pivot->is_admin == 0);
    }

    public function test_authenticated_user_should_be_include_in_the_conversation_if_he_is_not_in_user_ids_list()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $userIds = [$user1->id];

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.type', ConversationType::INDIVIDUAL)
                ->etc());

        $conversationId = $response->json('data.id');

        $this->assertNotNull($me->conversations()->find($conversationId));
        $this->assertNotNull($user1->conversations()->find($conversationId));
    }

    public function test_store_should_fail_if_user_ids_list_is_empty()
    {
        $me = User::factory()->create();
        $userIds = [];

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(422)
            ->assertInvalid('user_ids');
    }

    public function test_store_should_fail_if_there_are_duplicate_user_ids()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $userIds = [$me->id, $user1->id, $user1->id];

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(422)
            ->assertInvalid(['user_ids.1', 'user_ids.2']);
    }

    public function test_store_should_fail_if_user_ids_are_not_integer()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $userIds = ['wow', $user1->id];

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(422)
            ->assertInvalid('user_ids.0');
    }

    public function test_store_should_fail_if_user_ids_does_not_exist()
    {
        $me = User::factory()->create();
        $userIds = [$me->id, 123];

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(422)
            ->assertInvalid('user_ids.1');
    }

    public function test_store_should_fail_if_user_ids_list_is_null()
    {
        $me = User::factory()->create();
        $userIds = null;

        $response = $this->actingAs($me)->postJson(route('conversations.store', ['user_ids' => $userIds]));

        $response
            ->assertStatus(422)
            ->assertInvalid('user_ids');
    }
}
