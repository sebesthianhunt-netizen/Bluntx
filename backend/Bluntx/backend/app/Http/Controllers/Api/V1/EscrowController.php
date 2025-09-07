<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Escrow;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EscrowController extends Controller
{
    public function initiate(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'receiver_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:50',
            'asset' => 'sometimes|string|in:cash,points',
            'note' => 'sometimes|string|max:200',
        ]);
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $userId = $user->id;
        $receiverId = (int) $data['receiver_id'];
        abort_if($receiverId === (int)$userId, 422, 'Cannot escrow to self');

        $amountKobo = (int) ($data['amount'] * 100);

        // Ensure sufficient cash balance
        $cashBalanceKobo = (int) (\DB::table('ledger_entries')
            ->join('ledger_accounts', 'ledger_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('ledger_accounts.owner_type', 'user')
            ->where('ledger_accounts.owner_id', $userId)
            ->where('ledger_accounts.account_type', 'cash')
            ->where('ledger_entries.currency', 'NGN')
            ->selectRaw("COALESCE(SUM(CASE WHEN ledger_entries.direction = 'debit' THEN ledger_entries.amount ELSE -ledger_entries.amount END), 0) as balance")
            ->value('balance') ?? 0);
        abort_if($cashBalanceKobo < $amountKobo, 422, 'Insufficient balance');

        $reference = 'ESCR_' . Str::uuid();

        // Move from initiator cash -> initiator escrow
        $ledger->post('escrow_initiate', [
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'cash',   'direction' => 'credit', 'amount' => $amountKobo, 'currency' => 'NGN', 'reference' => $reference],
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'escrow', 'direction' => 'debit',  'amount' => $amountKobo, 'currency' => 'NGN', 'reference' => $reference],
        ], 'NGN', $reference);

        $escrow = Escrow::create([
            'initiator_id' => $userId,
            'receiver_id' => $receiverId,
            'amount' => $amountKobo,
            'currency' => 'NGN',
            'asset' => $data['asset'] ?? 'cash',
            'status' => 'pending',
            'reference' => $reference,
            'note' => $data['note'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $escrow->id, 'reference' => $reference]]);
    }

    public function confirm($id, Request $request, LedgerService $ledger)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $escrow = Escrow::findOrFail($id);
        abort_if($escrow->status !== 'pending', 422, 'Invalid state');
        abort_if((int)$escrow->receiver_id !== (int)$user->id, 403, 'Only receiver can confirm');

        // Release initiator escrow -> receiver cash
        $ledger->post('escrow_release', [
            ['owner_type' => 'user', 'owner_id' => (int)$escrow->initiator_id, 'account_type' => 'escrow', 'direction' => 'credit', 'amount' => (int)$escrow->amount, 'currency' => $escrow->currency, 'reference' => $escrow->reference],
            ['owner_type' => 'user', 'owner_id' => (int)$escrow->receiver_id,  'account_type' => 'cash',   'direction' => 'debit',  'amount' => (int)$escrow->amount, 'currency' => $escrow->currency, 'reference' => $escrow->reference],
        ], $escrow->currency, $escrow->reference);

        $escrow->status = 'released';
        $escrow->save();
        return response()->json(['success' => true]);
    }

    public function cancel($id, Request $request, LedgerService $ledger)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $escrow = Escrow::findOrFail($id);
        abort_if($escrow->status !== 'pending', 422, 'Invalid state');
        abort_unless(in_array((int)$user->id, [(int)$escrow->initiator_id, (int)$escrow->receiver_id], true), 403, 'Not allowed');

        // Return to initiator cash
        $ledger->post('escrow_cancel', [
            ['owner_type' => 'user', 'owner_id' => (int)$escrow->initiator_id, 'account_type' => 'escrow', 'direction' => 'credit', 'amount' => (int)$escrow->amount, 'currency' => $escrow->currency, 'reference' => $escrow->reference],
            ['owner_type' => 'user', 'owner_id' => (int)$escrow->initiator_id, 'account_type' => 'cash',   'direction' => 'debit',  'amount' => (int)$escrow->amount, 'currency' => $escrow->currency, 'reference' => $escrow->reference],
        ], $escrow->currency, $escrow->reference);

        $escrow->status = 'cancelled';
        $escrow->save();
        return response()->json(['success' => true]);
    }
}


