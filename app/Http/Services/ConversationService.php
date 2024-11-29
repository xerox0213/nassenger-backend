<?php

namespace App\Http\Services;

use App\Enums\ConversationType as CT;
use App\Models\Conversation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ConversationService
{
    public function index()
    {
        return Auth::user()
            ->conversations()
            ->join('messages', 'messages.conversation_id', '=', 'conversations.id')
            ->groupBy('conversations.id', 'conversations.name', 'conversations.type')
            ->orderBy(DB::raw('max(messages.sent_at)'), 'desc')
            ->select('conversations.*')
            ->simplePaginate(10);
    }

    public function store(Collection $userIds)
    {
        if ($userIds->doesntContain(fn($userId) => $userId == Auth::id())) {
            $userIds->push(Auth::id());
        }

        $isGroup = $userIds->count() > 2;

        $conversation = $isGroup ? null : $this->viewIndividualConversation($userIds[0], $userIds[1]);

        if ($conversation) return $conversation;

        $userIdsWithPivot = [];
        foreach ($userIds as $userId) {
            $userIdsWithPivot[$userId] = ['is_admin' => $isGroup && $userId == Auth::id()];
        }

        $conversation = Conversation::create(['type' => $isGroup ? CT::GROUP : CT::INDIVIDUAL, 'name' => null]);

        $conversation->users()->attach($userIdsWithPivot);

        return $conversation;
    }

    public function destroy(Conversation $conversation): void
    {
        Gate::authorize('delete', $conversation);

        $user = $conversation->users()->find(Auth::id());

        $now = Carbon::now();
        $user->pivot->deleted_at = $now;
        $user->pivot->last_deleted_at = $now;
        $user->pivot->save();
    }

    private function viewIndividualConversation(int $user1, int $user2) : Conversation | null
    {
        return Conversation::where('type', 'individual')
            ->join('conversation_user', 'conversations.id', '=', 'conversation_user.conversation_id')
            ->whereIn('conversation_user.user_id', [$user1, $user2])
            ->groupBy('conversations.id')
            ->havingRaw('count(conversation_user.user_id) = 2')
            ->first();
    }
}
