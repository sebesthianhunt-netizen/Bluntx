<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;

class ProviderClient
{
    public function initPayment(string $provider, array $payload): array
    {
        $provider = strtolower($provider);
        if ($provider === 'paystack') {
            $secret = config('services.paystack.secret');
            $resp = Http::withToken($secret)
                ->post('https://api.paystack.co/transaction/initialize', $payload)
                ->throw()
                ->json();
            return [
                'reference' => $resp['data']['reference'] ?? $payload['reference'] ?? null,
                'checkout_url' => $resp['data']['authorization_url'] ?? null,
                'provider_response' => $resp,
            ];
        }
        if ($provider === 'flutterwave') {
            $secret = config('services.flutterwave.secret');
            $resp = Http::withToken($secret)
                ->post('https://api.flutterwave.com/v3/payments', $payload)
                ->throw()
                ->json();
            return [
                'reference' => $resp['data']['tx_ref'] ?? $payload['tx_ref'] ?? null,
                'checkout_url' => $resp['data']['link'] ?? null,
                'provider_response' => $resp,
            ];
        }
        if ($provider === 'monnify') {
            // Monnify uses basic auth for access token; simplified for brevity
            $secret = config('services.monnify.secret');
            $resp = Http::withToken($secret)
                ->post('https://api.monnify.com/api/v1/merchant/transactions/init-transaction', $payload)
                ->throw()
                ->json();
            return [
                'reference' => $resp['responseBody']['transactionReference'] ?? $payload['paymentReference'] ?? null,
                'checkout_url' => $resp['responseBody']['checkoutUrl'] ?? null,
                'provider_response' => $resp,
            ];
        }
        throw new \InvalidArgumentException('Unsupported provider');
    }

    public function initPayout(string $provider, array $payload): array
    {
        $provider = strtolower($provider);
        if ($provider === 'paystack') {
            $secret = config('services.paystack.secret');
            // Ensure recipient exists
            $recipientResp = Http::withToken($secret)
                ->post('https://api.paystack.co/transferrecipient', [
                    'type' => 'nuban',
                    'name' => $payload['name'] ?? 'BLVKDOT User',
                    'account_number' => $payload['account_number'] ?? '',
                    'bank_code' => $payload['bank_code'] ?? '',
                    'currency' => 'NGN',
                ])->throw()->json();
            $recipientCode = $recipientResp['data']['recipient_code'] ?? null;

            $transferResp = Http::withToken($secret)
                ->post('https://api.paystack.co/transfer', [
                    'source' => 'balance',
                    'amount' => (int) $payload['amount'], // kobo
                    'recipient' => $recipientCode,
                    'reason' => $payload['narration'] ?? 'Wallet withdrawal',
                    'reference' => $payload['reference'] ?? null,
                ])->throw()->json();

            return [
                'reference' => $transferResp['data']['reference'] ?? ($payload['reference'] ?? null),
                'provider_response' => [
                    'recipient' => $recipientResp,
                    'transfer' => $transferResp,
                ],
            ];
        }

        if ($provider === 'flutterwave') {
            $secret = config('services.flutterwave.secret');
            $transferResp = Http::withToken($secret)
                ->post('https://api.flutterwave.com/v3/transfers', [
                    'account_bank' => $payload['bank_code'] ?? $payload['account_bank'] ?? '',
                    'account_number' => $payload['account_number'] ?? '',
                    'amount' => (float) ($payload['amount'] / 100),
                    'narration' => $payload['narration'] ?? 'Wallet withdrawal',
                    'currency' => 'NGN',
                    'reference' => $payload['reference'] ?? null,
                    'debit_currency' => 'NGN',
                ])->throw()->json();

            return [
                'reference' => $transferResp['data']['reference'] ?? ($payload['reference'] ?? null),
                'provider_response' => $transferResp,
            ];
        }

        if ($provider === 'monnify') {
            $secret = config('services.monnify.secret');
            $transferResp = Http::withToken($secret)
                ->post('https://api.monnify.com/api/v2/disbursements/single', [
                    'reference' => $payload['reference'] ?? null,
                    'amount' => (float) ($payload['amount'] / 100),
                    'narration' => $payload['narration'] ?? 'Wallet withdrawal',
                    'destinationBankCode' => $payload['bank_code'] ?? '',
                    'destinationAccountNumber' => $payload['account_number'] ?? '',
                    'currency' => 'NGN',
                    'sourceAccountNumber' => $payload['source_account'] ?? '',
                    'contractCode' => env('MONNIFY_CONTRACT_CODE', ''),
                ])->throw()->json();

            return [
                'reference' => $transferResp['responseBody']['reference'] ?? ($payload['reference'] ?? null),
                'provider_response' => $transferResp,
            ];
        }

        throw new \InvalidArgumentException('Unsupported provider');
    }
}


