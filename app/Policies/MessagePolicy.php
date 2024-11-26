<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Message $message): bool
    {
        return $message->user->id == $user->id;
    }
}
