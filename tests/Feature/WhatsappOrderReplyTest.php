<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\WhatsappMessageDirection;
use App\Models\Client;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class WhatsappOrderReplyTest extends TestCase
{
    use RefreshDatabase;

    private function postWebhook(array $payload): TestResponse
    {
        config(['services.whatsapp.app_secret' => 'test-secret']);

        $body = json_encode($payload);
        $signature = 'sha256='.hash_hmac('sha256', $body, 'test-secret');

        return $this->call('POST', '/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
        ], $body);
    }

    private function webhookPayloadFor(string $orderNumber, string $phone, string $name): array
    {
        return [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'contacts' => [
                                    ['profile' => ['name' => $name]],
                                ],
                                'messages' => [
                                    [
                                        'from' => $phone,
                                        'id' => 'wamid.inbound123',
                                        'text' => [
                                            'body' => "Halo Gerai Dempo, saya ingin memesan:\n- 2x Dimsum Kukus\n\nTotal: Rp 170.000\nKode Pesanan: {$orderNumber}",
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function test_it_auto_replies_and_confirms_a_pending_order(): void
    {
        config([
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.phone_number_id' => '123456',
        ]);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.reply123']]], 200),
        ]);

        $client = Client::factory()->create(['name' => 'Tamu Website', 'phone' => null]);
        $order = Order::factory()->for($client)->create(['status' => OrderStatus::Pending, 'total_amount' => 170000]);
        $menu = Menu::factory()->create(['name' => 'Dimsum Kukus', 'price' => 85000]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $menu->id,
            'menu_name' => 'Dimsum Kukus',
            'quantity' => 2,
            'price' => 85000,
            'subtotal' => 170000,
        ]);

        $response = $this->postWebhook($this->webhookPayloadFor($order->order_number, '6281234567890', 'Budi'));

        $response->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::Confirmed, $order->status);

        $client->refresh();
        $this->assertSame('6281234567890', $client->phone);
        $this->assertSame('Budi', $client->name);

        $this->assertDatabaseHas('whatsapp_messages', [
            'direction' => WhatsappMessageDirection::Outbound->value,
            'wa_message_id' => 'wamid.reply123',
        ]);

        Http::assertSentCount(1);
    }

    public function test_it_does_not_reply_twice_for_the_same_order(): void
    {
        config([
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.phone_number_id' => '123456',
        ]);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.reply123']]], 200),
        ]);

        $client = Client::factory()->create();
        $order = Order::factory()->for($client)->create(['status' => OrderStatus::Pending, 'total_amount' => 170000]);
        OrderItem::factory()->for($order)->create(['menu_name' => 'Dimsum Kukus', 'quantity' => 2]);

        $payload = $this->webhookPayloadFor($order->order_number, '6281234567890', 'Budi');

        $this->postWebhook($payload)->assertOk();
        $this->postWebhook($payload)->assertOk();

        Http::assertSentCount(1);
    }
}
