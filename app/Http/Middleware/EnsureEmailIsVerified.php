<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\EnsureEmailIsVerified as Middleware;

class EnsureEmailIsVerified extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, \Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ! $request->user()->hasVerifiedEmail()) {
            return $request->expectsJson()
                    ? response()->json(['message' => 'Your email address is not verified.'], 409)
                    : redirect()->route($redirectToRoute ?? 'verification.notice');
        }

        return $next($request);
    }
}
