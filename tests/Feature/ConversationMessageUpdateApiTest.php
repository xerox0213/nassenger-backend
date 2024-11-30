<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationMessageUpdateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_update_the_message_content()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();
        $newMessageContent = 'Once upon a time.';

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]), ['content' => $newMessageContent]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.id', $message->id)
            ->assertJsonPath('data.content', $newMessageContent);
    }

    public function test_should_fail_if_the_user_does_not_participate_to_the_conversation()
    {
        $me = User::factory()->create();
        $contact1 = User::factory()->create();
        $contact2 = User::factory()->create();
        $conversation = Conversation::factory()->group()->hasAttached(collect([$contact1, $contact2]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();
        $newMessageContent = 'Once upon a time.';

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]), ['content' => $newMessageContent]);

        $response->assertStatus(403);
    }

    public function test_should_fail_if_the_user_is_not_the_author_of_the_message()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->group()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($contact)->for($conversation)->create();
        $newMessageContent = 'Once upon a time.';

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]), ['content' => $newMessageContent]);

        $response->assertStatus(403);
    }

    public function test_should_fail_if_the_new_message_content_is_not_present()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]));

        $response->assertInvalid('content');
    }

    public function test_should_fail_if_the_new_message_content_is_null()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]), ['content' => null]);

        $response->assertInvalid('content');
    }

    public function test_should_fail_if_the_new_message_content_is_an_empty_string()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();
        $newMessageContent = '';

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]), ['content' => $newMessageContent]);

        $response->assertInvalid('content');
    }
}
