<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests (or JSON-expecting requests), return 401 JSON instead of redirecting
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        return null;
    }
}


