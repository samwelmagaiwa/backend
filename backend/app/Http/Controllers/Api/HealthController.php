<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    /**
     * Health check endpoint for testing CORS and API connectivity.
     */
    public function check(Request $request): JsonResponse
    {
        $origin = $request->headers->get('Origin');
        $userAgent = $request->headers->get('User-Agent');
        
        return response()->json([
            'status' => 'ok',
            'message' => 'API is working correctly',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'debug_info' => [
                'origin' => $origin,
                'method' => $request->getMethod(),
                'path' => $request->getPathInfo(),
                'user_agent' => $userAgent,
                'cors_enabled' => true,
                'allowed_origins' => config('cors.allowed_origins'),
            ]
        ]);
    }

    /**
     * CORS test endpoint specifically for testing cross-origin requests.
     */
    public function corsTest(Request $request): JsonResponse
    {
        return response()->json([
            'cors_test' => 'success',
            'message' => 'CORS is working correctly',
            'origin' => $request->headers->get('Origin'),
            'method' => $request->getMethod(),
            'timestamp' => now()->toISOString()
        ]);
    }
}