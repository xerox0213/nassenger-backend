<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Http\Services\ConversationMessageService;
use App\Models\Conversation;

class ConversationMessageController extends Controller
{
    public function index(ConversationMessageService $cms, Conversation $conversation)
    {
        $conversationMessages = $cms->index($conversation);

        return MessageResource::collection($conversationMessages);
    }
}
