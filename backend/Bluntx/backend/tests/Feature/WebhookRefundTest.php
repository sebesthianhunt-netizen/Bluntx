<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_refund_reverses_fund_ledger(): void
    {
        $user = User::factory()->create(['phone' => '+2348000000000']);
        $tx = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'fund',
            'status' => 'pending',
            'amount' => 15000,
            'currency' => 'NGN',
            'provider' => 'paystack',
            'reference' => 'FUNDREF1',
        ]);

        // First: success
        $success = ['event' => 'charge.success', 'data' => ['reference' => 'FUNDREF1', 'status' => 'success']];
        config(['services.paystack.webhook_secret' => 'testsecret']);
        $sig = hash_hmac('sha512', json_encode($success), 'testsecret');
        $this->withHeaders(['x-paystack-signature' => $sig])
            ->postJson('/api/v1/webhooks/paystack', $success)
            ->assertOk();

        // Refund event
        $refund = ['event' => 'charge.refund', 'data' => ['reference' => 'FUNDREF1']];
        $sig2 = hash_hmac('sha512', json_encode($refund), 'testsecret');
        $this->withHeaders(['x-paystack-signature' => $sig2])
            ->postJson('/api/v1/webhooks/paystack', $refund)
            ->assertOk();

        $tx->refresh();
        $this->assertEquals('success', $tx->status);
    }
}


