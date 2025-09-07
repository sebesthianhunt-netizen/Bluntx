<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_withdraw_success_posts_ledger(): void
    {
        $user = User::factory()->create(['phone' => '+2348000000000']);
        $tx = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'withdraw',
            'status' => 'pending',
            'amount' => 20000,
            'currency' => 'NGN',
            'provider' => 'paystack',
            'reference' => 'WDREF1',
        ]);

        $payload = [
            'event' => 'transfer.success',
            'data' => ['reference' => 'WDREF1', 'status' => 'success'],
        ];
        config(['services.paystack.webhook_secret' => 'testsecret']);
        $this->withHeaders(['x-paystack-signature' => hash_hmac('sha512', json_encode($payload), 'testsecret')])
            ->postJson('/api/v1/webhooks/paystack', $payload)
            ->assertOk();

        $tx->refresh();
        $this->assertEquals('success', $tx->status);
    }
}


