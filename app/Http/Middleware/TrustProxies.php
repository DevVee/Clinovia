<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Trust the upstream reverse proxy / load balancer so that:
 *   - Request::ip()      returns the real client IP (for audit logs)
 *   - Request::secure()  returns true when the proxy terminates HTTPS
 *   - Rate limiters      throttle per real client, not per proxy
 *
 * Set TRUST_PROXIES=* in .env to trust all proxies (Cloudflare, shared hosting).
 * For a known proxy IP (e.g. 10.0.0.1), set TRUST_PROXIES=10.0.0.1 instead.
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR    |
        Request::HEADER_X_FORWARDED_HOST   |
        Request::HEADER_X_FORWARDED_PORT   |
        Request::HEADER_X_FORWARDED_PROTO  |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    public function __construct()
    {
        // Read from .env: '*' = trust all, or comma-separated IPs
        $value = env('TRUST_PROXIES', '*');
        $this->proxies = $value === '*' ? '*' : array_map('trim', explode(',', $value));
    }
}
