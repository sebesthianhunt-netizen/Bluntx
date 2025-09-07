<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotifier
{
    public function alert(string $message, array $context = []): void
    {
        try {
            $token = config('services.slack.notifications.bot_user_oauth_token');
            $channel = config('services.slack.notifications.channel');
            if (!$token || !$channel) {
                Log::warning('SlackNotifier not configured', ['message' => $message, 'context' => $context]);
                return;
            }
            $text = $message . (empty($context) ? '' : "\n```" . json_encode($context, JSON_PRETTY_PRINT) . "```\n");
            Http::withToken($token)
                ->post('https://slack.com/api/chat.postMessage', [
                    'channel' => $channel,
                    'text' => $text,
                ])->throw();
        } catch (\Throwable $e) {
            Log::error('Failed to send Slack alert', ['error' => $e->getMessage(), 'message' => $message, 'context' => $context]);
        }
    }
}


