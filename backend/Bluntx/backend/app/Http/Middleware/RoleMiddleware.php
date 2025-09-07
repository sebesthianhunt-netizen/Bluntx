<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user('api') ?? $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $userRole = $user->role ?? 'User';
        if (empty($roles) || in_array($userRole, $roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
    }
}


