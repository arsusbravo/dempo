<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WebsiteText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CateringSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_belongs_to_its_category(): void
    {
        $category = MenuCategory::factory()->create();
        $menu = Menu::factory()->for($category, 'category')->create();

        $this->assertTrue($menu->category->is($category));
        $this->assertTrue($category->menus->contains($menu));
    }

    public function test_order_with_multiple_items_relates_back_to_its_client(): void
    {
        $client = Client::factory()->create();
        $order = Order::factory()->for($client, 'client')->create();
        OrderItem::factory()->count(2)->for($order)->create();

        $this->assertTrue($order->client->is($client));
        $this->assertCount(2, $order->items);
    }

    public function test_deleting_a_menu_nulls_the_order_item_but_keeps_the_snapshot(): void
    {
        $menu = Menu::factory()->create(['name' => 'Dimsum Kukus', 'price' => 85000]);
        $orderItem = OrderItem::factory()->for($menu)->create([
            'menu_name' => $menu->name,
            'price' => $menu->price,
        ]);

        $menu->delete();
        $orderItem->refresh();

        $this->assertNull($orderItem->menu_id);
        $this->assertSame('Dimsum Kukus', $orderItem->menu_name);
        $this->assertSame('85000.00', $orderItem->price);
    }

    public function test_website_text_can_be_looked_up_by_key(): void
    {
        WebsiteText::create([
            'key' => 'home.hero.headline',
            'group' => 'home',
            'label' => 'Hero Headline',
            'value' => 'Catering Lezat untuk Setiap Momen Spesial Anda',
        ]);

        $text = WebsiteText::where('key', 'home.hero.headline')->first();

        $this->assertNotNull($text);
        $this->assertSame('Catering Lezat untuk Setiap Momen Spesial Anda', $text->value);
    }
}
