<?php

namespace Database\Factories;

use App\Models\SupportConversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportConversation>
 */
class SupportConversationFactory extends Factory
{
    protected $model = SupportConversation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'guest_token' => null,
            'status' => 'open',
            'last_message_at' => now(),
        ];
    }
}
