<?php

namespace Database\Factories;

use App\Enums\MessageType;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'type' => MessageType::TEXT->value,
            'content' => fake()->text(),
            'sent_at' => Carbon::now(),
        ];
    }
}
