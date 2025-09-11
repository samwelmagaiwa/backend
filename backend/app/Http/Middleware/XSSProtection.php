<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class XSSProtection
{
    /**
     * List of suspicious patterns that might indicate XSS attempts
     *
     * @var array
     */
    protected $suspiciousPatterns = [
        '/<script[^>]*>.*?<\/script>/is',
        '/<iframe[^>]*>.*?<\/iframe>/is', 
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/data\s*:\s*text\/html/i',
        '/on\w+\s*=\s*["\'][^"\']*["\']/i',
        '/<embed[^>]*>/i',
        '/<object[^>]*>/i',
        '/<applet[^>]*>/i',
        '/<meta[^>]*>/i',
        '/<link[^>]*>/i',
        '/expression\s*\(/i',
        '/url\s*\(/i',
        '/import\s/i',
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for XSS attempts in all input
        $this->detectXSSAttempts($request);
        
        // Process the request
        $response = $next($request);
        
        // Add XSS protection headers
        $this->addXSSHeaders($response);
        
        // Validate response content for potential XSS
        if ($this->shouldValidateResponse($request)) {
            $this->validateResponseContent($response);
        }
        
        return $response;
    }
    
    /**
     * Detect potential XSS attempts in request data
     *
     * @param Request $request
     * @return void
     */
    private function detectXSSAttempts(Request $request): void
    {
        $allInput = $request->all();
        $userAgent = $request->header('User-Agent', '');
        $referrer = $request->header('Referer', '');
        
        // Check all input fields
        $this->scanForXSS($allInput, 'request_input');
        
        // Check headers
        $this->scanForXSS(['user_agent' => $userAgent, 'referrer' => $referrer], 'request_headers');
        
        // Check URL parameters
        $this->scanForXSS($request->query->all(), 'url_parameters');
        
        // Check JSON payload if present
        if ($request->isJson() && $request->getContent()) {
            $jsonData = json_decode($request->getContent(), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                $this->scanForXSS($jsonData, 'json_payload');
            }
        }
    }
    
    /**
     * Recursively scan data for XSS patterns
     *
     * @param mixed $data
     * @param string $context
     * @return void
     */
    private function scanForXSS($data, string $context): void
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->scanForXSS($value, $context . '.' . $key);
            }
        } elseif (is_string($data)) {
            $this->checkStringForXSS($data, $context);
        }
    }
    
    /**
     * Check string for XSS patterns
     *
     * @param string $input
     * @param string $context
     * @return void
     */
    private function checkStringForXSS(string $input, string $context): void
    {
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $this->logXSSAttempt($input, $pattern, $context);
                // Optionally block the request entirely for high-risk patterns
                if ($this->isHighRiskPattern($pattern)) {
                    abort(422, 'Potentially malicious content detected');
                }
            }
        }
    }
    
    /**
     * Check if pattern is high risk
     *
     * @param string $pattern
     * @return bool
     */
    private function isHighRiskPattern(string $pattern): bool
    {
        $highRiskPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/data\s*:\s*text\/html/i',
        ];
        
        return in_array($pattern, $highRiskPatterns);
    }
    
    /**
     * Log XSS attempt
     *
     * @param string $input
     * @param string $pattern
     * @param string $context
     * @return void
     */
    private function logXSSAttempt(string $input, string $pattern, string $context): void
    {
        Log::warning('Potential XSS attempt detected', [
            'input' => substr($input, 0, 200), // Log only first 200 chars
            'pattern' => $pattern,
            'context' => $context,
            'ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
        ]);
    }
    
    /**
     * Add XSS protection headers
     *
     * @param Response $response
     * @return void
     */
    private function addXSSHeaders(Response $response): void
    {
        // These are additional to the SecurityHeaders middleware
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Add specific CSP for XSS protection if not already set
        if (!$response->headers->has('Content-Security-Policy')) {
            $csp = "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;";
            $response->headers->set('Content-Security-Policy', $csp);
        }
    }
    
    /**
     * Check if response content should be validated
     *
     * @param Request $request
     * @return bool
     */
    private function shouldValidateResponse(Request $request): bool
    {
        // Only validate HTML responses
        $acceptHeader = $request->header('Accept', '');
        return strpos($acceptHeader, 'text/html') !== false;
    }
    
    /**
     * Validate response content for XSS vulnerabilities
     *
     * @param Response $response
     * @return void
     */
    private function validateResponseContent(Response $response): void
    {
        $content = $response->getContent();
        
        if (!empty($content) && is_string($content)) {
            // Check for unescaped user data in HTML output
            $this->scanResponseForUnescapedData($content);
        }
    }
    
    /**
     * Scan response content for potential unescaped data
     *
     * @param string $content
     * @return void
     */
    private function scanResponseForUnescapedData(string $content): void
    {
        // Look for potentially dangerous patterns in output
        $dangerousPatterns = [
            '/<script[^>]*>[^<]*\{\{.*?\}\}[^<]*<\/script>/i', // Laravel blade syntax in script tags
            '/on\w+\s*=\s*["\'][^"\']*\{\{.*?\}\}[^"\']*["\']/i', // Event handlers with blade syntax
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::warning('Potential XSS vulnerability in response output', [
                    'pattern' => $pattern,
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'timestamp' => now()->toISOString(),
                ]);
            }
        }
    }
}
