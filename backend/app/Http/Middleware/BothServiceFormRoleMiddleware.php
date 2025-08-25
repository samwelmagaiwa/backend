<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BothServiceFormRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredRole = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Get user roles (many-to-many relationship)
        $userRoles = $user->roles()->pluck('name')->toArray();
        
        if (empty($userRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'User has no roles assigned'
            ], 403);
        }

        // Define allowed roles for both-service-form
        $allowedRoles = [
            'head_of_department',
            'divisional_director',
            'ict_director',
            'hod_it',
            'ict_officer',
            'admin',
            'super_admin',
            'staff' // Regular staff can create forms
        ];

        // Check if user has any allowed role
        if (!array_intersect($userRoles, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions for both-service-form access'
            ], 403);
        }

        // If a specific role is required for this route, check it
        if ($requiredRole) {
            $roleMapping = [
                'hod' => ['head_of_department'],
                'divisional_director' => ['divisional_director'],
                'dict' => ['ict_director'],
                'hod_it' => ['hod_it'],
                'ict_officer' => ['ict_officer'],
                'admin' => ['admin', 'super_admin'],
            ];

            $allowedForRoute = $roleMapping[$requiredRole] ?? [];
            
            if (!array_intersect($userRoles, $allowedForRoute)) {
                return response()->json([
                    'success' => false,
                    'message' => "Access denied. Required role: {$requiredRole}"
                ], 403);
            }
        }

        return $next($request);
    }
}