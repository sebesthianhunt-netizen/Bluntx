<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SlackNotifier;
use App\Models\WebhookEvent;

class WebhookController extends Controller
{
    public function handle(string $provider, Request $request, LedgerService $ledger, SlackNotifier $slack)
    {
        $provider = strtolower($provider);

        if (!in_array($provider, ['paystack','flutterwave','monnify'])) {
            return response()->json(['message' => 'Unsupported provider'], Response::HTTP_BAD_REQUEST);
        }

        // Verify signature
        if (!$this->verifySignature($provider, $request)) {
            WebhookEvent::create([
                'provider' => $provider,
                'reference' => null,
                'event' => 'invalid_signature',
                'signature' => $request->header('x-paystack-signature')
                    ?? $request->header('verif-hash')
                    ?? $request->header('monnify-signature'),
                'status' => 'rejected',
                'headers' => $request->headers->all(),
                'payload' => json_decode($raw = $request->getContent(), true),
            ]);
            $slack->alert('Invalid webhook signature', ['provider' => $provider, 'headers' => $request->headers->all()]);
            return response()->json(['message' => 'Invalid signature'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $request->all();
        [$reference, $isSuccessful] = $this->extractReferenceAndStatus($provider, $payload);

        if (!$reference) {
            return response()->json(['message' => 'Missing reference'], Response::HTTP_BAD_REQUEST);
        }

        $tx = WalletTransaction::where('reference', $reference)->first();
        $event = WebhookEvent::create([
            'provider' => $provider,
            'reference' => $reference,
            'event' => $payload['event'] ?? ($payload['eventType'] ?? 'unknown'),
            'signature' => $request->header('x-paystack-signature')
                ?? $request->header('verif-hash')
                ?? $request->header('monnify-signature'),
            'status' => $isSuccessful ? 'success' : 'failed',
            'headers' => $request->headers->all(),
            'payload' => $payload,
        ]);
        // Increment retry count for duplicates
        if ($tx && $tx->status !== 'pending') {
            $event->retry_count = ($event->retry_count ?? 0) + 1;
            $event->save();
            if ($event->retry_count >= 3) {
                $slack->alert('High webhook retry count', ['provider' => $provider, 'reference' => $reference, 'retries' => $event->retry_count]);
            }
        }

        if (!$tx) {
            // Acknowledge to avoid endless retries, but log silently
            return response()->json(['message' => 'Transaction not found']);
        }

        if ($tx->status !== 'pending') {
            Log::info('Webhook duplicate or late event', ['reference' => $reference, 'status' => $tx->status]);
            return response()->json(['message' => 'Already processed']);
        }

        return DB::transaction(function () use ($tx, $isSuccessful, $provider, $ledger, $payload, $slack) {
            if ($isSuccessful) {
                if ($tx->type === 'fund') {
                    // Credit user's cash (debit) and credit provider clearing (credit) for the same amount
                    $ledger->post('fund', [
                        [
                            'owner_type' => 'user',
                            'owner_id' => $tx->user_id,
                            'account_type' => 'cash',
                            'direction' => 'debit',
                            'amount' => (int) $tx->amount,
                            'currency' => $tx->currency,
                            'reference' => $tx->reference,
                            'meta' => ['provider' => $provider],
                        ],
                        [
                            'owner_type' => 'provider',
                            'owner_id' => 0,
                            'account_type' => 'cash',
                            'direction' => 'credit',
                            'amount' => (int) $tx->amount,
                            'currency' => $tx->currency,
                            'reference' => $tx->reference,
                            'meta' => ['provider' => $provider],
                        ],
                    ], $tx->currency, $tx->reference);
                }
                if ($tx->type === 'withdraw') {
                    // On-site escrow release to provider on webhook success:
                    // Move user escrow -> provider cash
                    $ledger->post('withdraw_webhook_release', [
                        [
                            'owner_type' => 'user',
                            'owner_id' => $tx->user_id,
                            'account_type' => 'escrow',
                            'direction' => 'credit',
                            'amount' => (int) $tx->amount,
                            'currency' => $tx->currency,
                            'reference' => $tx->reference,
                            'meta' => ['provider' => $provider],
                        ],
                        [
                            'owner_type' => 'provider',
                            'owner_id' => 0,
                            'account_type' => 'cash',
                            'direction' => 'debit',
                            'amount' => (int) $tx->amount,
                            'currency' => $tx->currency,
                            'reference' => $tx->reference,
                            'meta' => ['provider' => $provider],
                        ],
                    ], $tx->currency, $tx->reference);
                }
                // For reversals/refunds: If event indicates refund/chargeback, reverse prior ledger entries
                $this->maybeHandleRefund($provider, $payload, $ledger, $tx);

                $tx->status = 'success';
            } else {
                // Failure: if this was a withdraw hold, return escrow back to user cash
                if ($tx->type === 'withdraw') {
                    $ledger->post('withdraw_webhook_fail_revert', [
                        [
                            'owner_type' => 'user',
                            'owner_id' => $tx->user_id,
                            'account_type' => 'escrow',
                            'direction' => 'credit',
                            'amount' => (int) $tx->amount,
                            'currency' => $tx->currency,
                            'reference' => $tx->reference,
                            'meta' => ['provider' => $provider, 'failed' => true],
                        ],
                        [
                            'owner_type' => 'user',
                            'owner_id' => $tx->user_id,
                            'account_type' => 'cash',
                            'direction' => 'debit',
                            'amount' => (int) $tx->amount,
                            'currency' => $tx->currency,
                            'reference' => $tx->reference,
                            'meta' => ['provider' => $provider, 'failed' => true],
                        ],
                    ], $tx->currency, $tx->reference);
                }

                $tx->status = 'failed';
                $slack->alert('Webhook failed transaction', ['provider' => $provider, 'reference' => $tx->reference, 'payload' => $payload]);
            }

            $meta = $tx->meta ?? [];
            $meta['webhook'] = ['provider' => $provider, 'payload' => $payload];
            $tx->meta = $meta;
            $tx->save();

            return response()->json(['message' => 'ok']);
        });
    }

    private function verifySignature(string $provider, Request $request): bool
    {
        $raw = $request->getContent();

        if ($provider === 'paystack') {
            $secret = config('services.paystack.webhook_secret') ?? config('services.paystack.secret');
            $signature = $request->header('x-paystack-signature');
            if (!$secret || !$signature) return false;
            $computed = hash_hmac('sha512', $raw, $secret);
            return hash_equals($computed, $signature);
        }

        if ($provider === 'flutterwave') {
            $secret = config('services.flutterwave.webhook_secret') ?? config('services.flutterwave.secret');
            $signature = $request->header('verif-hash');
            if (!$secret || !$signature) return false;
            return hash_equals($secret, $signature);
        }

        if ($provider === 'monnify') {
            $secret = config('services.monnify.webhook_secret') ?? config('services.monnify.secret');
            $signature = $request->header('monnify-signature');
            if (!$secret || !$signature) return false;
            // Monnify recommends HMAC SHA512 of raw body with secret, base64-encoded
            $computed = base64_encode(hash_hmac('sha512', $raw, $secret, true));
            return hash_equals($computed, $signature);
        }

        return false;
    }

    private function extractReferenceAndStatus(string $provider, array $payload): array
    {
        $reference = null;
        $success = false;

        if ($provider === 'paystack') {
            $event = $payload['event'] ?? null;
            $data = $payload['data'] ?? [];
            $reference = $data['reference'] ?? null;
            $status = strtolower($data['status'] ?? '');
            $success = ($event === 'charge.success') || in_array($status, ['success','successful']);
        } elseif ($provider === 'flutterwave') {
            $data = $payload['data'] ?? [];
            $reference = $data['tx_ref'] ?? $data['reference'] ?? null;
            $status = strtolower($data['status'] ?? '');
            $success = in_array($status, ['success','successful','completed']);
        } elseif ($provider === 'monnify') {
            $data = $payload['eventData'] ?? [];
            $reference = $data['transactionReference'] ?? $data['paymentReference'] ?? null;
            $status = strtoupper($data['paymentStatus'] ?? '');
            $success = in_array($status, ['PAID','SUCCESSFUL']);
        }

        return [$reference, $success];
    }

    private function maybeHandleRefund(string $provider, array $payload, LedgerService $ledger, WalletTransaction $tx): void
    {
        $isRefund = false;
        if ($provider === 'paystack') {
            $event = $payload['event'] ?? '';
            $isRefund = str_contains($event, 'refund');
        } elseif ($provider === 'flutterwave') {
            $event = strtolower($payload['event'] ?? '');
            $isRefund = str_contains($event, 'refund');
        } elseif ($provider === 'monnify') {
            $event = strtoupper($payload['eventType'] ?? '');
            $isRefund = str_contains($event, 'REFUND');
        }

        if (!$isRefund) return;

        // Reverse a fund transaction by moving cash -> provider
        if ($tx->type === 'fund') {
            $ledger->post('refund', [
                ['owner_type' => 'user', 'owner_id' => $tx->user_id, 'account_type' => 'cash', 'direction' => 'credit', 'amount' => (int) $tx->amount, 'currency' => $tx->currency, 'reference' => $tx->reference, 'meta' => ['provider' => $provider, 'refund' => true]],
                ['owner_type' => 'provider', 'owner_id' => 0, 'account_type' => 'cash', 'direction' => 'debit', 'amount' => (int) $tx->amount, 'currency' => $tx->currency, 'reference' => $tx->reference, 'meta' => ['provider' => $provider, 'refund' => true]],
            ], $tx->currency, $tx->reference . ':refund');
        }
    }
}


