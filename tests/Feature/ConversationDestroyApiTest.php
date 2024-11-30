<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationDestroyApiTest extends TestCase
{
    use RefreshDatabase;

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

        $response->assertStatus(403);
    }
}
