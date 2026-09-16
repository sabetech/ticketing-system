<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * Single authoritative CORS layer (Bearer-only, no cookies).
     * Allowlist is env-driven via CORS_ALLOWED_ORIGINS (comma-separated).
     * Echoes back the matched Origin instead of '*' so browsers accept
     * the preflight, and adds Vary: Origin for correct caching.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowedOrigins = array_filter(array_map('trim', explode(',', env(
            'CORS_ALLOWED_ORIGINS',
            'http://localhost:5173,http://localhost:3000'
        ))));

        $origin = $request->headers->get('Origin');

        if ($origin && in_array($origin, $allowedOrigins)) {
            $allowOrigin = $origin;
        } elseif (in_array('*', $allowedOrigins)) {
            $allowOrigin = '*';
        } elseif ($origin) {
            // Origin not allowlisted: still let the request through so
            // Laravel returns its normal status, but without ACAO headers
            // the browser will correctly block it.
            $allowOrigin = null;
        } else {
            // Non-browser request (curl, health check): nothing to echo.
            $allowOrigin = $allowedOrigins ? $allowedOrigins[0] : '*';
        }

        $headers = [
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-Auth-Token, X-CSRF-TOKEN',
            'Access-Control-Max-Age' => '86400',
            'Vary' => 'Origin',
        ];

        if ($allowOrigin) {
            $headers['Access-Control-Allow-Origin'] = $allowOrigin;
        }

        if ($request->isMethod('OPTIONS')) {
            return response()->make('', 204, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}
