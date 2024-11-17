<?php

namespace App\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Message */
class LastMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'sent_at' => $this->sent_at,
            'author' => LastMessageAuthorResource::make($this->user),
            'you' => $this->user->id == $request->user()->id
        ];
    }
}
