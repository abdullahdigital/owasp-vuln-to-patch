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
        // For API: if the request does not expect JSON, just return null.
        // This makes Laravel respond with 401 Unauthorized instead of trying to redirect.
        return $request->expectsJson() ? null : null;
    }
}
