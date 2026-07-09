<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 15000, 250000);

        return [
            'order_id' => Order::factory(),
            'menu_id' => Menu::factory(),
            'menu_name' => ucwords(fake()->words(3, true)),
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $quantity * $price,
        ];
    }

    /**
     * Keep the snapshot fields (menu_name, price, subtotal) in sync with the related Menu.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (OrderItem $orderItem) {
            $menu = $orderItem->menu_id ? Menu::find($orderItem->menu_id) : null;

            if ($menu) {
                $orderItem->menu_name = $menu->name;
                $orderItem->price = $menu->price ?? $orderItem->price;
                $orderItem->subtotal = $orderItem->price * $orderItem->quantity;
            }
        });
    }
}
