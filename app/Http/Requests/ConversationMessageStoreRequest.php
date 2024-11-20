<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConversationMessageStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'initial_message_id' => ['sometimes', 'integer', 'exists:messages,id']
        ];
    }
}
