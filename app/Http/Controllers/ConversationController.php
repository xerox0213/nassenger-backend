<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationStoreRequest;
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

    public function store(ConversationStoreRequest $request, ConversationService $cs)
    {
        $userIds = collect($request->validated('user_ids'));

        $conversation = $cs->store($userIds);

        return ConversationResource::make($conversation)->response()->setStatusCode(201);
    }

    public function destroy(Conversation $conversation, ConversationService $cs)
    {
        $cs->deleteConversation($conversation);

        return response()->json(null, 204);
    }
}
