<?php

namespace App\Http\Services;

use App\Models\Conversation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function getConversations()
    {
        return Auth::user()
            ->conversations()
            ->join('messages', 'messages.conversation_id', '=', 'conversations.id')
            ->groupBy('conversations.id', 'conversations.name', 'conversations.type')
            ->orderBy(DB::raw('max(messages.sent_at)'), 'desc')
            ->select('conversations.*')
            ->simplePaginate(10);
    }

    public function deleteConversation(Conversation $conversation): void
    {
        $userId = Auth::id();
        $user = $conversation->users()->findOrFail($userId);

        $now = Carbon::now();
        $user->pivot->deleted_at = $now;
        $user->pivot->last_deleted_at = $now;
        $user->pivot->save();
    }
}
