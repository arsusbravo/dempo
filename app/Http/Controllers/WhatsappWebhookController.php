<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\WhatsappMessageDirection;
use App\Enums\WhatsappMessageStatus;
use App\Models\Order;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Services\OrderMessageBuilder;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class WhatsappWebhookController extends Controller
{
    /**
     * Handle Meta's one-time webhook verification handshake (GET).
     */
    public function verify(Request $request): Response
    {
        $token = config('services.whatsapp.webhook_verify_token');

        if (
            $request->query('hub_mode') === 'subscribe'
            && filled($token)
            && hash_equals((string) $token, (string) $request->query('hub_verify_token'))
        ) {
            return response((string) $request->query('hub_challenge'), 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Invalid verify token', 403);
    }

    /**
     * Receive incoming messages and status updates from Meta (POST).
     */
    public function receive(Request $request, WhatsAppCloudApiService $whatsapp): Response
    {
        if (! $whatsapp->verifySignature($request->getContent(), $request->header('X-Hub-Signature-256'))) {
            return response('Invalid signature', 403);
        }

        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                foreach ($value['messages'] ?? [] as $message) {
                    $this->storeInboundMessage($value, $message, $whatsapp);
                }

                foreach ($value['statuses'] ?? [] as $status) {
                    $this->updateMessageStatus($status);
                }
            }
        }

        return response('OK', 200);
    }

    /**
     * @param  array<string, mixed>  $value
     * @param  array<string, mixed>  $message
     */
    private function storeInboundMessage(array $value, array $message, WhatsAppCloudApiService $whatsapp): void
    {
        $waMessageId = $message['id'] ?? null;

        // Meta retries webhook deliveries on failure/timeout — skip if we've already processed this exact message.
        if ($waMessageId && WhatsappMessage::where('wa_message_id', $waMessageId)->exists()) {
            return;
        }

        $phoneNumber = $message['from'];
        $contactName = $value['contacts'][0]['profile']['name'] ?? null;

        $conversation = WhatsappConversation::firstOrCreate(
            ['phone_number' => $phoneNumber],
            ['name' => $contactName]
        );

        $conversation->fill([
            'name' => $contactName ?? $conversation->name,
            'window_open' => true,
            'last_customer_message_at' => now(),
            'last_message_at' => now(),
        ])->save();

        $body = $message['text']['body'] ?? null;

        WhatsappMessage::create([
            'whatsapp_conversation_id' => $conversation->id,
            'direction' => WhatsappMessageDirection::Inbound,
            'wa_message_id' => $waMessageId,
            'body' => $body,
            'status' => WhatsappMessageStatus::Delivered,
        ]);

        if ($body) {
            $this->handleOrderCode($body, $phoneNumber, $contactName, $conversation, $whatsapp);
        }
    }

    /**
     * If the message contains a pending order's code, back-fill the client's real contact
     * details and auto-reply with an order recap + payment instructions. Free-form (no
     * template needed) since the customer just messaged us, which opens the 24h window.
     */
    private function handleOrderCode(
        string $body,
        string $phoneNumber,
        ?string $contactName,
        WhatsappConversation $conversation,
        WhatsAppCloudApiService $whatsapp
    ): void {
        if (! preg_match('/ORD-[A-Z0-9]+/', $body, $matches)) {
            return;
        }

        $order = Order::with(['items', 'client'])
            ->where('order_number', $matches[0])
            ->where('status', OrderStatus::Pending)
            ->first();

        if (! $order) {
            return;
        }

        $order->client->update([
            'phone' => $phoneNumber,
            'name' => $contactName ?: $order->client->name,
        ]);

        $reply = $this->buildOrderConfirmationMessage($order, $contactName);

        try {
            $response = $whatsapp->sendTextMessage($phoneNumber, $reply);

            WhatsappMessage::create([
                'whatsapp_conversation_id' => $conversation->id,
                'direction' => WhatsappMessageDirection::Outbound,
                'wa_message_id' => $response['messages'][0]['id'] ?? null,
                'body' => $reply,
                'status' => WhatsappMessageStatus::Sent,
            ]);

            $order->update(['status' => OrderStatus::Confirmed]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function buildOrderConfirmationMessage(Order $order, ?string $contactName): string
    {
        return 'Terima kasih atas pesanan Anda'.($contactName ? ", {$contactName}" : '')."! 🙏\n\n"
            ."Kode Pesanan: {$order->order_number}\n"
            .OrderMessageBuilder::itemsBlock($order)."\n\n"
            ."Mohon lakukan pembayaran ke:\n"
            .OrderMessageBuilder::paymentBlock()."\n\n"
            .'Setelah pembayaran diterima, pesanan Anda akan segera kami proses. Jika ada pertanyaan, silakan balas pesan ini. Terima kasih! 😊';
    }

    /**
     * @param  array<string, mixed>  $status
     */
    private function updateMessageStatus(array $status): void
    {
        $waMessageId = $status['id'] ?? null;
        $newStatus = $status['status'] ?? null;

        if (! $waMessageId || ! $newStatus) {
            return;
        }

        WhatsappMessage::where('wa_message_id', $waMessageId)->update([
            'status' => $newStatus,
        ]);
    }
}
