<?php

namespace Tests\Feature;

use App\Enums\WhatsappMessageStatus;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsappChatTest extends TestCase
{
    use RefreshDatabase;

    private function configureWhatsapp(): void
    {
        config([
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.phone_number_id' => '123456',
            'services.whatsapp.template_name' => 'website_inquiry',
            'services.whatsapp.template_language' => 'id',
        ]);
    }

    public function test_it_creates_a_conversation_and_sends_a_template_message(): void
    {
        $this->configureWhatsapp();

        Http::fake([
            'graph.facebook.com/*' => Http::response([
                'messages' => [['id' => 'wamid.test123']],
            ], 200),
        ]);

        $response = $this->post(route('whatsapp.start'), [
            'name' => 'Budi',
            'country_code' => '62',
            'phone_local' => '81234567890',
            'message' => 'Saya ingin bertanya tentang menu dimsum.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('whatsapp_chat_sent', true);

        $this->assertDatabaseHas('whatsapp_conversations', [
            'phone_number' => '6281234567890',
            'name' => 'Budi',
        ]);

        $conversation = WhatsappConversation::first();
        $this->assertNotNull($conversation);

        $message = WhatsappMessage::where('whatsapp_conversation_id', $conversation->id)->first();
        $this->assertSame(WhatsappMessageStatus::Sent, $message->status);
        $this->assertSame('wamid.test123', $message->wa_message_id);

        Http::assertSent(fn ($request) => str_contains($request->url(), '123456/messages')
            && $request['to'] === '6281234567890'
            && $request['type'] === 'template'
        );
    }

    public function test_it_marks_the_message_failed_when_the_api_call_fails(): void
    {
        $this->configureWhatsapp();

        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Invalid parameter']], 400),
        ]);

        $response = $this->post(route('whatsapp.start'), [
            'name' => 'Siti',
            'country_code' => '62',
            'phone_local' => '81234567890',
            'message' => 'Halo, ada paket untuk 50 orang?',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('whatsapp_chat_error');

        $message = WhatsappMessage::first();
        $this->assertSame(WhatsappMessageStatus::Failed, $message->status);
    }

    public function test_it_validates_required_fields(): void
    {
        $this->configureWhatsapp();

        $response = $this->post(route('whatsapp.start'), []);

        $response->assertSessionHasErrors(['name', 'country_code', 'phone_local', 'message']);
    }
}
