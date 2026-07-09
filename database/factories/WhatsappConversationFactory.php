<?php

namespace Database\Factories;

use App\Models\WhatsappConversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhatsappConversation>
 */
class WhatsappConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone_number' => '62'.fake()->numerify('85#########'),
            'name' => fake()->name(),
            'window_open' => false,
            'last_customer_message_at' => null,
            'last_message_at' => null,
        ];
    }
}
