<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     */
    protected $except = [
        'api/*',      // Excludes REST API endpoints
        'login',      // Excludes web login endpoint for simulator compatibility
        'register',   // Excludes web registration endpoint for testing
    ];
}
