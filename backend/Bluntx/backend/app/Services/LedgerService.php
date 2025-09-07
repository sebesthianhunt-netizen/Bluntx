<?php

namespace App\Services;

use App\Models\LedgerAccount;
use App\Models\LedgerEntry;
use App\Models\LedgerTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use App\Services\SlackNotifier;

class LedgerService
{
    /**
     * Create a balanced ledger transaction with entries.
     * $entries: [ [owner_type, owner_id, account_type, direction, amount, currency, reference, meta], ... ]
     */
    public function post(string $type, array $entries, string $currency = 'NGN', ?string $reference = null): LedgerTransaction
    {
        $sum = 0;
        foreach ($entries as $e) {
            $sum += ($e['direction'] === 'debit' ? 1 : -1) * (int) $e['amount'];
        }
        if ($sum !== 0) {
            $context = ['type' => $type, 'reference' => $reference, 'entries' => $entries, 'currency' => $currency, 'sum' => $sum];
            Log::error('Ledger imbalance detected', $context);
            try { app(SlackNotifier::class)->alert('Ledger imbalance detected', $context); } catch (\Throwable $e) {}
            throw new InvalidArgumentException('Ledger entries not balanced');
        }

        return DB::transaction(function () use ($type, $entries, $currency, $reference) {
            $tx = LedgerTransaction::create([
                'type' => $type,
                'status' => 'success',
                'total_amount' => array_sum(array_column($entries, 'amount')),
                'currency' => $currency,
                'reference' => $reference,
            ]);

            foreach ($entries as $e) {
                $account = LedgerAccount::firstOrCreate([
                    'owner_type' => $e['owner_type'],
                    'owner_id' => $e['owner_id'],
                    'account_type' => $e['account_type'],
                ]);
                LedgerEntry::create([
                    'ledger_transaction_id' => $tx->id,
                    'ledger_account_id' => $account->id,
                    'direction' => $e['direction'],
                    'amount' => (int) $e['amount'],
                    'currency' => $e['currency'] ?? $currency,
                    'reference' => $e['reference'] ?? null,
                    'meta' => $e['meta'] ?? null,
                ]);
            }

            return $tx;
        });
    }
}
