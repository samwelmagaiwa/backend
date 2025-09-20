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
            $response = response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
            
            // Ensure CORS headers are present
            return $this->addCorsHeaders($request, $response);
        }

        // Get user roles (many-to-many relationship)
        $userRoles = $user->roles()->pluck('name')->toArray();
        
        if (empty($userRoles)) {
            $response = response()->json([
                'success' => false,
                'message' => 'User has no roles assigned'
            ], 403);
            
            // Ensure CORS headers are present
            return $this->addCorsHeaders($request, $response);
        }

        // Define allowed roles for both-service-form
        $allowedRoles = [
            'head_of_department',
            'divisional_director',
            'ict_director',
            'ict_officer',
            'admin',
            'staff' // Regular staff can create forms
        ];

        // Check if user has any allowed role
        if (!array_intersect($userRoles, $allowedRoles)) {
            $response = response()->json([
                'success' => false,
                'message' => 'Insufficient permissions for both-service-form access'
            ], 403);
            
            // Ensure CORS headers are present
            return $this->addCorsHeaders($request, $response);
        }

        // If a specific role is required for this route, check it
        if ($requiredRole) {
            // Support comma-separated roles
            $requiredRoles = explode(',', $requiredRole);
            
            $roleMapping = [
                'hod' => ['head_of_department'],
                'divisional_director' => ['divisional_director'],
                'dict' => ['ict_director'],
                'ict_officer' => ['ict_officer'],
                'admin' => ['admin'],
            ];

            $allowedForRoute = [];
            foreach ($requiredRoles as $role) {
                $role = trim($role); // Remove any whitespace
                if (isset($roleMapping[$role])) {
                    $allowedForRoute = array_merge($allowedForRoute, $roleMapping[$role]);
                }
            }
            
            if (!array_intersect($userRoles, $allowedForRoute)) {
                $response = response()->json([
                    'success' => false,
                    'message' => "Access denied. Required roles: {$requiredRole}"
                ], 403);
                
                // Ensure CORS headers are present
                return $this->addCorsHeaders($request, $response);
            }
        }

        return $next($request);
    }
    
    /**
     * Add CORS headers to response
     */
    private function addCorsHeaders(Request $request, $response)
    {
        $origin = $request->header('Origin');
        $allowedOrigins = [
            'http://localhost:8080',
            'http://localhost:8081',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:8081'
        ];
        
        if (in_array($origin, $allowedOrigins)) {
            $response->header('Access-Control-Allow-Origin', $origin);
        }
        
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->header('Access-Control-Allow-Credentials', 'true');
        
        return $response;
    }
}
