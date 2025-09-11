<?php

namespace App\Http\Controllers;

use App\Support\Security\Health as SecurityHealth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SecurityTestController extends Controller
{
    /**
     * Test endpoint for security features
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Security test endpoint',
            'timestamp' => now()->toISOString(),
            'request_data' => $request->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'security_features' => [
                'headers_applied' => 'SecurityHeaders middleware',
                'input_sanitized' => 'InputSanitization middleware', 
                'xss_protected' => 'XSSProtection middleware',
                'rate_limited' => 'throttle:api (60/min)',
                'cors_enabled' => 'HandleCors middleware'
            ]
        ]);
    }

    /**
     * Test input sanitization
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sanitizationTest(Request $request): JsonResponse
    {
        $originalData = json_encode($request->all());
        
        return response()->json([
            'message' => 'Input sanitization test',
            'note' => 'Compare input vs processed data',
            'processed_data' => $request->all(),
            'security_info' => 'HTML/JS/Script tags have been removed by InputSanitization middleware'
        ]);
    }

    /**
     * Test XSS protection 
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function xssTest(Request $request): JsonResponse
    {
        // This will trigger XSS detection if malicious content is sent
        return response()->json([
            'message' => 'XSS protection test',
            'processed_input' => $request->all(),
            'security_info' => 'XSS patterns are logged and high-risk ones are blocked'
        ]);
    }

    /**
     * Test rate limiting
     *
     * @param Request $request
     * @return JsonResponse  
     */
    public function rateLimitTest(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Rate limit test endpoint',
            'current_time' => now()->toISOString(),
            'rate_limit_info' => 'This endpoint is limited to 60 requests per minute per IP',
            'request_count' => 'Check X-RateLimit-* headers in response'
        ]);
    }

    /**
     * Security health check endpoint
     *
     * @return JsonResponse
     */
    public function healthCheck(): JsonResponse
    {
        $checks = SecurityHealth::checks();
        $allOk = collect($checks)->every(fn ($r) => $r['ok'] === true);

        return response()->json([
            'message' => 'Security health check',
            'overall_status' => $allOk ? 'OK' : 'FAIL',
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
            'summary' => [
                'total_checks' => count($checks),
                'passed' => collect($checks)->where('ok', true)->count(),
                'failed' => collect($checks)->where('ok', false)->count(),
            ]
        ], $allOk ? 200 : 500);
    }
}
