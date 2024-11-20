<?php

namespace Tests\Feature;

use App\Enums\MessageType;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ConversationMessageStoreApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_store_the_message_in_the_conversation_without_initial_message_id()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();

        $messageContent = 'Goose - Synrise';

        $response = $this->actingAs($me)->postJson(route('conversations.messages.store', [
            'conversation' => $conversation->id
        ]), [
            'content' => $messageContent
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', fn(AssertableJson $json) => $json
                    ->where('type', MessageType::TEXT)
                    ->where('content', $messageContent)
                    ->where('author.id', $me->id)
                    ->where('author.firstname', $me->firstname)
                    ->where('author.lastname', $me->lastname)
                    ->where('author.avatar', $me->avatar)
                    ->where('author.you', true)
                    ->where('initial_message', null)
                    ->etc())
            );
    }

    public function test_should_store_the_message_in_the_conversation_with_initial_message_id()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();

        $initialMessage = Message::factory()->for($contact)->for($conversation)->create();

        $messageContent = 'Goose - Synrise';

        $response = $this->actingAs($me)->postJson(route('conversations.messages.store', [
            'conversation' => $conversation->id
        ]), [
            'content' => $messageContent,
            'initial_message_id' => $initialMessage->id
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', fn(AssertableJson $json) => $json
                    ->where('type', MessageType::TEXT)
                    ->where('content', $messageContent)
                    ->where('author.id', $me->id)
                    ->where('author.firstname', $me->firstname)
                    ->where('author.lastname', $me->lastname)
                    ->where('author.avatar', $me->avatar)
                    ->where('author.you', true)
                    ->where('initial_message.id', $initialMessage->id)
                    ->where('initial_message.content', $initialMessage->content)
                    ->where('initial_message.type', $initialMessage->type)
                    ->etc())
            );
    }

    public function test_should_not_store_the_message_if_the_user_do_not_participate_to_the_conversation()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $contact = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$user1, $contact]), ['is_admin' => false])->create();

        $messageContent = 'Goose - Synrise';

        $response = $this->actingAs($me)->postJson(route('conversations.messages.store', [
            'conversation' => $conversation->id
        ]), [
            'content' => $messageContent
        ]);

        $response
            ->assertForbidden()
            ->assertJsonMissingPath('data');
    }

    public function test_should_not_store_the_message_if_content_is_null()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();

        $messageContent = null;

        $response = $this->actingAs($me)->postJson(route('conversations.messages.store', [
            'conversation' => $conversation->id
        ]), [
            'content' => $messageContent
        ]);

        $response
            ->assertStatus(422)
            ->assertInvalid(['content']);
    }

    public function test_should_not_store_the_message_if_content_is_not_present()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();

        $response = $this->actingAs($me)->postJson(route('conversations.messages.store', [
            'conversation' => $conversation->id
        ]));

        $response
            ->assertStatus(422)
            ->assertInvalid(['content']);
    }

    public function test_should_not_store_the_message_if_content_is_an_empty_string()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();

        $messageContent = '';

        $response = $this->actingAs($me)->postJson(route('conversations.messages.store', [
            'conversation' => $conversation->id
        ]), [
            'content' => $messageContent
        ]);

        $response
            ->assertStatus(422)
            ->assertInvalid(['content']);
    }
}
