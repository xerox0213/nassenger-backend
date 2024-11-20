<?php

namespace App\Http\Services;

use App\Enums\MessageType;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ConversationMessageService
{
    public function index(Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        return $conversation->messages;
    }

    public function store(Conversation $conversation, array $messageData)
    {
        Gate::authorize('view', $conversation);

        $messageData['type'] = MessageType::TEXT->value;

        $message = Message::make($messageData);

        $message->user()->associate(Auth::user());

        $message->conversation()->associate($conversation);

        if (isset($message['initial_message_id'])) {
            $initialMessage = Message::find($messageData['initial_message_id']);
            $message->initialMessage()->associate($initialMessage);
        }

        $message->save();

        return $message;
    }
}
