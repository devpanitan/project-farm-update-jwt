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
        // This is the crucial part. If the request expects a JSON response (like our API calls),
        // this function will return null, which prevents the redirect and allows
        // an authentication exception to be thrown. That exception is then rendered
        // as a 401 JSON response by the framework.
        return $request->expectsJson() ? null : route('login');
    }
}
