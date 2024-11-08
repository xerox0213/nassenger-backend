<?php

namespace App\Models;

use App\Enums\MessageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    const CREATED_AT = 'sent_at';
    const UPDATED_AT = null;
    protected $fillable = [
        'type',
        'content',
        'conversation_id'
    ];

    protected function casts(): array
    {
        return [
            'type' => MessageType::class
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
