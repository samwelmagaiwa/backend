<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Helmet-like security headers for comprehensive protection
        
        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer policy for privacy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions policy (Feature Policy replacement)
        $response->headers->set('Permissions-Policy', 
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()'
        );
        
        // Content Security Policy - strict but functional for API
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline'; " .
               "style-src 'self' 'unsafe-inline'; " .
               "img-src 'self' data: https:; " .
               "font-src 'self'; " .
               "connect-src 'self'; " .
               "media-src 'none'; " .
               "object-src 'none'; " .
               "child-src 'none'; " .
               "frame-src 'none'; " .
               "worker-src 'none'; " .
               "frame-ancestors 'none'; " .
               "form-action 'self'; " .
               "base-uri 'self'; " .
               "manifest-src 'self'";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        // HSTS (HTTP Strict Transport Security) - only for HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Prevent information disclosure
        $response->headers->set('X-Powered-By', '');
        $response->headers->remove('Server');
        
        // Cache control for sensitive responses
        if ($request->is('api/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        }
        
        // Cross-Origin-Embedder-Policy and Cross-Origin-Opener-Policy
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        
        return $response;
    }
}
