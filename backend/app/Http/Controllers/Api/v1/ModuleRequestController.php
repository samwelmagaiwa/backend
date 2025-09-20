<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\WellsoftModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ModuleRequestController extends Controller
{
    /**
     * Store a new module request.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                'user_access_id' => 'required|exists:user_access,id',
                'module_requested_for' => 'required|string|in:use,revoke',
                'wellsoft_modules' => 'required|array|min:1',
                'wellsoft_modules.*' => 'required|string|exists:wellsoft_modules,name',
            ], [
                'module_requested_for.required' => 'Please select whether you want to use or revoke modules.',
                'module_requested_for.in' => 'Module request type must be either "use" or "revoke".',
                'wellsoft_modules.required' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.min' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.*.exists' => 'One or more selected modules are invalid.',
            ]);

            Log::info('🔄 PROCESSING MODULE REQUEST', [
                'user_id' => $user->id,
                'user_access_id' => $validatedData['user_access_id'],
                'module_requested_for' => $validatedData['module_requested_for'],
                'wellsoft_modules_count' => count($validatedData['wellsoft_modules']),
                'wellsoft_modules' => $validatedData['wellsoft_modules']
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($validatedData['user_access_id']);
            
            // Check if the user owns this request or has permission to modify it
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to modify this request.'
                ], 403);
            }

            // Update the user_access record with module_requested_for
            $userAccess->update([
                'module_requested_for' => $validatedData['module_requested_for']
            ]);

            // Get the module IDs from the module names
            $moduleIds = WellsoftModule::whereIn('name', $validatedData['wellsoft_modules'])
                                     ->where('is_active', true)
                                     ->pluck('id', 'name')
                                     ->toArray();

            // Validate that all requested modules exist and are active
            $missingModules = array_diff($validatedData['wellsoft_modules'], array_keys($moduleIds));
            if (!empty($missingModules)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some selected modules are not available: ' . implode(', ', $missingModules),
                    'errors' => [
                        'wellsoft_modules' => ['Some selected modules are not available.']
                    ]
                ], 422);
            }

            // Clear existing module selections for this request
            $userAccess->selectedWellsoftModules()->detach();

            // Attach the new module selections
            $userAccess->selectedWellsoftModules()->attach(array_values($moduleIds));

            Log::info('✅ MODULE REQUEST STORED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'module_requested_for' => $userAccess->module_requested_for,
                'modules_attached' => count($moduleIds),
                'module_ids' => array_values($moduleIds)
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['selectedWellsoftModules', 'user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Module request stored successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'module_requested_for' => $userAccess->module_requested_for,
                    'selected_modules' => $userAccess->selectedWellsoftModules->pluck('name')->toArray(),
                    'selected_modules_count' => $userAccess->selectedWellsoftModules->count(),
                    'request_status' => $userAccess->status,
                    'user_name' => $userAccess->user->name,
                    'department_name' => $userAccess->department->name ?? 'Unknown'
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error storing module request', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store module request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available Wellsoft modules.
     */
    public function getAvailableModules(): JsonResponse
    {
        try {
            $modules = WellsoftModule::active()
                                   ->orderBy('name')
                                   ->get(['id', 'name', 'description']);

            return response()->json([
                'status' => 'success',
                'data' => $modules,
                'count' => $modules->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching available modules', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch available modules.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get module request details for a specific user access record.
     */
    public function show(int $userAccessId): JsonResponse
    {
        try {
            $userAccess = UserAccess::with(['selectedWellsoftModules', 'user', 'department'])
                                  ->findOrFail($userAccessId);

            $user = request()->user();
            
            // Check if the user has permission to view this request
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to view this request.'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'module_requested_for' => $userAccess->module_requested_for,
                    'selected_modules' => $userAccess->selectedWellsoftModules->map(function ($module) {
                        return [
                            'id' => $module->id,
                            'name' => $module->name,
                            'description' => $module->description
                        ];
                    }),
                    'selected_modules_count' => $userAccess->selectedWellsoftModules->count(),
                    'request_status' => $userAccess->status,
                    'user_info' => [
                        'id' => $userAccess->user->id,
                        'name' => $userAccess->user->name,
                        'pf_number' => $userAccess->pf_number,
                        'staff_name' => $userAccess->staff_name
                    ],
                    'department_info' => [
                        'id' => $userAccess->department->id ?? null,
                        'name' => $userAccess->department->name ?? 'Unknown'
                    ],
                    'created_at' => $userAccess->created_at,
                    'updated_at' => $userAccess->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching module request details', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch module request details.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update an existing module request.
     */
    public function update(Request $request, int $userAccessId): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                'module_requested_for' => 'required|string|in:use,revoke',
                'wellsoft_modules' => 'required|array|min:1',
                'wellsoft_modules.*' => 'required|string|exists:wellsoft_modules,name',
            ], [
                'module_requested_for.required' => 'Please select whether you want to use or revoke modules.',
                'module_requested_for.in' => 'Module request type must be either "use" or "revoke".',
                'wellsoft_modules.required' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.min' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.*.exists' => 'One or more selected modules are invalid.',
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Check if the user owns this request or has permission to modify it
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to modify this request.'
                ], 403);
            }

            // Check if the request can be updated (only pending or rejected requests)
            if (!$userAccess->canBeUpdated()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This request cannot be updated. Current status: ' . $userAccess->status
                ], 403);
            }

            Log::info('🔄 UPDATING MODULE REQUEST', [
                'user_id' => $user->id,
                'user_access_id' => $userAccessId,
                'module_requested_for' => $validatedData['module_requested_for'],
                'wellsoft_modules_count' => count($validatedData['wellsoft_modules']),
                'wellsoft_modules' => $validatedData['wellsoft_modules']
            ]);

            // Update the user_access record with module_requested_for
            $userAccess->update([
                'module_requested_for' => $validatedData['module_requested_for']
            ]);

            // Get the module IDs from the module names
            $moduleIds = WellsoftModule::whereIn('name', $validatedData['wellsoft_modules'])
                                     ->where('is_active', true)
                                     ->pluck('id', 'name')
                                     ->toArray();

            // Validate that all requested modules exist and are active
            $missingModules = array_diff($validatedData['wellsoft_modules'], array_keys($moduleIds));
            if (!empty($missingModules)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some selected modules are not available: ' . implode(', ', $missingModules),
                    'errors' => [
                        'wellsoft_modules' => ['Some selected modules are not available.']
                    ]
                ], 422);
            }

            // Clear existing module selections for this request
            $userAccess->selectedWellsoftModules()->detach();

            // Attach the new module selections
            $userAccess->selectedWellsoftModules()->attach(array_values($moduleIds));

            Log::info('✅ MODULE REQUEST UPDATED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'module_requested_for' => $userAccess->module_requested_for,
                'modules_attached' => count($moduleIds),
                'module_ids' => array_values($moduleIds)
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['selectedWellsoftModules', 'user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Module request updated successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'module_requested_for' => $userAccess->module_requested_for,
                    'selected_modules' => $userAccess->selectedWellsoftModules->pluck('name')->toArray(),
                    'selected_modules_count' => $userAccess->selectedWellsoftModules->count(),
                    'request_status' => $userAccess->status,
                    'user_name' => $userAccess->user->name,
                    'department_name' => $userAccess->department->name ?? 'Unknown'
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating module request', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'user_access_id' => $userAccessId,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update module request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}