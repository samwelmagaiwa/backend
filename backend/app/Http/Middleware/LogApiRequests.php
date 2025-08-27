<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $requestId = uniqid('req_', true);
        
        // Add request ID to request for tracking
        $request->attributes->set('request_id', $requestId);
        
        // Log incoming request
        Log::info('API Request Started', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
            'headers' => $this->getRelevantHeaders($request)
        ]);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        // Log response
        Log::info('API Request Completed', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);

        // Add request ID to response headers for debugging
        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('X-Response-Time', $duration . 'ms');

        return $response;
    }

    /**
     * Get relevant headers for logging (excluding sensitive data)
     */
    private function getRelevantHeaders(Request $request): array
    {
        $headers = $request->headers->all();
        
        // Remove sensitive headers
        unset($headers['authorization']);
        unset($headers['cookie']);
        unset($headers['x-csrf-token']);
        
        // Keep only relevant headers
        return array_intersect_key($headers, array_flip([
            'accept',
            'content-type',
            'x-requested-with',
            'origin',
            'referer',
            'user-agent'
        ]));
    }
}