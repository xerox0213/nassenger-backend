<?php

namespace App\Http\Services;

use App\Models\Conversation;
use Illuminate\Support\Facades\Gate;

class ConversationMessageService
{
    public function index(Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        return $conversation->messages;
    }
}
