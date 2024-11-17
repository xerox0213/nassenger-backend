<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConversationResource;
use App\Http\Services\ConversationService;

class ConversationController extends Controller
{
    public function index(ConversationService $cs)
    {
        $conversations = $cs->getConversations();

        return ConversationResource::collection($conversations);
    }
}
