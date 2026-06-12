<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Trust reverse proxy / load balancer (must run first) ─────────────
        // Ensures Request::ip() returns the real client IP for audit logs and
        // rate limiting, not the proxy's internal IP.
        $middleware->prepend(\App\Http\Middleware\TrustProxies::class);

        // ── Security response headers on every web response ───────────────────
        // Adds CSP, X-Frame-Options, HSTS, etc. to all web responses.
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // ── Register custom middleware aliases ─────────────────────────────────
        $middleware->alias([
            'check.active'       => \App\Http\Middleware\CheckActiveUser::class,
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ── Log every 419 TokenMismatch with full context ─────────────────────
        // This makes CSRF/session issues diagnosable in Render's log stream
        // without impacting users (returns null = let Laravel render default 419).
        $exceptions->render(function (
            TokenMismatchException $e,
            \Illuminate\Http\Request $request
        ) {
            \Illuminate\Support\Facades\Log::warning('419 CSRF TokenMismatch', [
                'url'         => $request->fullUrl(),
                'method'      => $request->method(),
                'ip'          => $request->ip(),
                'user_id'     => optional(auth()->user())->id,
                'user_agent'  => $request->userAgent(),
                'referer'     => $request->header('Referer'),
                'session_id'  => $request->hasSession()
                                   ? $request->session()->getId()
                                   : null,
                'has_token'   => $request->hasSession()
                                   ? (bool) $request->session()->token()
                                   : false,
                'session_drv' => config('session.driver'),
            ]);

            return null; // Let Laravel render the default 419 page
        });
    })->create();
