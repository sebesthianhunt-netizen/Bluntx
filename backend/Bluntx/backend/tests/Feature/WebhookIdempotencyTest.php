<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_duplicate_is_ignored(): void
    {
        $user = User::factory()->create(['phone' => '+2348000000000']);
        $tx = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'fund',
            'status' => 'pending',
            'amount' => 10000,
            'currency' => 'NGN',
            'provider' => 'paystack',
            'reference' => 'REF123',
        ]);

        // First success callback
        $payload = [
            'event' => 'charge.success',
            'data' => ['reference' => 'REF123', 'status' => 'success'],
        ];
        config(['services.paystack.webhook_secret' => 'testsecret']);
        $this->withHeaders(['x-paystack-signature' => hash_hmac('sha512', json_encode($payload), 'testsecret')])
            ->postJson('/api/v1/webhooks/paystack', $payload)
            ->assertOk();

        // Duplicate
        $this->withHeaders(['x-paystack-signature' => hash_hmac('sha512', json_encode($payload), 'testsecret')])
            ->postJson('/api/v1/webhooks/paystack', $payload)
            ->assertOk();

        $tx->refresh();
        $this->assertEquals('success', $tx->status);
    }
}


