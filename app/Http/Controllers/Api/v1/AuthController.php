<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

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
        $user->load('role');
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Return user data and token, including role name
        return response()->json([
            'user'  => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'pf_number' => $user->pf_number,
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->name : null,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Handle user registration and assign staff role by default.
     */
    public function register(Request $request)
    {
        $logData = $request->except(['password', 'password_confirmation']);
        Log::info('register() called', ['request' => $logData]);
        $request->validate([
            'name' => 'required|string',
            'phone' => ['required','regex:/^255[0-9]{9}$/','unique:users,phone'],
            'pf_number' => ['required','regex:/^MLG\.\d{4}$/','unique:users,pf_number'],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Find staff role
        $staffRole = \App\Models\Role::where('name', 'staff')->first();
        if (!$staffRole) {
            return response()->json(['message' => 'Staff role not found.'], 500);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'pf_number' => $request->pf_number,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $staffRole->id,
        ]);

        return response()->json([
            'message' => 'Registration successful. Please login.',
        ], 201);
    }

    public function logout(Request $request)
    {
        Log::info('logout called', ['user_id' => $request->user() ? $request->user()->id : null]);
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
