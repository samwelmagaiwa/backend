<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoCache
{
    /**
     * Add server-side no-cache headers to responses for targeted endpoints.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only apply to successful responses; keep error behavior unchanged
        $status = (int) $response->getStatusCode();
        if ($status >= 200 && $status < 400) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
