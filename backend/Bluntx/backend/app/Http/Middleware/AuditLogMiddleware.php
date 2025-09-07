<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $user = $request->user('api') ?? $request->user();
            $body = $request->getContent();
            $hash = hash('sha256', $body);
            DB::table('audit_logs')->insert([
                'user_id' => $user?->id,
                'guard' => $user ? ($request->user('api') ? 'api' : 'web') : null,
                'method' => $request->method(),
                'route' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $response->getStatusCode(),
                'request_hash' => $hash,
                'meta' => json_encode([
                    'headers' => [
                        'content-type' => $request->header('content-type'),
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // swallow to never block requests
        }

        return $response;
    }
}


