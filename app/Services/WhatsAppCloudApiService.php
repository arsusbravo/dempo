<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsAppCloudApiService
{
    public function isConfigured(): bool
    {
        return filled(config('services.whatsapp.access_token'))
            && filled(config('services.whatsapp.phone_number_id'));
    }

    /**
     * Send a pre-approved template message. Required for the first message to a contact,
     * or any message sent outside the 24-hour customer service window.
     *
     * @param  array<int, string>  $bodyParameters
     * @return array<string, mixed>
     */
    public function sendTemplateMessage(string $to, string $templateName, array $bodyParameters = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => config('services.whatsapp.template_language')],
            ],
        ];

        if ($bodyParameters !== []) {
            $payload['template']['components'] = [
                [
                    'type' => 'body',
                    'parameters' => array_map(
                        fn (string $value): array => ['type' => 'text', 'text' => $value],
                        $bodyParameters
                    ),
                ],
            ];
        }

        return $this->send($payload);
    }

    /**
     * Send a free-form text message. Only allowed within an open 24-hour customer service window.
     *
     * @return array<string, mixed>
     */
    public function sendTextMessage(string $to, string $body): array
    {
        return $this->send([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $body],
        ]);
    }

    /**
     * Verify that an incoming webhook request really came from Meta.
     */
    public function verifySignature(string $payload, ?string $signatureHeader): bool
    {
        $appSecret = config('services.whatsapp.app_secret');

        if (blank($appSecret) || blank($signatureHeader)) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $payload, $appSecret);

        return hash_equals($expected, $signatureHeader);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function send(array $payload): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('WhatsApp Cloud API is not configured yet.');
        }

        $phoneNumberId = config('services.whatsapp.phone_number_id');

        $response = Http::withToken(config('services.whatsapp.access_token'))
            ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", $payload);

        if ($response->failed()) {
            Log::error('WhatsApp Cloud API send failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException('Gagal mengirim pesan WhatsApp: '.$response->json('error.message', 'unknown error'));
        }

        return $response->json();
    }
}
