<?php

namespace App\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Message */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'content' => $this->content,
            'sent_at' => $this->sent_at,
            'author' => MessageAuthorResource::make($this->user),
            'initial_message' => InitialMessageResource::make($this->initialMessage)
        ];
    }
}
