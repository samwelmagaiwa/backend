<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenAbilities
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$abilities): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $token = $user->currentAccessToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'No valid token found'
            ], 401);
        }

        // Check if token has required abilities
        foreach ($abilities as $ability) {
            if (!$token->can($ability)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions for this action',
                    'required_ability' => $ability
                ], 403);
            }
        }

        return $next($request);
    }
}