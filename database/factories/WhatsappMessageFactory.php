<?php

namespace Database\Factories;

use App\Enums\WhatsappMessageDirection;
use App\Enums\WhatsappMessageStatus;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhatsappMessage>
 */
class WhatsappMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'whatsapp_conversation_id' => WhatsappConversation::factory(),
            'direction' => WhatsappMessageDirection::Outbound,
            'wa_message_id' => null,
            'body' => fake()->sentence(),
            'template_name' => null,
            'status' => WhatsappMessageStatus::Pending,
        ];
    }
}
