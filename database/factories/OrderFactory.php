<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'order_number' => 'ORD-'.strtoupper(Str::random(8)),
            'status' => OrderStatus::Pending,
            'event_date' => fake()->dateTimeBetween('now', '+2 months'),
            'delivery_address' => fake()->address(),
            'notes' => null,
            'total_amount' => 0,
        ];
    }
}
