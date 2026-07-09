<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WebsiteText;

class OrderMessageBuilder
{
    /**
     * Itemized order lines + total, e.g. "Detail Pesanan:\n- 2x Dimsum Kukus\n\nTotal: Rp 235.000".
     */
    public static function itemsBlock(Order $order): string
    {
        $lines = $order->items->map(fn ($item) => "- {$item->quantity}x {$item->menu_name}")->implode("\n");

        return "Detail Pesanan:\n{$lines}\n\nTotal: Rp ".number_format((float) $order->total_amount, 0, ',', '.');
    }

    /**
     * Bank transfer details pulled from the editable website_texts (group "payment").
     */
    public static function paymentBlock(): string
    {
        $bankName = WebsiteText::where('key', 'payment.bank_name')->value('value');
        $accountNumber = WebsiteText::where('key', 'payment.bank_account_number')->value('value');
        $accountHolder = WebsiteText::where('key', 'payment.bank_account_holder')->value('value');

        return "Bank {$bankName}\nNo. Rek: {$accountNumber}\na.n. {$accountHolder}";
    }

    /**
     * Single-line version of itemsBlock() for use as a WhatsApp template parameter —
     * template parameters may not contain newline/tab characters.
     */
    public static function itemsBlockInline(Order $order): string
    {
        $lines = $order->items->map(fn ($item) => "{$item->quantity}x {$item->menu_name}")->implode(', ');

        return "{$lines} (Total: Rp ".number_format((float) $order->total_amount, 0, ',', '.').')';
    }

    /**
     * Single-line version of paymentBlock() for use as a WhatsApp template parameter.
     */
    public static function paymentBlockInline(): string
    {
        $bankName = WebsiteText::where('key', 'payment.bank_name')->value('value');
        $accountNumber = WebsiteText::where('key', 'payment.bank_account_number')->value('value');
        $accountHolder = WebsiteText::where('key', 'payment.bank_account_holder')->value('value');

        return "Bank {$bankName}, No. Rek: {$accountNumber} a.n. {$accountHolder}";
    }
}
