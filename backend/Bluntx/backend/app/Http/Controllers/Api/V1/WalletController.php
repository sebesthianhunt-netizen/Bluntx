<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\LedgerService;
// use App\Services\Payment\ProviderClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $userId = $user->id;
        $currency = 'NGN';

        // Ensure wallet row exists for points tracking
        $wallet = Wallet::firstOrCreate(['user_id' => $userId, 'currency' => $currency]);

        // Compute cash & escrow from ledger for accuracy
        $cashBalanceKobo = (int) (DB::table('ledger_entries')
            ->join('ledger_accounts', 'ledger_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('ledger_accounts.owner_type', 'user')
            ->where('ledger_accounts.owner_id', $userId)
            ->where('ledger_accounts.account_type', 'cash')
            ->where('ledger_entries.currency', $currency)
            ->selectRaw("COALESCE(SUM(CASE WHEN ledger_entries.direction = 'debit' THEN ledger_entries.amount ELSE -ledger_entries.amount END), 0) as balance")
            ->value('balance') ?? 0);

        $escrowBalanceKobo = (int) (DB::table('ledger_entries')
            ->join('ledger_accounts', 'ledger_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('ledger_accounts.owner_type', 'user')
            ->where('ledger_accounts.owner_id', $userId)
            ->where('ledger_accounts.account_type', 'escrow')
            ->where('ledger_entries.currency', $currency)
            ->selectRaw("COALESCE(SUM(CASE WHEN ledger_entries.direction = 'debit' THEN ledger_entries.amount ELSE -ledger_entries.amount END), 0) as balance")
            ->value('balance') ?? 0);

        return response()->json([
            'cash_balance' => (int) ($cashBalanceKobo / 100),
            'points_balance' => (int) $wallet->points_balance,
            'escrow_balance' => (int) ($escrowBalanceKobo / 100),
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $userId = $user->id;
        $tx = WalletTransaction::where('user_id', $userId)->orderByDesc('id')->limit(50)->get();
        return response()->json(['data' => $tx]);
    }

    public function showTx($reference)
    {
        $tx = WalletTransaction::where('reference', $reference)->firstOrFail();
        return response()->json([
            'reference' => $tx->reference,
            'status' => $tx->status,
            'type' => $tx->type,
            'amount' => (int) $tx->amount,
            'currency' => $tx->currency,
            'provider' => $tx->provider,
            'created_at' => $tx->created_at,
            'updated_at' => $tx->updated_at,
        ]);
    }

    public function fund(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_provider' => 'required|string|in:paystack,flutterwave,monnify',
        ]);
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $userId = $user->id;
        $amountKobo = (int) ($data['amount'] * 100);
        $reference = 'BLVK_' . Str::uuid();

        // Record intent
        WalletTransaction::create([
            'user_id' => $userId,
            'type' => 'fund',
            'status' => 'pending',
            'amount' => $amountKobo,
            'currency' => 'NGN',
            'provider' => $data['payment_provider'],
            'reference' => (string) $reference,
        ]);

        // In real flow, return provider checkout URL then confirm via webhook.
        return response()->json([
            'success' => true,
            'message' => 'Payment initiated',
            'data' => [
                'payment_url' => 'https://checkout.example.com',
                'reference' => (string) $reference,
                'amount' => $data['amount'],
            ],
        ]);
    }

    public function withdraw(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_provider' => 'required|string|in:paystack,flutterwave,monnify',
            'destination' => 'required|array',
            'destination.agent_user_id' => 'required|integer|min:1',
        ]);
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $userId = $user->id;
        $amountKobo = (int) ($data['amount'] * 100);

        // Ensure sufficient cash balance before placing hold to escrow
        $cashBalanceKobo = (int) (DB::table('ledger_entries')
            ->join('ledger_accounts', 'ledger_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('ledger_accounts.owner_type', 'user')
            ->where('ledger_accounts.owner_id', $userId)
            ->where('ledger_accounts.account_type', 'cash')
            ->where('ledger_entries.currency', 'NGN')
            ->selectRaw("COALESCE(SUM(CASE WHEN ledger_entries.direction = 'debit' THEN ledger_entries.amount ELSE -ledger_entries.amount END), 0) as balance")
            ->value('balance') ?? 0);
        abort_if($cashBalanceKobo < $amountKobo, 422, 'Insufficient balance');

        $withdrawTx = WalletTransaction::create([
            'user_id' => $userId,
            'type' => 'withdraw',
            'status' => 'pending',
            'amount' => $amountKobo,
            'currency' => 'NGN',
            'provider' => $data['payment_provider'],
            'reference' => 'BLVK_' . uniqid(),
            'meta' => $data['destination'],
        ]);

        // Move on-site hold: user cash -> user escrow
        $ledger->post('withdraw_hold', [
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'cash',   'direction' => 'credit', 'amount' => $amountKobo, 'currency' => 'NGN', 'reference' => $withdrawTx->reference],
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'escrow', 'direction' => 'debit',  'amount' => $amountKobo, 'currency' => 'NGN', 'reference' => $withdrawTx->reference],
        ], 'NGN', $withdrawTx->reference);

        return response()->json(['success' => true, 'reference' => $withdrawTx->reference]);
    }

    public function withdrawConfirm(string $reference, Request $request, LedgerService $ledger)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $tx = WalletTransaction::where('reference', $reference)->where('type', 'withdraw')->firstOrFail();
        abort_if($tx->status !== 'pending', 422, 'Invalid state');
        abort_if((int)$tx->user_id !== (int)$user->id && (int)($tx->meta['agent_user_id'] ?? 0) !== (int)$user->id, 403, 'Not allowed');

        $agentId = (int)($tx->meta['agent_user_id'] ?? 0);
        abort_if($agentId <= 0, 422, 'Missing agent user');

        // Release user escrow -> agent cash
        $ledger->post('withdraw_release', [
            ['owner_type' => 'user', 'owner_id' => (int)$tx->user_id, 'account_type' => 'escrow', 'direction' => 'credit', 'amount' => (int)$tx->amount, 'currency' => $tx->currency, 'reference' => $tx->reference],
            ['owner_type' => 'user', 'owner_id' => $agentId,          'account_type' => 'cash',   'direction' => 'debit',  'amount' => (int)$tx->amount, 'currency' => $tx->currency, 'reference' => $tx->reference],
        ], $tx->currency, $tx->reference);

        $tx->status = 'success';
        $meta = $tx->meta ?? [];
        $meta['released_by'] = $user->id;
        $tx->meta = $meta;
        $tx->save();
        return response()->json(['success' => true]);
    }

    public function withdrawCancel(string $reference, Request $request, LedgerService $ledger)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $tx = WalletTransaction::where('reference', $reference)->where('type', 'withdraw')->firstOrFail();
        abort_if($tx->status !== 'pending', 422, 'Invalid state');
        abort_if((int)$tx->user_id !== (int)$user->id, 403, 'Only owner can cancel');

        // Return user escrow -> user cash
        $ledger->post('withdraw_cancel', [
            ['owner_type' => 'user', 'owner_id' => (int)$tx->user_id, 'account_type' => 'escrow', 'direction' => 'credit', 'amount' => (int)$tx->amount, 'currency' => $tx->currency, 'reference' => $tx->reference],
            ['owner_type' => 'user', 'owner_id' => (int)$tx->user_id, 'account_type' => 'cash',   'direction' => 'debit',  'amount' => (int)$tx->amount, 'currency' => $tx->currency, 'reference' => $tx->reference],
        ], $tx->currency, $tx->reference);

        $tx->status = 'cancelled';
        $tx->save();
        return response()->json(['success' => true]);
    }

    public function transfer(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'recipient_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:50',
            'note' => 'nullable|string|max:200',
        ]);
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $userId = $user->id;
        $amountKobo = (int) ($data['amount'] * 100);
        // Prevent self-transfer
        abort_if((int)$data['recipient_id'] === (int)$userId, 422, 'Cannot transfer to self');
        // Ensure recipient exists
        $recipient = User::find((int)$data['recipient_id']);
        abort_unless($recipient, 422, 'Recipient not found');
        // Ensure sufficient balance
        $cashBalanceKobo = (int) (DB::table('ledger_entries')
            ->join('ledger_accounts', 'ledger_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('ledger_accounts.owner_type', 'user')
            ->where('ledger_accounts.owner_id', $userId)
            ->where('ledger_accounts.account_type', 'cash')
            ->where('ledger_entries.currency', 'NGN')
            ->selectRaw("COALESCE(SUM(CASE WHEN ledger_entries.direction = 'debit' THEN ledger_entries.amount ELSE -ledger_entries.amount END), 0) as balance")
            ->value('balance') ?? 0);
        abort_if($cashBalanceKobo < $amountKobo, 422, 'Insufficient balance');

        // Perform double-entry: user cash -> recipient cash
        $ledger->post('transfer', [
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'cash', 'direction' => 'credit', 'amount' => $amountKobo, 'currency' => 'NGN'],
            ['owner_type' => 'user', 'owner_id' => (int) $data['recipient_id'], 'account_type' => 'cash', 'direction' => 'debit', 'amount' => $amountKobo, 'currency' => 'NGN'],
        ]);

        WalletTransaction::create([
            'user_id' => $userId,
            'type' => 'transfer',
            'status' => 'success',
            'amount' => $amountKobo,
            'currency' => 'NGN',
            'reference' => 'BLVK_' . uniqid(),
            'meta' => ['recipient_id' => (int) $data['recipient_id'], 'note' => $data['note'] ?? null],
        ]);

        return response()->json(['success' => true]);
    }
}
