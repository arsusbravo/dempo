<?php

namespace App\Http\Controllers;

use App\Enums\WhatsappMessageDirection;
use App\Enums\WhatsappMessageStatus;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class WhatsappChatController extends Controller
{
    public function store(Request $request, WhatsAppCloudApiService $whatsapp): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'country_code' => ['required', 'string', 'regex:/^[0-9]{1,4}$/'],
            'phone_local' => ['required', 'string', 'regex:/^[0-9]{5,13}$/'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $phoneNumber = combine_country_phone($validated['country_code'], $validated['phone_local']);

        $conversation = WhatsappConversation::firstOrCreate(
            ['phone_number' => $phoneNumber],
            ['name' => $validated['name']]
        );

        $conversation->update(['name' => $validated['name']]);

        $message = WhatsappMessage::create([
            'whatsapp_conversation_id' => $conversation->id,
            'direction' => WhatsappMessageDirection::Outbound,
            'template_name' => config('services.whatsapp.template_name'),
            'body' => $validated['message'],
            'status' => WhatsappMessageStatus::Pending,
        ]);

        try {
            $response = $whatsapp->sendTemplateMessage($phoneNumber, config('services.whatsapp.template_name'), [
                $validated['name'],
                $validated['message'],
            ]);

            $message->update([
                'wa_message_id' => $response['messages'][0]['id'] ?? null,
                'status' => WhatsappMessageStatus::Sent,
            ]);

            $conversation->update(['last_message_at' => now()]);

            return back()->with('whatsapp_chat_sent', true);
        } catch (Throwable $e) {
            report($e);

            $message->update(['status' => WhatsappMessageStatus::Failed]);

            return back()->with('whatsapp_chat_error', 'Gagal mengirim pesan. Silakan coba lagi atau hubungi kami langsung.');
        }
    }
}
