<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HodUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        // Allow Heads of Department, Head of IT (and admins for maintenance/testing) to use this endpoint
        $this->middleware('role:head_of_department,head_of_it,admin');
    }

    /**
     * Allow Head of Department to create a new staff user for their department.
     *
     * This is a constrained variant of the Admin user creation flow:
     * - Role is always forced to `staff` (no privilege escalation).
     * - Department is limited to departments where the HOD is assigned as HOD,
     *   falling back to their own primary department when not provided.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'pf_number' => 'required|string|max:50|unique:users',
            'department_id' => 'nullable|exists:departments,id',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'pf_number.required' => 'PF number is required.',
            'pf_number.unique' => 'This PF number is already registered.',
            'phone.required' => 'Phone number is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.same' => 'Password confirmation does not match.',
            'department_id.exists' => 'Selected department does not exist.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $currentUser = $request->user();

        // Determine which department the new user should belong to.
        $departmentId = $request->input('department_id');

        if ($departmentId) {
            // HOD/Head of IT may only assign users to departments where they are the HOD (unless admin).
            // Head of IT can assign to their own department.
            if (!$currentUser->isAdmin()) {
                $isHodOfDepartment = Department::where('id', $departmentId)
                    ->where('hod_user_id', $currentUser->id)
                    ->exists();
                
                // Allow Head of IT to assign to their own department
                $isOwnDepartment = $currentUser->hasRole('head_of_it') && 
                                   $departmentId == $currentUser->department_id;

                if (!$isHodOfDepartment && !$isOwnDepartment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'department_id' => [
                                'You can only assign users to departments where you are the Head of Department, or to your own department.',
                            ],
                        ],
                    ], 422);
                }
            }
        } else {
            // If department_id is not explicitly provided, default to one of the HOD departments
            // or their own primary department.
            if ($currentUser->hasRole('head_of_department')) {
                $hodDepartment = $currentUser->departmentsAsHOD()->first();
                $departmentId = $hodDepartment?->id ?? $currentUser->department_id;
            } elseif ($currentUser->hasRole('head_of_it')) {
                // Head of IT defaults to their own department
                $departmentId = $currentUser->department_id;
            } else {
                // Admin or fallback
                $departmentId = $currentUser->department_id;
            }
            
            // Ensure department_id is not null
            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'department_id' => [
                            'Department is required. Please select a department or ensure your account has an assigned department.',
                        ],
                    ],
                ], 422);
            }
        }

        // Look up the staff role; this is the only role HODs may assign.
        $staffRole = Role::where('name', 'staff')->first();

        if (!$staffRole) {
            Log::error('HodUserController: staff role not found');

            return response()->json([
                'success' => false,
                'message' => 'Staff role is not configured. Please contact the system administrator.',
            ], 500);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'pf_number' => $request->pf_number,
                'department_id' => $departmentId,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            // Attach staff role with pivot metadata consistent with AdminUserController.
            $user->roles()->attach([
                $staffRole->id => [
                    'assigned_at' => now(),
                    'assigned_by' => $currentUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // Log role assignment for audit trail.
            \App\Models\RoleChangeLog::create([
                'user_id' => $user->id,
                'role_id' => $staffRole->id,
                'action' => 'assigned',
                'changed_by' => $currentUser->id,
                'changed_at' => now(),
                'metadata' => [
                    'user_email' => $user->email,
                    'changed_by_email' => $currentUser->email,
                    'context' => 'hod_user_creation',
                ],
            ]);

            DB::commit();

            $user->load('roles', 'department', 'departmentsAsHOD');

            Log::info('User created by HOD', [
                'hod_id' => $currentUser->id,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'department_id' => $departmentId,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'pf_number' => $user->pf_number,
                        'phone' => $user->phone,
                        'department_id' => $user->department_id,
                        'department' => $user->department ? [
                            'id' => $user->department->id,
                            'name' => $user->department->name,
                            'code' => $user->department->code,
                            'display_name' => $user->department->getFullNameAttribute(),
                        ] : null,
                        'is_active' => $user->is_active,
                        'roles' => $user->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name,
                                'display_name' => $role->getDisplayName(),
                                'assigned_at' => $role->pivot->assigned_at,
                            ];
                        }),
                        'display_roles' => $user->getDisplayRoleNames(),
                        'created_at' => $user->created_at,
                    ],
                ],
                'message' => 'Staff user created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating user via HOD', [
                'hod_id' => $currentUser->id,
                'user_role' => $currentUser->getPrimaryRoleName(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the user. Please try again.',
            ], 500);
        }
    }
}