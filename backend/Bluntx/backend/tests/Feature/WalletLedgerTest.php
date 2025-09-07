<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_balance_computed_from_ledger(): void
    {
        $user = User::factory()->create(['phone' => '+2348000000000']);
        $token = $user->createToken('t')->accessToken;

        $ledger = app(LedgerService::class);
        $ledger->post('fund', [
            ['owner_type' => 'user', 'owner_id' => $user->id, 'account_type' => 'cash', 'direction' => 'debit', 'amount' => 50000, 'currency' => 'NGN'],
            ['owner_type' => 'provider', 'owner_id' => 0, 'account_type' => 'cash', 'direction' => 'credit', 'amount' => 50000, 'currency' => 'NGN'],
        ]);

        $resp = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/v1/wallet')
            ->assertOk()
            ->json();

        $this->assertEquals(500, $resp['cash_balance']);
    }
}


