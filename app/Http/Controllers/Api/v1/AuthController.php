<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\UserOnboarding;

class AuthController extends Controller
{
    /**
     * Handle user login and return an authentication token.
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

        Log::info('Login attempt for email: ' . $request->email);

        // Attempt to authenticate the user
        if (!Auth::attempt($credentials)) {
            Log::warning('Failed login attempt for email: ' . $request->email);
            return response()->json([
                'message' => 'Invalid email or password.'
            ], 401);
        }
      
        $user = User::find(Auth::id());
        $user->load(['role', 'onboarding']);
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Get onboarding status
        $onboarding = $user->getOrCreateOnboarding();
        
        // Return user data and token, including role name and onboarding status
        return response()->json([
            'user'  => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'pf_number' => $user->pf_number,
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->name : null,
                'needs_onboarding' => $user->needsOnboarding(),
                'onboarding_step' => $onboarding->current_step,
            ],
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            Log::info('logout called', ['user_id' => $user ? $user->id : null]);
            
            if ($user) {
                // Delete the current access token
                $user->currentAccessToken()->delete();
                
                Log::info('User logged out successfully', ['user_id' => $user->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No authenticated user found'
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
}
