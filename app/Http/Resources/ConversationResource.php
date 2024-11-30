<?php

namespace App\Http\Resources;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Conversation */
class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->user()->id;

        return [
            'id' => $this->id,
            'name' => $this->name != null ? $this->name : $this->nameConversation($userId),
            'type' => $this->type,
            'last_message' => LastMessageResource::make($this->messages()->latest()->first()),
            'contacts' => ContactResource::collection($this->users()->where('id', '!=', $userId)->get())
        ];
    }

    private function nameConversation($currUserId): string
    {
        return $this->users()->where('id', '!=', $currUserId)->implode('firstname', ', ');
    }
}
