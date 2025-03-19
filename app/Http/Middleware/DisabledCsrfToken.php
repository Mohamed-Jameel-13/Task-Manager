<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisabledCsrfToken
{
    /**
     * Handle an incoming request without CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
