<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserOnboarding;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Handle user login and return an authentication token.
     * Supports multiple concurrent sessions per user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $logData = $request->except(['password', 'password_confirmation']);
        Log::info('login() called', ['request' => $logData]);
        
        // Validate incoming request data
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        
        $credentials = $request->only('email', 'password');
        $userAgent = $request->header('User-Agent', 'Unknown');
        $ipAddress = $request->ip();

        Log::info('Login attempt for email: ' . $request->email, [
            'ip' => $ipAddress,
            'user_agent' => $userAgent
        ]);
        
        // Find user by email with eager loading to reduce queries
        $user = User::with(['role', 'roles', 'onboarding'])
                   ->where('email', $request->email)
                   ->first();
        
        if (!$user) {
            Log::warning('User not found in database: ' . $request->email);
            return response()->json([
                'message' => 'Invalid email or password.'
            ], 401);
        }
        
        Log::info('User found in database', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_id' => $user->role_id,
            'old_role_name' => $user->role ? $user->role->name : null,
            'primary_role_name' => $user->getPrimaryRoleName(),
            'many_to_many_roles' => $user->roles()->pluck('name')->toArray(),
            'permissions' => $user->getAllPermissions()
        ]);
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Password verification failed for user: ' . $request->email);
            return response()->json([
                'message' => 'Invalid email or password.'
            ], 401);
        }
        
        Log::info('Password verification successful for user: ' . $request->email);
        
        // Relationships already loaded with eager loading above
        
        // Create a unique token name for this session
        $tokenName = $this->generateTokenName($user, $userAgent, $ipAddress);
        
        // Create token with abilities based on user role
        $abilities = $this->getTokenAbilities($user);
        $token = $user->createToken($tokenName, $abilities)->plainTextToken;
        
        Log::info('Token created successfully', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'token_name' => $tokenName,
            'abilities' => $abilities,
            'token_length' => strlen($token)
        ]);
        
        // Get onboarding status
        $onboarding = $user->getOrCreateOnboarding();
        
        $responseData = [
            'user'  => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'pf_number' => $user->pf_number,
                'role_id' => $user->role_id,
                'role_name' => $user->getPrimaryRoleName(),
                'roles' => $user->roles()->pluck('name')->toArray(),
                'permissions' => $user->getAllPermissions(),
                'needs_onboarding' => $user->needsOnboarding(),
                'onboarding_step' => $onboarding->current_step,
            ],
            'token' => $token,
            'token_name' => $tokenName,
            'session_info' => [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'created_at' => Carbon::now()->toISOString()
            ]
        ];
        
        Log::info('Login successful, returning response', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role_name' => $user->getPrimaryRoleName(),
            'roles' => $user->roles()->pluck('name')->toArray(),
            'token_name' => $tokenName
        ]);
        
        // Return user data and token, including role name and onboarding status
        return response()->json($responseData, 200);
    }
    
    /**
     * Generate a unique token name for this session
     */
    private function generateTokenName(User $user, string $userAgent, string $ipAddress): string
    {
        $browser = $this->getBrowserName($userAgent);
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $rolePrefix = $user->getPrimaryRoleName() ? strtoupper($user->getPrimaryRoleName()) : 'USER';
        
        return "{$rolePrefix}_{$browser}_{$ipAddress}_{$timestamp}";
    }
    
    /**
     * Extract browser name from user agent
     */
    private function getBrowserName(string $userAgent): string
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } else {
            return 'Unknown';
        }
    }
    
    /**
     * Get token abilities based on user role
     */
    private function getTokenAbilities(User $user): array
    {
        $baseAbilities = ['read-profile', 'update-profile'];
        
        $userRoles = $user->roles()->pluck('name')->toArray();
        
        if (empty($userRoles)) {
            return $baseAbilities;
        }
        
        $abilities = $baseAbilities;
        
        // Add abilities based on roles
        if (array_intersect($userRoles, ['admin'])) {
            $abilities = array_merge($abilities, [
                'admin-access',
                'manage-users',
                'manage-requests',
                'view-all-data',
                'system-settings'
            ]);
        }
        
        if (array_intersect($userRoles, ['divisional_director', 'head_of_department', 'ict_director', 'ict_officer'])) {
            $abilities = array_merge($abilities, [
                'approver-access',
                'review-requests',
                'approve-requests',
                'view-department-data'
            ]);
        }
        
        if (array_intersect($userRoles, ['staff'])) {
            $abilities = array_merge($abilities, [
                'staff-access',
                'create-requests',
                'view-own-requests'
            ]);
        }
        
        return array_unique($abilities);
    }

    /**
     * Logout user by revoking the current token.
     * Only affects the current session/tab.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $currentToken = $request->user()->currentAccessToken();
            
            Log::info('logout called', [
                'user_id' => $user ? $user->id : null,
                'token_name' => $currentToken ? $currentToken->name : null
            ]);
            
            if ($user && $currentToken) {
                // Delete only the current access token (this session)
                $currentToken->delete();
                
                Log::info('User logged out successfully', [
                    'user_id' => $user->id,
                    'token_name' => $currentToken->name,
                    'remaining_tokens' => $user->tokens()->count()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully',
                    'session_ended' => true
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No authenticated user or token found'
            ], 401);
            
        } catch (\Exception $e) {
            Log::error('Logout error', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Logout failed'
            ], 500);
        }
    }
    
    /**
     * Logout from all sessions by revoking all tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll(Request $request)
    {
        try {
            $user = $request->user();
            
            Log::info('logoutAll called', ['user_id' => $user ? $user->id : null]);
            
            if ($user) {
                $tokenCount = $user->tokens()->count();
                
                // Delete all access tokens for this user
                $user->tokens()->delete();
                
                Log::info('User logged out from all sessions', [
                    'user_id' => $user->id,
                    'tokens_revoked' => $tokenCount
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logged out from all sessions successfully',
                    'tokens_revoked' => $tokenCount
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No authenticated user found'
            ], 401);
            
        } catch (\Exception $e) {
            Log::error('LogoutAll error', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Logout from all sessions failed'
            ], 500);
        }
    }
    
    /**
     * Get active sessions for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveSessions(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found'
                ], 401);
            }
            
            $sessions = $user->tokens()->select('id', 'name', 'created_at', 'last_used_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($token) {
                    $parts = explode('_', $token->name);
                    return [
                        'id' => $token->id,
                        'name' => $token->name,
                        'role' => $parts[0] ?? 'Unknown',
                        'browser' => $parts[1] ?? 'Unknown',
                        'ip_address' => $parts[2] ?? 'Unknown',
                        'created_at' => $token->created_at,
                        'last_used_at' => $token->last_used_at,
                        'is_current' => $token->id === $request->user()->currentAccessToken()->id
                    ];
                });
            
            return response()->json([
                'success' => true,
                'sessions' => $sessions,
                'total_sessions' => $sessions->count()
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('GetActiveSessions error', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active sessions'
            ], 500);
        }
    }
    
    /**
     * Revoke a specific session by token ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeSession(Request $request)
    {
        try {
            $request->validate([
                'token_id' => 'required|integer'
            ]);
            
            $user = $request->user();
            $tokenId = $request->token_id;
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found'
                ], 401);
            }
            
            $token = $user->tokens()->where('id', $tokenId)->first();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }
            
            $tokenName = $token->name;
            $token->delete();
            
            Log::info('Session revoked', [
                'user_id' => $user->id,
                'token_id' => $tokenId,
                'token_name' => $tokenName
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Session revoked successfully'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('RevokeSession error', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke session'
            ], 500);
        }
    }

    /**
     * Get current authenticated user with fresh data.
     * Used for token verification and session restoration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found'
                ], 401);
            }
            
            // Refresh user data with relationships
            $user->load(['role', 'roles', 'onboarding']);
            
            // Get onboarding status
            $onboarding = $user->getOrCreateOnboarding();
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'pf_number' => $user->pf_number,
                'role_id' => $user->role_id,
                'role_name' => $user->getPrimaryRoleName(),
                'roles' => $user->roles()->pluck('name')->toArray(),
                'permissions' => $user->getAllPermissions(),
                'needs_onboarding' => $user->needsOnboarding(),
                'onboarding_step' => $onboarding->current_step,
            ];
            
            Log::info('getCurrentUser called', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role_name' => $user->getPrimaryRoleName(),
                'roles' => $user->roles()->pluck('name')->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $userData
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('getCurrentUser error', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user data'
            ], 500);
        }
    }

    /**
     * Get role-based redirect URL for authenticated user.
     * Used by frontend to determine where to redirect after login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoleBasedRedirect(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found'
                ], 401);
            }
            
            $primaryRole = $user->getPrimaryRoleName();
            
            // Define role-based redirects
            $redirectMap = [
                'admin' => '/admin-dashboard',
                'head_of_department' => '/hod-dashboard',
                'divisional_director' => '/divisional-dashboard',
                'ict_director' => '/dict-dashboard',
                'ict_officer' => '/ict-dashboard',
                'staff' => '/user-dashboard'
            ];
            
            $redirectUrl = $redirectMap[$primaryRole] ?? '/user-dashboard';
            
            // Check if user needs onboarding (except admin)
            if ($primaryRole !== 'admin' && $user->needsOnboarding()) {
                $redirectUrl = '/onboarding';
            }
            
            Log::info('getRoleBasedRedirect called', [
                'user_id' => $user->id,
                'role' => $primaryRole,
                'redirect_url' => $redirectUrl,
                'needs_onboarding' => $user->needsOnboarding()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'redirect_url' => $redirectUrl,
                    'role' => $primaryRole,
                    'needs_onboarding' => $user->needsOnboarding()
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('getRoleBasedRedirect error', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to determine redirect URL'
            ], 500);
        }
    }
}