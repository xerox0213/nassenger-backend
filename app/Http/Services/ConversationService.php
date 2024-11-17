<?php

namespace App\Http\Services;

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
}
