<?php

namespace Database\Factories;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportMessage>
 */
class SupportMessageFactory extends Factory
{
    protected $model = SupportMessage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'support_conversation_id' => SupportConversation::factory(),
            'admin_id' => null,
            'content' => fake()->sentence(12),
            'read_at' => null,
        ];
    }
}
