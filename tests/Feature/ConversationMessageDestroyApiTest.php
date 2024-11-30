<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationMessageDestroyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_delete_the_message()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();

        $response = $this->actingAs($me)->delete(route('conversations.messages.destroy', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]));

        $response->assertStatus(204);
        $this->assertModelMissing($message);
    }

    public function test_should_fail_if_the_user_does_not_participate_to_the_conversation()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::factory()->group()->hasAttached(collect([$user1, $user2]), ['is_admin' => false])->create();
        $message = Message::factory()->for($me)->for($conversation)->create();

        $response = $this->actingAs($me)->delete(route('conversations.messages.destroy', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]));

        $response->assertStatus(403);
        $this->assertModelExists($message);
    }

    public function test_should_fail_if_the_user_is_not_the_author_of_the_message()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();
        $conversation = Conversation::factory()->group()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();
        $message = Message::factory()->for($contact)->for($conversation)->create();

        $response = $this->actingAs($me)->delete(route('conversations.messages.destroy', [
            'conversation' => $conversation->id,
            'message' => $message->id
        ]));

        $response->assertStatus(403);
        $this->assertModelExists($message);
    }
}
