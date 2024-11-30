<?php

namespace Database\Factories;

use App\Enums\ConversationType;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'name' => null,
            'type' => ConversationType::INDIVIDUAL->value
        ];
    }

    public function group(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => ConversationType::GROUP
        ]);
    }

    public function namedGroup(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => fake()->name,
            'type' => ConversationType::GROUP->value
        ]);
    }
}
