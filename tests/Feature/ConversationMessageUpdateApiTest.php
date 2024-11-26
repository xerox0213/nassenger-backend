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

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update',[
            'conversation' => $conversation->id,
            'message' => $message->id
        ]), ['content' => $newMessageContent]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.id', $message->id)
            ->assertJsonPath('data.content', $newMessageContent);
    }

    public function test_should_fail_if_the_new_message_content_is_not_present()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();

        $response = $this->actingAs($me)->patchJson(route('conversations.messages.update',[
            'conversation' => $conversation->id,
            'message' => $message->id
        ]));

        $response->assertInvalid('content');
    }
}
