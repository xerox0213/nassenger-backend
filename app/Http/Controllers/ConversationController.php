<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConversationResource;
use App\Http\Services\ConversationService;
use App\Models\Conversation;

class ConversationController extends Controller
{
    public function index(ConversationService $cs)
    {
        $conversations = $cs->getConversations();

        return ConversationResource::collection($conversations);
    }

    public function destroy(Conversation $conversation, ConversationService $cs)
    {
        $cs->deleteConversation($conversation);

        return response()->json(null, 204);
    }
}
