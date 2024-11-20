<?php

namespace Tests\Feature;

use App\Enums\MessageType;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ConversationMessageIndexApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_messages_of_the_conversation()
    {
        $me = User::factory()->create();
        $contact = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$me, $contact]), ['is_admin' => false])->create();

        $messageFromMe = Message::factory()->for($me)->for($conversation)->create();
        $messageFromContact = Message::factory()->for($contact)->for($conversation)->for($messageFromMe, 'initialMessage')->create();

        $response = $this->actingAs($me)->getJson(route('conversations.messages.index', ['conversation' => $conversation->id]));

        $response
            ->assertStatus(200)
            ->assertJson(fn($json) => $json
                ->has('data', 2)
                ->has('data.0', fn(AssertableJson $json) => $json
                    ->where('id', $messageFromMe->id)
                    ->where('content', $messageFromMe->content)
                    ->where('type', MessageType::TEXT)
                    ->where('author.id', $me->id)
                    ->where('author.firstname', $me->firstname)
                    ->where('author.lastname', $me->lastname)
                    ->where('author.avatar', $me->avatar)
                    ->where('author.you', true)
                    ->where('initial_message', null)
                    ->etc())
                ->has('data.1', fn(AssertableJson $json) => $json
                    ->where('id', $messageFromContact->id)
                    ->where('content', $messageFromContact->content)
                    ->where('type', MessageType::TEXT)
                    ->where('author.id', $contact->id)
                    ->where('author.firstname', $contact->firstname)
                    ->where('author.lastname', $contact->lastname)
                    ->where('author.avatar', $contact->avatar)
                    ->where('author.you', false)
                    ->where('initial_message.id', $messageFromMe->id)
                    ->where('initial_message.type', MessageType::TEXT)
                    ->where('initial_message.content', $messageFromMe->content)
                    ->etc())
                ->etc()
            );
    }

    public function test_should_not_return_messages_if_user_do_not_participate_to_the_conversation()
    {
        $me = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conversation = Conversation::factory()->hasAttached(collect([$user1, $user2]), ['is_admin' => false])->create();

        $messageFromUser1 = Message::factory()->for($user1)->for($conversation)->create();
        Message::factory()->for($user2)->for($conversation)->for($messageFromUser1, 'initialMessage')->create();

        $response = $this->actingAs($me)->getJson(route('conversations.messages.index', ['conversation' => $conversation->id]));

        $response
            ->assertForbidden()
            ->assertJsonMissingPath('data');
    }
}
