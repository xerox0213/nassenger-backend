<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationMessageStoreRequest;
use App\Http\Requests\ConversationMessageUpdateRequest;
use App\Http\Resources\MessageResource;
use App\Http\Services\ConversationMessageService;
use App\Models\Conversation;
use App\Models\Message;

class ConversationMessageController extends Controller
{
    private ConversationMessageService $cms;

    /**
     * @param ConversationMessageService $cms
     */
    public function __construct(ConversationMessageService $cms)
    {
        $this->cms = $cms;
    }

    public function index(Conversation $conversation)
    {
        $conversationMessages = $this->cms->index($conversation);

        return MessageResource::collection($conversationMessages);
    }

    public function store(ConversationMessageStoreRequest $request, Conversation $conversation)
    {
        $messageData = $request->validated();

        $conversationMessage = $this->cms->store($conversation, $messageData);

        return MessageResource::make($conversationMessage)->response()->setStatusCode(201);
    }

    public function update(ConversationMessageUpdateRequest $request, Conversation $conversation, Message $message)
    {
        $newMessageContent = $request->validated('content');

        $this->cms->update($conversation, $message, $newMessageContent);

        return MessageResource::make($message);
    }
}
