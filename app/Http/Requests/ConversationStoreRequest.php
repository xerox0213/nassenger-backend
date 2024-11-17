<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConversationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'list', 'min:1'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id']
        ];
    }
}
