<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;

class WithdrawEscrowTest extends TestCase
{
    use RefreshDatabase;

    private function balanceKobo(int $userId, string $accountType = 'cash', string $currency = 'NGN'): int
    {
        return (int) (DB::table('ledger_entries')
            ->join('ledger_accounts', 'ledger_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('ledger_accounts.owner_type', 'user')
            ->where('ledger_accounts.owner_id', $userId)
            ->where('ledger_accounts.account_type', $accountType)
            ->where('ledger_entries.currency', $currency)
            ->selectRaw("COALESCE(SUM(CASE WHEN ledger_entries.direction = 'debit' THEN ledger_entries.amount ELSE -ledger_entries.amount END), 0) as balance")
            ->value('balance') ?? 0);
    }

    public function test_withdraw_hold_and_cancel_returns_to_cash(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $agent = User::factory()->create();

        // Seed user cash: +50000 kobo (500 NGN)
        $ledger = app(LedgerService::class);
        $ledger->post('seed_fund', [
            ['owner_type' => 'user', 'owner_id' => $user->id, 'account_type' => 'cash', 'direction' => 'debit', 'amount' => 50000, 'currency' => 'NGN'],
            ['owner_type' => 'system', 'owner_id' => 0, 'account_type' => 'clearing', 'direction' => 'credit', 'amount' => 50000, 'currency' => 'NGN'],
        ]);

        $this->assertSame(50000, $this->balanceKobo($user->id, 'cash'));
        $this->assertSame(0, $this->balanceKobo($user->id, 'escrow'));

        Passport::actingAs($user);
        // Request withdraw hold of 100 NGN => 10000 kobo
        $resp = $this->postJson('/api/v1/wallet/withdraw', [
            'amount' => 100,
            'payment_provider' => 'paystack',
            'destination' => [ 'agent_user_id' => $agent->id, 'note' => 'test' ],
        ])->assertOk();
        $ref = $resp->json('reference');
        $this->assertNotEmpty($ref);

        // Cash decreased by 10000, escrow increased by 10000
        $this->assertSame(40000, $this->balanceKobo($user->id, 'cash'));
        $this->assertSame(10000, $this->balanceKobo($user->id, 'escrow'));

        // Cancel by owner
        Passport::actingAs($user);
        $this->postJson("/api/v1/wallet/withdraw/{$ref}/cancel")->assertOk();

        // Escrow -> cash
        $this->assertSame(50000, $this->balanceKobo($user->id, 'cash'));
        $this->assertSame(0, $this->balanceKobo($user->id, 'escrow'));

        $tx = WalletTransaction::where('reference', $ref)->firstOrFail();
        $this->assertSame('cancelled', $tx->status);
    }

    public function test_withdraw_hold_and_confirm_releases_to_agent(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $agent = User::factory()->create();

        $ledger = app(LedgerService::class);
        $ledger->post('seed_fund', [
            ['owner_type' => 'user', 'owner_id' => $user->id, 'account_type' => 'cash', 'direction' => 'debit', 'amount' => 200000, 'currency' => 'NGN'],
            ['owner_type' => 'system', 'owner_id' => 0, 'account_type' => 'clearing', 'direction' => 'credit', 'amount' => 200000, 'currency' => 'NGN'],
        ]);

        Passport::actingAs($user);
        $resp = $this->postJson('/api/v1/wallet/withdraw', [
            'amount' => 150,
            'payment_provider' => 'paystack',
            'destination' => [ 'agent_user_id' => $agent->id, 'note' => 'cashout' ],
        ])->assertOk();
        $ref = $resp->json('reference');

        // balances after hold: user cash -15000, escrow +15000
        $this->assertSame(185000, $this->balanceKobo($user->id, 'cash'));
        $this->assertSame(15000, $this->balanceKobo($user->id, 'escrow'));
        $this->assertSame(0, $this->balanceKobo($agent->id, 'cash'));

        // Confirm by agent
        Passport::actingAs($agent);
        $this->postJson("/api/v1/wallet/withdraw/{$ref}/confirm")->assertOk();

        // Escrow reduced to 0, agent cash +15000
        $this->assertSame(185000, $this->balanceKobo($user->id, 'cash'));
        $this->assertSame(0, $this->balanceKobo($user->id, 'escrow'));
        $this->assertSame(15000, $this->balanceKobo($agent->id, 'cash'));

        $tx = WalletTransaction::where('reference', $ref)->firstOrFail();
        $this->assertSame('success', $tx->status);
        $this->assertSame($agent->id, (int)($tx->meta['agent_user_id'] ?? 0));
    }
}


