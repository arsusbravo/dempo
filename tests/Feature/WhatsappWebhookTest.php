<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_returns_challenge_when_token_matches(): void
    {
        config(['services.whatsapp.webhook_verify_token' => 'secret-token']);

        $response = $this->get('/webhooks/whatsapp?'.http_build_query([
            'hub.mode' => 'subscribe',
            'hub.verify_token' => 'secret-token',
            'hub.challenge' => 'challenge-123',
        ]));

        $response->assertOk();
        $response->assertSeeText('challenge-123');
    }

    public function test_verify_rejects_wrong_token(): void
    {
        config(['services.whatsapp.webhook_verify_token' => 'secret-token']);

        $response = $this->get('/webhooks/whatsapp?'.http_build_query([
            'hub.mode' => 'subscribe',
            'hub.verify_token' => 'wrong-token',
            'hub.challenge' => 'challenge-123',
        ]));

        $response->assertStatus(403);
    }

    public function test_receive_rejects_request_with_invalid_signature(): void
    {
        config(['services.whatsapp.app_secret' => 'app-secret']);

        $response = $this->postJson('/webhooks/whatsapp', ['entry' => []], [
            'X-Hub-Signature-256' => 'sha256=invalid',
        ]);

        $response->assertStatus(403);
    }
}
