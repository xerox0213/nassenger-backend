<?php

namespace App\Models;

use App\Enums\MessageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    const CREATED_AT = 'sent_at';
    const UPDATED_AT = null;
    protected $fillable = [
        'type',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'type' => MessageType::class
        ];
    }
}
