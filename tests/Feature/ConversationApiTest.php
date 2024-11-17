<?php

namespace Tests\Feature;

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
}
