<?php

namespace Tests\Feature;

use App\Enums\ConversationType;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ConversationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_the_first_10_conversations_ordered_by_most_recent_message()
    {
        $users = User::factory()->count(4)->create();
        $me = $users[0];
        $contact1 = $users[1];
        $contact2 = $users[2];
        $contact3 = $users[3];

        $conversations = [
            Conversation::factory()->group()->hasAttached(collect([$me, $contact1, $contact3]), ['is_admin' => false])->create(),
            Conversation::factory()->hasAttached(collect([$me, $contact2]), ['is_admin' => false])->create()
        ];
        $conversation1 = $conversations[0];
        $conversation2 = $conversations[1];

        $messages = [
            Message::factory()->for($conversation1)->for($contact1)->create(['sent_at' => Carbon::now()->subHour()]),
            Message::factory()->for($conversation1)->for($me)->create(['sent_at' => Carbon::now()->subMinutes(30)]),

            Message::factory()->for($conversation2)->for($me)->create(['sent_at' => Carbon::now()->subHours(2)]),
            Message::factory()->for($conversation2)->for($contact2)->create(['sent_at' => Carbon::now()->subMinutes(10)]),
        ];
        $mostRecentMsgConv1 = $messages[1];
        $mostRecentMsgConv2 = $messages[3];

        $response = $this->actingAs($me)->getJson(route('conversations.index'));

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 2)
                ->has('data.0', fn(AssertableJson $json) => $json
                    ->where('id', $conversation2->id)
                    ->where('name', $contact2->firstname)
                    ->where('last_message.id', $mostRecentMsgConv2->id)
                    ->where('last_message.you', false)
                    ->has('contacts', 1)
                    ->etc())
                ->has('data.1', fn(AssertableJson $json) => $json
                    ->where('id', $conversation1->id)
                    ->where('name', "$contact1->firstname, $contact3->firstname")
                    ->where('last_message.id', $mostRecentMsgConv1->id)
                    ->where('last_message.you', true)
                    ->has('contacts', 2)
                    ->etc())
                ->where('meta.per_page', 10)
                ->etc()
            );
    }

    public function test_should_soft_delete_the_given_conversation()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached($user, ['is_admin' => false])->create();

        $response = $this->actingAs($user)->deleteJson(route('conversations.destroy', ['conversation' => $conversation->id]));

        $pivot = $conversation->users()->find($user->id)->pivot;

        $response->assertStatus(204);
        $this->assertNotNull($pivot->deleted_at);
        $this->assertNotNull($pivot->last_deleted_at);
    }

    public function test_soft_delete_conversation_should_fail_if_user_is_not_participating_in_it()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached(collect([$user1, $user2]), ['is_admin' => false])->create();

        $response = $this->actingAs($me)->deleteJson(route('conversations.destroy', ['conversation' => $conversation->id]));

        $response->assertStatus(404);
    }

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
}
