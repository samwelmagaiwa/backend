<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
                'error' => 'Unauthenticated'
            ], 401);
        }

        $user = Auth::user();

        // Check if user has any roles assigned
        if (!$user->roles()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User role not assigned.',
                'error' => 'No role assigned'
            ], 403);
        }

        // Check if user has any of the required roles using new many-to-many system
        if (!$user->hasAnyRole($roles)) {
            $userRoles = $user->roles()->pluck('name')->toArray();
            
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions.',
                'error' => 'Access denied',
                'required_roles' => $roles,
                'user_roles' => $userRoles
            ], 403);
        }

        return $next($request);
    }
}