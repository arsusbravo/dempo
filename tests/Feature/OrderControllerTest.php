<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\WhatsappMessageDirection;
use App\Models\Menu;
use App\Models\Order;
use App\Models\WhatsappMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private function configureWhatsapp(): void
    {
        config([
            'services.whatsapp.number' => '6281111111111',
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.phone_number_id' => '123456',
            'services.whatsapp.order_template_name' => 'order_confirmation',
        ]);
    }

    public function test_it_sends_the_order_confirmation_instantly_via_the_cloud_api(): void
    {
        $this->configureWhatsapp();

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.confirm123']]], 200),
        ]);

        $menuA = Menu::factory()->create(['name' => 'Dimsum Kukus', 'price' => 85000]);
        $menuB = Menu::factory()->create(['name' => 'Risol Renyah', 'price' => 65000]);

        $response = $this->post(route('orders.store'), [
            'name' => 'Budi',
            'country_code' => '62',
            'phone_local' => '81234567890',
            'items' => [
                ['menu_id' => $menuA->id, 'quantity' => 2],
                ['menu_id' => $menuB->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::Confirmed, $order->status);
        $this->assertSame('235000.00', $order->total_amount);

        $this->assertDatabaseHas('clients', [
            'name' => 'Budi',
            'phone' => '6281234567890',
        ]);

        $response->assertRedirect('https://wa.me/6281111111111');

        $message = WhatsappMessage::first();
        $this->assertSame(WhatsappMessageDirection::Outbound, $message->direction);
        $this->assertSame('order_confirmation', $message->template_name);
        $this->assertSame('wamid.confirm123', $message->wa_message_id);

        Http::assertSent(function ($request) use ($order) {
            if ($request['to'] !== '6281234567890'
                || $request['type'] !== 'template'
                || $request['template']['name'] !== 'order_confirmation') {
                return false;
            }

            $parameters = $request['template']['components'][0]['parameters'];

            // Must match the APPROVED template exactly: 3 params (code, items, payment), no name.
            if (count($parameters) !== 3 || $parameters[0]['text'] !== $order->order_number) {
                return false;
            }

            // WhatsApp rejects template parameters containing newlines/tabs or 4+ consecutive spaces (#132018).
            foreach ($parameters as $parameter) {
                if (preg_match('/[\n\t]|\s{4,}/', $parameter['text'])) {
                    return false;
                }
            }

            return true;
        });
    }

    public function test_it_falls_back_to_whatsapp_redirect_when_the_api_call_fails(): void
    {
        $this->configureWhatsapp();

        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Template not approved']], 400),
        ]);

        $menu = Menu::factory()->create(['name' => 'Dimsum Kukus', 'price' => 85000]);

        $response = $this->post(route('orders.store'), [
            'name' => 'Budi',
            'country_code' => '62',
            'phone_local' => '81234567890',
            'items' => [
                ['menu_id' => $menu->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertDatabaseCount('whatsapp_messages', 0);

        $response->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/6281111111111?text=', $location);

        $decoded = urldecode($location);
        $this->assertStringContainsString('1x Dimsum Kukus', $decoded);
        $this->assertStringContainsString($order->order_number, $decoded);
    }

    public function test_it_requires_name_phone_and_at_least_one_item(): void
    {
        $response = $this->post(route('orders.store'), []);

        $response->assertSessionHasErrors(['name', 'country_code', 'phone_local', 'items']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_it_accepts_a_foreign_phone_number(): void
    {
        $this->configureWhatsapp();

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.confirm456']]], 200),
        ]);

        $menu = Menu::factory()->create(['name' => 'Dimsum Kukus', 'price' => 85000]);

        $response = $this->post(route('orders.store'), [
            'name' => 'Jan',
            'country_code' => '31',
            'phone_local' => '612345678',
            'items' => [
                ['menu_id' => $menu->id, 'quantity' => 1],
            ],
        ]);

        $response->assertRedirect('https://wa.me/6281111111111');

        $this->assertDatabaseHas('clients', [
            'name' => 'Jan',
            'phone' => '31612345678',
        ]);

        Http::assertSent(fn ($request) => $request['to'] === '31612345678');
    }

    public function test_it_strips_a_leading_zero_from_the_local_number(): void
    {
        $this->configureWhatsapp();

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.confirm789']]], 200),
        ]);

        $menu = Menu::factory()->create(['name' => 'Dimsum Kukus', 'price' => 85000]);

        $this->post(route('orders.store'), [
            'name' => 'Budi',
            'country_code' => '62',
            'phone_local' => '081234567890',
            'items' => [
                ['menu_id' => $menu->id, 'quantity' => 1],
            ],
        ]);

        $this->assertDatabaseHas('clients', [
            'name' => 'Budi',
            'phone' => '6281234567890',
        ]);
    }

    public function test_it_rejects_an_invalid_phone_number(): void
    {
        $menu = Menu::factory()->create(['name' => 'Dimsum Kukus', 'price' => 85000]);

        $response = $this->post(route('orders.store'), [
            'name' => 'Budi',
            'country_code' => '62',
            'phone_local' => 'not-a-number',
            'items' => [
                ['menu_id' => $menu->id, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('phone_local');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_it_ignores_inactive_menus(): void
    {
        $this->configureWhatsapp();

        $menu = Menu::factory()->create(['is_active' => false]);

        $response = $this->post(route('orders.store'), [
            'name' => 'Budi',
            'country_code' => '62',
            'phone_local' => '81234567890',
            'items' => [
                ['menu_id' => $menu->id, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('orders', 0);
    }
}
