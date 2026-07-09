<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\WhatsappMessageDirection;
use App\Enums\WhatsappMessageStatus;
use App\Models\Client;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Services\OrderMessageBuilder;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class OrderController extends Controller
{
    public function store(Request $request, WhatsAppCloudApiService $whatsapp): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'country_code' => ['required', 'string', 'regex:/^[0-9]{1,4}$/'],
            'phone_local' => ['required', 'string', 'regex:/^[0-9]{5,13}$/'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $menus = Menu::whereIn('id', collect($validated['items'])->pluck('menu_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $lineItems = [];
        $total = 0;

        foreach ($validated['items'] as $item) {
            $menu = $menus->get($item['menu_id']);

            if (! $menu) {
                continue;
            }

            $subtotal = $menu->price * $item['quantity'];
            $total += $subtotal;

            $lineItems[] = [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'quantity' => $item['quantity'],
                'price' => $menu->price,
                'subtotal' => $subtotal,
            ];
        }

        if ($lineItems === []) {
            return back()->withErrors(['items' => 'Menu yang dipilih tidak tersedia.']);
        }

        $phoneNumber = combine_country_phone($validated['country_code'], $validated['phone_local']);

        $client = Client::create(['name' => $validated['name'], 'phone' => $phoneNumber]);

        $order = Order::create([
            'client_id' => $client->id,
            'order_number' => $this->generateOrderNumber(),
            'status' => OrderStatus::Pending,
            'total_amount' => $total,
        ]);

        foreach ($lineItems as $lineItem) {
            OrderItem::create(['order_id' => $order->id, ...$lineItem]);
        }

        $order->load('items');

        if ($this->sendInstantConfirmation($order, $phoneNumber, $validated['name'], $whatsapp)) {
            // Message is already sent — open straight into the chat (no ?text=) so there's
            // nothing to edit or manually send, just the confirmation already sitting there.
            return redirect()->away('https://wa.me/'.config('services.whatsapp.number'));
        }

        // Cloud API send failed (e.g. template not approved yet) — fall back to letting
        // the customer send the order themselves via their own WhatsApp app.
        return redirect()->away($this->buildFallbackWhatsappUrl($order));
    }

    /**
     * Send the order confirmation immediately via the Cloud API, so it can't be
     * edited or left unsent by the customer. Returns false on any failure so the
     * caller can fall back to the wa.me redirect.
     */
    private function sendInstantConfirmation(Order $order, string $phoneNumber, string $name, WhatsAppCloudApiService $whatsapp): bool
    {
        $itemsBlock = OrderMessageBuilder::itemsBlock($order);
        $paymentBlock = OrderMessageBuilder::paymentBlock();

        try {
            // Must match the APPROVED template exactly: 3 params (code, items, payment) —
            // no name variable, and template parameters can't contain newlines, so the
            // single-line variants are used here even though the multi-line versions are
            // used below for the stored record.
            $response = $whatsapp->sendTemplateMessage(
                $phoneNumber,
                config('services.whatsapp.order_template_name'),
                [
                    $order->order_number,
                    OrderMessageBuilder::itemsBlockInline($order),
                    OrderMessageBuilder::paymentBlockInline(),
                ]
            );

            $conversation = WhatsappConversation::firstOrCreate(
                ['phone_number' => $phoneNumber],
                ['name' => $name]
            );

            $conversation->update(['name' => $name, 'last_message_at' => now()]);

            WhatsappMessage::create([
                'whatsapp_conversation_id' => $conversation->id,
                'direction' => WhatsappMessageDirection::Outbound,
                'wa_message_id' => $response['messages'][0]['id'] ?? null,
                'template_name' => config('services.whatsapp.order_template_name'),
                'body' => "{$itemsBlock}\n\n{$paymentBlock}",
                'status' => WhatsappMessageStatus::Sent,
            ]);

            $order->update(['status' => OrderStatus::Confirmed]);

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    private function buildFallbackWhatsappUrl(Order $order): string
    {
        $message = "Halo Gerai Dempo, saya ingin memesan.\n\n"
            .OrderMessageBuilder::itemsBlock($order)
            ."\nKode Pesanan: {$order->order_number}";

        return 'https://wa.me/'.config('services.whatsapp.number').'?text='.urlencode($message);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('ymd').strtoupper(Str::random(4));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
