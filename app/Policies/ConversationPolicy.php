<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversationPolicy
{
    use HandlesAuthorization;

    private function participate(User $user, Conversation $conversation): bool
    {
        return $conversation->users()->find($user->id) != null;
    }

    public function view(User $user, Conversation $conversation)
    {
        return $this->participate($user, $conversation);
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $this->participate($user, $conversation);
    }
}
