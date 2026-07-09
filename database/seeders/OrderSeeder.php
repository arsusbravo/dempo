<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $menus = Menu::all();

        if ($clients->isEmpty() || $menus->isEmpty()) {
            return;
        }

        Order::factory()
            ->count(3)
            ->create(['client_id' => fn () => $clients->random()->id, 'order_number' => fn () => 'ORD-'.strtoupper(Str::random(8))])
            ->each(function (Order $order) use ($menus) {
                $items = $menus->random(min(2, $menus->count()));
                $total = 0;

                foreach ($items as $menu) {
                    $quantity = fake()->numberBetween(1, 5);
                    $subtotal = $menu->price * $quantity;
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'quantity' => $quantity,
                        'price' => $menu->price,
                        'subtotal' => $subtotal,
                    ]);
                }

                $order->update(['total_amount' => $total]);
            });
    }
}
