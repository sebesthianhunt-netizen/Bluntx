<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\User;
use App\Services\LedgerService;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function create(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'opponent_id' => 'required|integer',
            'stake_amount' => 'required|numeric|min:100',
            'insurance_amount' => 'nullable|numeric|min:0',
            'venue_id' => 'nullable|integer',
        ]);
        $user = $request->user('api') ?? $request->user();
        $userId = $user?->id ?? 1;
        $c = Challenge::create([
            'challenger_id' => $userId,
            'opponent_id' => $data['opponent_id'],
            'venue_id' => $data['venue_id'] ?? null,
            'stake_amount' => (int) ($data['stake_amount'] * 100),
            'insurance_amount' => isset($data['insurance_amount']) ? (int) ($data['insurance_amount'] * 100) : 0,
            'total_escrow' => 0,
            'status' => 'pending',
        ]);
        // Lock challenger stake into escrow
        $amount = (int) ($data['stake_amount'] * 100);
        $ledger->post('escrow_lock', [
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'cash', 'direction' => 'credit', 'amount' => $amount, 'currency' => 'NGN', 'reference' => 'challenge:' . $c->id],
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'escrow', 'direction' => 'debit', 'amount' => $amount, 'currency' => 'NGN', 'reference' => 'challenge:' . $c->id],
        ], 'NGN', 'challenge:' . $c->id);
        return response()->json(['id' => $c->id, 'status' => $c->status]);
    }

    public function accept($id, Request $request, LedgerService $ledger)
    {
        $c = Challenge::findOrFail($id);
        $user = $request->user('api') ?? $request->user();
        $userId = $user?->id ?? 1;
        // Lock opponent stake into escrow
        $amount = (int) $c->stake_amount;
        $ledger->post('escrow_lock', [
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'cash', 'direction' => 'credit', 'amount' => $amount, 'currency' => 'NGN', 'reference' => 'challenge:' . $c->id],
            ['owner_type' => 'user', 'owner_id' => $userId, 'account_type' => 'escrow', 'direction' => 'debit', 'amount' => $amount, 'currency' => 'NGN', 'reference' => 'challenge:' . $c->id],
        ], 'NGN', 'challenge:' . $c->id);
        $c->total_escrow = $c->total_escrow + ($amount * 2 > $c->total_escrow ? $amount : 0);
        $c->status = 'accepted';
        $c->save();
        return response()->json(['success' => true]);
    }

    public function decline($id)
    {
        $c = Challenge::findOrFail($id);
        $c->status = 'declined';
        $c->save();
        return response()->json(['success' => true]);
    }

    public function result($id, Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'winner_id' => 'required|integer',
        ]);
        $c = Challenge::findOrFail($id);
        abort_if($c->status !== 'accepted', 422, 'Challenge not accepted');
        $winnerId = (int) $data['winner_id'];
        $loserId = $winnerId === (int)$c->challenger_id ? (int)$c->opponent_id : (int)$c->challenger_id;

        $stake = (int) $c->stake_amount; // per side kobo
        $houseCutEach = (int) round($stake * 0.10);
        $winnerTakeEach = $stake - $houseCutEach; // per side
        $totalToWinner = $winnerTakeEach * 2; // both sides after 10% each side

        // Move escrow to winner and house
        $ledger->post('challenge_payout', [
            // Winner receives both sides net (escrow -> winner cash)
            ['owner_type' => 'user', 'owner_id' => $c->challenger_id, 'account_type' => 'escrow', 'direction' => 'credit', 'amount' => $stake, 'currency' => 'NGN', 'reference' => 'challenge:'.$c->id],
            ['owner_type' => 'user', 'owner_id' => $c->opponent_id,   'account_type' => 'escrow', 'direction' => 'credit', 'amount' => $stake, 'currency' => 'NGN', 'reference' => 'challenge:'.$c->id],
            ['owner_type' => 'user', 'owner_id' => $winnerId,          'account_type' => 'cash',   'direction' => 'debit',  'amount' => $totalToWinner, 'currency' => 'NGN', 'reference' => 'challenge:'.$c->id],
            // House cut from each side to platform cash
            ['owner_type' => 'platform', 'owner_id' => 0, 'account_type' => 'cash', 'direction' => 'debit', 'amount' => $houseCutEach * 2, 'currency' => 'NGN', 'reference' => 'challenge:'.$c->id],
        ], 'NGN', 'challenge:'.$c->id);

        // Update ELO
        $kFactor = 32;
        $winner = User::find($winnerId);
        $loser = User::find($loserId);
        $rA = pow(10, ($winner->elo ?? 1200) / 400);
        $rB = pow(10, ($loser->elo ?? 1200) / 400);
        $eA = $rA / ($rA + $rB);
        $eB = $rB / ($rA + $rB);
        $winner->elo = (int) round(($winner->elo ?? 1200) + $kFactor * (1 - $eA));
        $loser->elo = (int) round(($loser->elo ?? 1200) + $kFactor * (0 - $eB));
        $winner->save();
        $loser->save();

        $c->status = 'completed';
        $c->save();
        return response()->json(['success' => true]);
    }

    public function history()
    {
        $items = Challenge::orderByDesc('id')->limit(20)->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'stake_amount' => (int) ($c->stake_amount / 100),
                'status' => $c->status,
            ];
        });
        return response()->json(['data' => $items]);
    }
}
