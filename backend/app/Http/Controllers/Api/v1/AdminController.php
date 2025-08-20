<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOnboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class AdminController extends Controller
{
    /**
     * Get all users with their onboarding status (Admin only)
     */
    public function getUsers(Request $request)
    {
        try {
            // Check if current user is admin
            $currentUser = $request->user();
            if (!$currentUser->role || $currentUser->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'search' => 'sometimes|string|max:255',
                'role' => 'sometimes|string|max:100',
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Build query
            $query = User::with(['role', 'onboarding'])
                ->where('id', '!=', $currentUser->id); // Exclude current admin

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('pf_number', 'like', "%{$search}%");
                });
            }

            // Apply role filter
            if ($request->has('role') && $request->role) {
                $query->whereHas('role', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }

            // Exclude admin users from the list
            $query->whereHas('role', function ($q) {
                $q->where('name', '!=', 'admin');
            });

            // Pagination
            $perPage = $request->get('per_page', 20);
            $users = $query->paginate($perPage);

            // Transform the data
            $transformedUsers = $users->getCollection()->map(function ($user) {
                $onboarding = $user->onboarding;
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'pf_number' => $user->pf_number,
                    'phone' => $user->phone,
                    'role' => $user->role ? $user->role->name : null,
                    'role_display' => $user->role ? ucwords(str_replace('_', ' ', $user->role->name)) : null,
                    'created_at' => $user->created_at,
                    'onboarding_status' => [
                        'needs_onboarding' => $user->needsOnboarding(),
                        'terms_accepted' => $onboarding ? $onboarding->terms_accepted : false,
                        'terms_accepted_at' => $onboarding ? $onboarding->terms_accepted_at : null,
                        'ict_policy_accepted' => $onboarding ? $onboarding->ict_policy_accepted : false,
                        'ict_policy_accepted_at' => $onboarding ? $onboarding->ict_policy_accepted_at : null,
                        'declaration_submitted' => $onboarding ? $onboarding->declaration_submitted : false,
                        'declaration_submitted_at' => $onboarding ? $onboarding->declaration_submitted_at : null,
                        'completed' => $onboarding ? $onboarding->completed : false,
                        'completed_at' => $onboarding ? $onboarding->completed_at : null,
                        'current_step' => $onboarding ? $onboarding->current_step : 'terms-popup'
                    ]
                ];
            });

            Log::info('Admin retrieved users list', [
                'admin_id' => $currentUser->id,
                'total_users' => $users->total(),
                'search' => $request->search,
                'role_filter' => $request->role
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $transformedUsers,
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                        'from' => $users->firstItem(),
                        'to' => $users->lastItem()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving users for admin', [
                'admin_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users'
            ], 500);
        }
    }

    /**
     * Get specific user details with onboarding status (Admin only)
     */
    public function getUserDetails(Request $request, $userId)
    {
        try {
            // Check if current user is admin
            $currentUser = $request->user();
            if (!$currentUser->role || $currentUser->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $user = User::with(['role', 'onboarding'])->find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Don't allow viewing other admin users
            if ($user->role && $user->role->name === 'admin' && $user->id !== $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot view other admin users'
                ], 403);
            }

            $onboarding = $user->onboarding;

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'pf_number' => $user->pf_number,
                'phone' => $user->phone,
                'role' => $user->role ? $user->role->name : null,
                'role_display' => $user->role ? ucwords(str_replace('_', ' ', $user->role->name)) : null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'onboarding_status' => [
                    'needs_onboarding' => $user->needsOnboarding(),
                    'terms_accepted' => $onboarding ? $onboarding->terms_accepted : false,
                    'terms_accepted_at' => $onboarding ? $onboarding->terms_accepted_at : null,
                    'ict_policy_accepted' => $onboarding ? $onboarding->ict_policy_accepted : false,
                    'ict_policy_accepted_at' => $onboarding ? $onboarding->ict_policy_accepted_at : null,
                    'declaration_submitted' => $onboarding ? $onboarding->declaration_submitted : false,
                    'declaration_submitted_at' => $onboarding ? $onboarding->declaration_submitted_at : null,
                    'declaration_data' => $onboarding ? $onboarding->declaration_data : null,
                    'completed' => $onboarding ? $onboarding->completed : false,
                    'completed_at' => $onboarding ? $onboarding->completed_at : null,
                    'current_step' => $onboarding ? $onboarding->current_step : 'terms-popup'
                ]
            ];

            Log::info('Admin retrieved user details', [
                'admin_id' => $currentUser->id,
                'target_user_id' => $userId
            ]);

            return response()->json([
                'success' => true,
                'data' => $userData
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving user details for admin', [
                'admin_id' => $request->user()->id,
                'target_user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user details'
            ], 500);
        }
    }

    /**
     * Reset user onboarding status (Admin only)
     */
    public function resetUserOnboarding(Request $request)
    {
        try {
            // Check if current user is admin
            $currentUser = $request->user();
            if (!$currentUser->role || $currentUser->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'reset_type' => 'required|string|in:terms,ict,declaration,all'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $targetUserId = $request->user_id;
            $resetType = $request->reset_type;

            // Get target user
            $targetUser = User::with('role')->find($targetUserId);
            
            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Don't allow resetting other admin users
            if ($targetUser->role && $targetUser->role->name === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reset admin user onboarding'
                ], 403);
            }

            // Get or create onboarding record
            $onboarding = $targetUser->getOrCreateOnboarding();

            // Perform reset based on type
            switch ($resetType) {
                case 'terms':
                    $onboarding->update([
                        'terms_accepted' => false,
                        'terms_accepted_at' => null,
                        'ict_policy_accepted' => false,
                        'ict_policy_accepted_at' => null,
                        'declaration_submitted' => false,
                        'declaration_submitted_at' => null,
                        'declaration_data' => null,
                        'completed' => false,
                        'completed_at' => null,
                        'current_step' => 'terms-popup'
                    ]);
                    break;

                case 'ict':
                    $onboarding->update([
                        'ict_policy_accepted' => false,
                        'ict_policy_accepted_at' => null,
                        'declaration_submitted' => false,
                        'declaration_submitted_at' => null,
                        'declaration_data' => null,
                        'completed' => false,
                        'completed_at' => null,
                        'current_step' => 'policy-popup'
                    ]);
                    break;

                case 'declaration':
                    $onboarding->update([
                        'declaration_submitted' => false,
                        'declaration_submitted_at' => null,
                        'declaration_data' => null,
                        'completed' => false,
                        'completed_at' => null,
                        'current_step' => 'declaration'
                    ]);
                    break;

                case 'all':
                default:
                    $onboarding->update([
                        'terms_accepted' => false,
                        'terms_accepted_at' => null,
                        'ict_policy_accepted' => false,
                        'ict_policy_accepted_at' => null,
                        'declaration_submitted' => false,
                        'declaration_submitted_at' => null,
                        'declaration_data' => null,
                        'completed' => false,
                        'completed_at' => null,
                        'current_step' => 'terms-popup'
                    ]);
                    break;
            }

            Log::info('Admin reset user onboarding', [
                'admin_id' => $currentUser->id,
                'admin_name' => $currentUser->name,
                'target_user_id' => $targetUserId,
                'target_user_name' => $targetUser->name,
                'reset_type' => $resetType
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User onboarding reset successfully',
                'data' => [
                    'user_id' => $targetUserId,
                    'user_name' => $targetUser->name,
                    'reset_type' => $resetType,
                    'current_step' => $onboarding->current_step,
                    'reset_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting user onboarding', [
                'admin_id' => $request->user()->id,
                'target_user_id' => $request->user_id ?? null,
                'reset_type' => $request->reset_type ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset user onboarding'
            ], 500);
        }
    }

    /**
     * Get onboarding statistics (Admin only)
     */
    public function getOnboardingStats(Request $request)
    {
        try {
            // Check if current user is admin
            $currentUser = $request->user();
            if (!$currentUser->role || $currentUser->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // Get total users (excluding admins)
            $totalUsers = User::whereHas('role', function ($q) {
                $q->where('name', '!=', 'admin');
            })->count();

            // Get users with onboarding records
            $usersWithOnboarding = User::whereHas('onboarding')
                ->whereHas('role', function ($q) {
                    $q->where('name', '!=', 'admin');
                })->count();

            // Get completed onboarding count
            $completedOnboarding = User::whereHas('onboarding', function ($q) {
                $q->where('completed', true);
            })->whereHas('role', function ($q) {
                $q->where('name', '!=', 'admin');
            })->count();

            // Get users who accepted terms
            $termsAccepted = User::whereHas('onboarding', function ($q) {
                $q->where('terms_accepted', true);
            })->whereHas('role', function ($q) {
                $q->where('name', '!=', 'admin');
            })->count();

            // Get users who accepted ICT policy
            $ictPolicyAccepted = User::whereHas('onboarding', function ($q) {
                $q->where('ict_policy_accepted', true);
            })->whereHas('role', function ($q) {
                $q->where('name', '!=', 'admin');
            })->count();

            // Get users who submitted declaration
            $declarationSubmitted = User::whereHas('onboarding', function ($q) {
                $q->where('declaration_submitted', true);
            })->whereHas('role', function ($q) {
                $q->where('name', '!=', 'admin');
            })->count();

            // Calculate percentages
            $completionRate = $totalUsers > 0 ? round(($completedOnboarding / $totalUsers) * 100, 2) : 0;
            $termsRate = $totalUsers > 0 ? round(($termsAccepted / $totalUsers) * 100, 2) : 0;
            $ictRate = $totalUsers > 0 ? round(($ictPolicyAccepted / $totalUsers) * 100, 2) : 0;
            $declarationRate = $totalUsers > 0 ? round(($declarationSubmitted / $totalUsers) * 100, 2) : 0;

            $stats = [
                'total_users' => $totalUsers,
                'users_with_onboarding' => $usersWithOnboarding,
                'completed_onboarding' => $completedOnboarding,
                'pending_onboarding' => $totalUsers - $completedOnboarding,
                'terms_accepted' => $termsAccepted,
                'ict_policy_accepted' => $ictPolicyAccepted,
                'declaration_submitted' => $declarationSubmitted,
                'completion_rate' => $completionRate,
                'terms_acceptance_rate' => $termsRate,
                'ict_policy_acceptance_rate' => $ictRate,
                'declaration_submission_rate' => $declarationRate,
                'generated_at' => now()->toISOString()
            ];

            Log::info('Admin retrieved onboarding statistics', [
                'admin_id' => $currentUser->id,
                'stats' => Arr::except($stats, ['generated_at'])
            ]);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving onboarding statistics', [
                'admin_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve onboarding statistics'
            ], 500);
        }
    }

    /**
     * Bulk reset onboarding for multiple users (Admin only)
     */
    public function bulkResetOnboarding(Request $request)
    {
        try {
            // Check if current user is admin
            $currentUser = $request->user();
            if (!$currentUser->role || $currentUser->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array|min:1|max:50',
                'user_ids.*' => 'exists:users,id',
                'reset_type' => 'required|string|in:terms,ict,declaration,all'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userIds = $request->user_ids;
            $resetType = $request->reset_type;

            // Get target users (exclude admins)
            $targetUsers = User::with('role')
                ->whereIn('id', $userIds)
                ->whereHas('role', function ($q) {
                    $q->where('name', '!=', 'admin');
                })
                ->get();

            if ($targetUsers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid users found for reset'
                ], 404);
            }

            $resetResults = [];
            $successCount = 0;
            $failureCount = 0;

            foreach ($targetUsers as $user) {
                try {
                    $onboarding = $user->getOrCreateOnboarding();

                    // Perform reset based on type (same logic as single reset)
                    switch ($resetType) {
                        case 'terms':
                            $onboarding->update([
                                'terms_accepted' => false,
                                'terms_accepted_at' => null,
                                'ict_policy_accepted' => false,
                                'ict_policy_accepted_at' => null,
                                'declaration_submitted' => false,
                                'declaration_submitted_at' => null,
                                'declaration_data' => null,
                                'completed' => false,
                                'completed_at' => null,
                                'current_step' => 'terms-popup'
                            ]);
                            break;

                        case 'ict':
                            $onboarding->update([
                                'ict_policy_accepted' => false,
                                'ict_policy_accepted_at' => null,
                                'declaration_submitted' => false,
                                'declaration_submitted_at' => null,
                                'declaration_data' => null,
                                'completed' => false,
                                'completed_at' => null,
                                'current_step' => 'policy-popup'
                            ]);
                            break;

                        case 'declaration':
                            $onboarding->update([
                                'declaration_submitted' => false,
                                'declaration_submitted_at' => null,
                                'declaration_data' => null,
                                'completed' => false,
                                'completed_at' => null,
                                'current_step' => 'declaration'
                            ]);
                            break;

                        case 'all':
                        default:
                            $onboarding->update([
                                'terms_accepted' => false,
                                'terms_accepted_at' => null,
                                'ict_policy_accepted' => false,
                                'ict_policy_accepted_at' => null,
                                'declaration_submitted' => false,
                                'declaration_submitted_at' => null,
                                'declaration_data' => null,
                                'completed' => false,
                                'completed_at' => null,
                                'current_step' => 'terms-popup'
                            ]);
                            break;
                    }

                    $resetResults[] = [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'status' => 'success'
                    ];
                    $successCount++;

                } catch (\Exception $e) {
                    $resetResults[] = [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                    $failureCount++;
                }
            }

            Log::info('Admin performed bulk onboarding reset', [
                'admin_id' => $currentUser->id,
                'admin_name' => $currentUser->name,
                'reset_type' => $resetType,
                'total_users' => count($userIds),
                'success_count' => $successCount,
                'failure_count' => $failureCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Bulk reset completed. {$successCount} successful, {$failureCount} failed.",
                'data' => [
                    'reset_type' => $resetType,
                    'total_requested' => count($userIds),
                    'total_processed' => count($resetResults),
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                    'results' => $resetResults,
                    'reset_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error performing bulk onboarding reset', [
                'admin_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk onboarding reset'
            ], 500);
        }
    }
}