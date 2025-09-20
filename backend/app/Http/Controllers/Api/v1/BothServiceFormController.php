<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\Department;
use App\Models\User;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class BothServiceFormController extends Controller
{
    /**
     * Update HOD approval for a module request with proper file upload and module selection
     */
    public function updateHodApproval(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Check if user is HOD
            if (!array_intersect($userRoles, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only HOD can perform this action.'
                ], 403);
            }
            
            Log::info('🔍 HOD APPROVAL UPDATE START', [
                'user_access_id' => $userAccessId,
                'hod_user_id' => $currentUser->id,
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'has_files' => $request->hasFile('hod_signature'),
                'all_input_keys' => array_keys($request->all())
            ]);
            
            // Enhanced validation with better error messages
            $validated = $request->validate([
                'hod_name' => 'required|string|max:255',
                'hod_signature' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'approved_date' => 'required|date',
                'comments' => 'nullable|string|max:1000',
                'access_type' => 'required|in:permanent,temporary',
                'temporary_until' => 'required_if:access_type,temporary|nullable|date|after:today',
                // Module selection validation
                'wellsoft_modules_selected' => 'sometimes|array',
                'wellsoft_modules_selected.*' => 'string',
                'jeeva_modules_selected' => 'sometimes|array', 
                'jeeva_modules_selected.*' => 'string',
                'module_requested_for' => 'sometimes|string|in:use,revoke',
            ], [
                'hod_name.required' => 'HOD name is required',
                'hod_signature.required' => 'HOD signature file is required',
                'hod_signature.mimes' => 'Signature must be in JPEG, JPG, PNG, or PDF format',
                'hod_signature.max' => 'Signature file must not exceed 2MB',
                'approved_date.required' => 'Approval date is required',
                'approved_date.date' => 'Please provide a valid approval date',
                'access_type.required' => 'Access type (permanent/temporary) is required',
                'access_type.in' => 'Access type must be either permanent or temporary',
                'temporary_until.required_if' => 'End date is required for temporary access',
                'temporary_until.after' => 'End date must be in the future',
            ]);
            
            Log::info('✅ Validation passed', [
                'validated_data' => Arr::except($validated, ['hod_signature']),
                'has_signature_file' => isset($validated['hod_signature'])
            ]);
            
            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Verify HOD can approve this request (from their department)
            $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
            if (!$hodDepartment || $userAccess->department_id !== $hodDepartment->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You can only approve requests from your department.'
                ], 403);
            }
            
            Log::info('🏢 Department verification passed', [
                'hod_department_id' => $hodDepartment->id,
                'request_department_id' => $userAccess->department_id,
                'department_name' => $hodDepartment->name
            ]);
            
            DB::beginTransaction();
            
            // Handle signature upload with proper error handling
            $hodSignaturePath = null;
            if ($request->hasFile('hod_signature')) {
                try {
                    $hodSignatureFile = $request->file('hod_signature');
                    
                    // Validate file before processing
                    if (!$hodSignatureFile->isValid()) {
                        throw new \Exception('Uploaded signature file is invalid');
                    }
                    
                    // Create directory if it doesn't exist
                    $signatureDir = 'signatures/hod';
                    if (!Storage::disk('public')->exists($signatureDir)) {
                        Storage::disk('public')->makeDirectory($signatureDir);
                    }
                    
                    // Generate unique filename
                    $filename = 'hod_signature_' . $userAccess->pf_number . '_' . time() . '.' . $hodSignatureFile->getClientOriginalExtension();
                    
                    // Store the file
                    $hodSignaturePath = $hodSignatureFile->storeAs($signatureDir, $filename, 'public');
                    
                    // Verify file was actually stored
                    if (!Storage::disk('public')->exists($hodSignaturePath)) {
                        throw new \Exception('Failed to store signature file');
                    }
                    
                    Log::info('✅ HOD signature uploaded successfully', [
                        'original_name' => $hodSignatureFile->getClientOriginalName(),
                        'stored_path' => $hodSignaturePath,
                        'file_size' => $hodSignatureFile->getSize(),
                        'mime_type' => $hodSignatureFile->getMimeType()
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('❌ HOD signature upload failed', [
                        'error' => $e->getMessage(),
                        'file_info' => [
                            'name' => $request->file('hod_signature')->getClientOriginalName(),
                            'size' => $request->file('hod_signature')->getSize(),
                            'mime' => $request->file('hod_signature')->getMimeType()
                        ]
                    ]);
                    
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload HOD signature: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                Log::error('❌ No HOD signature file provided', [
                    'has_file' => $request->hasFile('hod_signature'),
                    'files' => $request->allFiles()
                ]);
                
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'HOD signature file is required'
                ], 422);
            }
            
            // Process module selections properly
            $updateData = [
                'hod_name' => $validated['hod_name'],
                'hod_signature_path' => $hodSignaturePath,
                'hod_approved_at' => $validated['approved_date'],
                'hod_comments' => $validated['comments'] ?? null,
                'hod_approved_by' => $currentUser->id,
                'hod_approved_by_name' => $currentUser->name,
                'access_type' => $validated['access_type'],
                'temporary_until' => $validated['access_type'] === 'temporary' ? $validated['temporary_until'] : null,
                'status' => 'hod_approved'
            ];
            
            // Handle module selections if provided
            if ($request->has('wellsoft_modules_selected')) {
                $wellsoftModules = $request->input('wellsoft_modules_selected', []);
                if (is_array($wellsoftModules)) {
                    $updateData['wellsoft_modules_selected'] = $wellsoftModules;
                    
                    Log::info('✅ Wellsoft modules processed', [
                        'modules' => $wellsoftModules,
                        'count' => count($wellsoftModules)
                    ]);
                }
            }
            
            if ($request->has('jeeva_modules_selected')) {
                $jeevaModules = $request->input('jeeva_modules_selected', []);
                if (is_array($jeevaModules)) {
                    $updateData['jeeva_modules_selected'] = $jeevaModules;
                    
                    Log::info('✅ Jeeva modules processed', [
                        'modules' => $jeevaModules,
                        'count' => count($jeevaModules)
                    ]);
                }
            }
            
            if ($request->has('module_requested_for')) {
                $updateData['module_requested_for'] = $request->input('module_requested_for', 'use');
                
                Log::info('✅ Module request type processed', [
                    'module_requested_for' => $updateData['module_requested_for']
                ]);
            }
            
            Log::info('🔄 Updating user access record', [
                'user_access_id' => $userAccess->id,
                'update_data' => Arr::except($updateData, ['hod_signature_path']),
                'has_signature_path' => !empty($updateData['hod_signature_path'])
            ]);
            
            // Update the user access record
            $userAccess->update($updateData);
            
            // Verify the update was successful
            $userAccess->refresh();
            
            Log::info('✅ User access record updated successfully', [
                'user_access_id' => $userAccess->id,
                'hod_name' => $userAccess->hod_name,
                'hod_signature_path' => $userAccess->hod_signature_path,
                'hod_approved_at' => $userAccess->hod_approved_at,
                'access_type' => $userAccess->access_type,
                'temporary_until' => $userAccess->temporary_until,
                'wellsoft_modules_count' => count($userAccess->wellsoft_modules_selected ?? []),
                'jeeva_modules_count' => count($userAccess->jeeva_modules_selected ?? []),
                'module_requested_for' => $userAccess->module_requested_for,
                'status' => $userAccess->status
            ]);
            
            DB::commit();
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'HOD approval updated successfully.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->status,
                    'access_rights' => [
                        'type' => $userAccess->access_type,
                        'temporary_until' => $userAccess->temporary_until,
                        'description' => $userAccess->access_type === 'permanent' 
                            ? 'Permanent (until retirement)' 
                            : 'Temporary until ' . ($userAccess->temporary_until ? $userAccess->temporary_until->format('F j, Y') : 'N/A')
                    ],
                    'hod_approval' => [
                        'name' => $userAccess->hod_name,
                        'approved_at' => $userAccess->hod_approved_at->format('Y-m-d H:i:s'),
                        'approved_at_formatted' => $userAccess->hod_approved_at->format('m/d/Y'),
                        'comments' => $userAccess->hod_comments,
                        'signature_url' => Storage::url($userAccess->hod_signature_path),
                        'signature_path' => $userAccess->hod_signature_path,
                        'approved_by' => $userAccess->hod_approved_by_name
                    ],
                    'modules' => [
                        'wellsoft_modules_selected' => $userAccess->wellsoft_modules_selected ?? [],
                        'jeeva_modules_selected' => $userAccess->jeeva_modules_selected ?? [],
                        'module_requested_for' => $userAccess->module_requested_for,
                        'wellsoft_count' => count($userAccess->wellsoft_modules_selected ?? []),
                        'jeeva_count' => count($userAccess->jeeva_modules_selected ?? [])
                    ],
                    'next_step' => $userAccess->getNextApprovalNeeded()
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Validation failed for HOD approval', [
                'errors' => $e->errors(),
                'user_access_id' => $userAccessId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ Error updating HOD approval', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update HOD approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get module request data for HOD approval
     */
    public function getModuleRequestData(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Get the user access request with relationships
            $userAccess = UserAccess::with(['user', 'department'])
                ->findOrFail($userAccessId);
            
            // Check permissions
            $allowedRoles = ['head_of_department', 'divisional_director', 'ict_director', 'ict_officer', 'admin', 'super_admin'];
            $canView = false;
            
            if ($userAccess->user_id === $currentUser->id) {
                $canView = true;
            } else if (array_intersect($userRoles, $allowedRoles)) {
                $canView = true;
                
                if (array_intersect($userRoles, ['head_of_department']) && !array_intersect($userRoles, ['admin', 'super_admin'])) {
                    $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
                    if (!$hodDepartment || $userAccess->department_id !== $hodDepartment->id) {
                        $canView = false;
                    }
                }
            }
            
            if (!$canView) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }
            
            // Transform data with proper module information
            $transformedData = [
                'id' => $userAccess->id,
                'shared' => [
                    'pfNumber' => $userAccess->pf_number,
                    'staffName' => $userAccess->staff_name,
                    'department' => $userAccess->department?->name ?? '',
                    'phone' => $userAccess->phone_number,
                ],
                'signature_path' => $userAccess->signature_path,
                'signature_url' => $userAccess->signature_path ? Storage::url($userAccess->signature_path) : null,
                
                // Proper module data
                'wellsoft_modules_selected' => $userAccess->wellsoft_modules_selected ?? [],
                'jeeva_modules_selected' => $userAccess->jeeva_modules_selected ?? [],
                'module_requested_for' => $userAccess->module_requested_for ?? 'use',
                
                'internet_purposes' => $userAccess->internet_purposes ?? [],
                'access_type' => $userAccess->access_type ?? 'permanent',
                'temporary_until' => $userAccess->temporary_until,
                'request_type' => $userAccess->request_type ?? [],
                'status' => $userAccess->status,
                
                // Complete approval information
                'approvals' => [
                    'hod' => [
                        'name' => $userAccess->hod_name,
                        'signature' => $userAccess->hod_signature_path,
                        'signature_url' => $userAccess->hod_signature_path ? Storage::url($userAccess->hod_signature_path) : null,
                        'date' => $userAccess->hod_approved_at,
                        'comments' => $userAccess->hod_comments,
                        'approved_by' => $userAccess->hod_approved_by_name,
                        'has_signature' => !empty($userAccess->hod_signature_path),
                        'is_approved' => !empty($userAccess->hod_approved_at)
                    ],
                    'divisionalDirector' => [
                        'name' => $userAccess->divisional_director_name,
                        'signature' => $userAccess->divisional_director_signature_path,
                        'signature_url' => $userAccess->divisional_director_signature_path ? Storage::url($userAccess->divisional_director_signature_path) : null,
                        'date' => $userAccess->divisional_approved_at,
                        'comments' => $userAccess->divisional_director_comments,
                        'has_signature' => !empty($userAccess->divisional_director_signature_path),
                        'is_approved' => !empty($userAccess->divisional_approved_at)
                    ],
                    'directorICT' => [
                        'name' => $userAccess->ict_director_name,
                        'signature' => $userAccess->ict_director_signature_path,
                        'signature_url' => $userAccess->ict_director_signature_path ? Storage::url($userAccess->ict_director_signature_path) : null,
                        'date' => $userAccess->ict_director_approved_at,
                        'comments' => $userAccess->ict_director_comments,
                        'has_signature' => !empty($userAccess->ict_director_signature_path),
                        'is_approved' => !empty($userAccess->ict_director_approved_at)
                    ]
                ],
                
                // Implementation data
                'implementation' => [
                    'headIT' => [
                        'name' => $userAccess->head_it_name,
                        'signature' => $userAccess->head_it_signature_path,
                        'signature_url' => $userAccess->head_it_signature_path ? Storage::url($userAccess->head_it_signature_path) : null,
                        'date' => $userAccess->head_it_approved_at,
                        'has_signature' => !empty($userAccess->head_it_signature_path),
                        'is_approved' => !empty($userAccess->head_it_approved_at)
                    ],
                    'ictOfficer' => [
                        'name' => $userAccess->ict_officer_name,
                        'signature' => $userAccess->ict_officer_signature_path,
                        'signature_url' => $userAccess->ict_officer_signature_path ? Storage::url($userAccess->ict_officer_signature_path) : null,
                        'date' => $userAccess->ict_officer_implemented_at,
                        'comments' => $userAccess->ict_officer_comments,
                        'implementation_comments' => $userAccess->implementation_comments,
                        'has_signature' => !empty($userAccess->ict_officer_signature_path),
                        'is_implemented' => !empty($userAccess->ict_officer_implemented_at)
                    ]
                ],
                
                'created_at' => $userAccess->created_at,
                'updated_at' => $userAccess->updated_at,
                'next_approval_step' => $userAccess->getNextApprovalNeeded(),
                'approval_progress' => $userAccess->getApprovalProgress(),
                'workflow_stage' => $userAccess->getCurrentWorkflowStage()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $transformedData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting module request data', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'user_id' => $request->user()?->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get module request data'
            ], 500);
        }
    }

    /**
     * Show a specific user access request for editing/viewing
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Get the user access request with relationships
            $userAccess = UserAccess::with(['user', 'department'])
                ->findOrFail($id);
            
            // Check permissions
            $allowedRoles = ['head_of_department', 'divisional_director', 'ict_director', 'ict_officer', 'admin', 'super_admin'];
            $canView = false;
            
            if ($userAccess->user_id === $currentUser->id) {
                $canView = true;
            } else if (array_intersect($userRoles, $allowedRoles)) {
                $canView = true;
                
                // HODs can only view requests from their department
                if (array_intersect($userRoles, ['head_of_department']) && !array_intersect($userRoles, ['admin', 'super_admin'])) {
                    $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
                    if (!$hodDepartment || $userAccess->department_id !== $hodDepartment->id) {
                        $canView = false;
                    }
                }
            }
            
            if (!$canView) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }
            
            // Transform data with proper module information
            $transformedData = [
                'id' => $userAccess->id,
                'shared' => [
                    'pfNumber' => $userAccess->pf_number,
                    'staffName' => $userAccess->staff_name,
                    'department' => $userAccess->department?->name ?? '',
                    'phone' => $userAccess->phone_number,
                ],
                'signature_path' => $userAccess->signature_path,
                'signature_url' => $userAccess->signature_path ? Storage::url($userAccess->signature_path) : null,
                
                // Module data - ensuring arrays are properly handled
                'wellsoft_modules_selected' => $userAccess->wellsoft_modules_selected ?? [],
                'jeeva_modules_selected' => $userAccess->jeeva_modules_selected ?? [],
                'module_requested_for' => $userAccess->module_requested_for ?? 'use',
                
                'internet_purposes' => $userAccess->internet_purposes ?? [],
                'access_type' => $userAccess->access_type ?? 'permanent',
                'temporary_until' => $userAccess->temporary_until,
                'request_type' => $userAccess->request_type ?? [],
                'status' => $userAccess->status,
                
                // Complete approval information
                'approvals' => [
                    'hod' => [
                        'name' => $userAccess->hod_name,
                        'signature' => $userAccess->hod_signature_path,
                        'signature_url' => $userAccess->hod_signature_path ? Storage::url($userAccess->hod_signature_path) : null,
                        'date' => $userAccess->hod_approved_at,
                        'comments' => $userAccess->hod_comments,
                        'approved_by' => $userAccess->hod_approved_by_name,
                        'has_signature' => !empty($userAccess->hod_signature_path),
                        'is_approved' => !empty($userAccess->hod_approved_at)
                    ],
                    'divisionalDirector' => [
                        'name' => $userAccess->divisional_director_name,
                        'signature' => $userAccess->divisional_director_signature_path,
                        'signature_url' => $userAccess->divisional_director_signature_path ? Storage::url($userAccess->divisional_director_signature_path) : null,
                        'date' => $userAccess->divisional_approved_at,
                        'comments' => $userAccess->divisional_director_comments,
                        'has_signature' => !empty($userAccess->divisional_director_signature_path),
                        'is_approved' => !empty($userAccess->divisional_approved_at)
                    ],
                    'directorICT' => [
                        'name' => $userAccess->ict_director_name,
                        'signature' => $userAccess->ict_director_signature_path,
                        'signature_url' => $userAccess->ict_director_signature_path ? Storage::url($userAccess->ict_director_signature_path) : null,
                        'date' => $userAccess->ict_director_approved_at,
                        'comments' => $userAccess->ict_director_comments,
                        'has_signature' => !empty($userAccess->ict_director_signature_path),
                        'is_approved' => !empty($userAccess->ict_director_approved_at)
                    ]
                ],
                
                // Implementation data
                'implementation' => [
                    'headIT' => [
                        'name' => $userAccess->head_it_name,
                        'signature' => $userAccess->head_it_signature_path,
                        'signature_url' => $userAccess->head_it_signature_path ? Storage::url($userAccess->head_it_signature_path) : null,
                        'date' => $userAccess->head_it_approved_at,
                        'has_signature' => !empty($userAccess->head_it_signature_path),
                        'is_approved' => !empty($userAccess->head_it_approved_at)
                    ],
                    'ictOfficer' => [
                        'name' => $userAccess->ict_officer_name,
                        'signature' => $userAccess->ict_officer_signature_path,
                        'signature_url' => $userAccess->ict_officer_signature_path ? Storage::url($userAccess->ict_officer_signature_path) : null,
                        'date' => $userAccess->ict_officer_implemented_at,
                        'comments' => $userAccess->ict_officer_comments,
                        'implementation_comments' => $userAccess->implementation_comments,
                        'has_signature' => !empty($userAccess->ict_officer_signature_path),
                        'is_implemented' => !empty($userAccess->ict_officer_implemented_at)
                    ]
                ],
                
                'created_at' => $userAccess->created_at,
                'updated_at' => $userAccess->updated_at,
                'next_approval_step' => $userAccess->getNextApprovalNeeded(),
                'approval_progress' => $userAccess->getApprovalProgress(),
                'workflow_stage' => $userAccess->getCurrentWorkflowStage()
            ];
            
            Log::info('✅ Successfully retrieved both service form data', [
                'id' => $id,
                'user_id' => $currentUser->id,
                'status' => $userAccess->status,
                'has_hod_signature' => !empty($userAccess->hod_signature_path),
                'jeeva_modules_count' => count($userAccess->jeeva_modules_selected ?? []),
                'wellsoft_modules_count' => count($userAccess->wellsoft_modules_selected ?? [])
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $transformedData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting both service form details', [
                'error' => $e->getMessage(),
                'id' => $id,
                'user_id' => $request->user()?->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get form details'
            ], 500);
        }
    }

    /**
     * Get pending module requests for HOD approval
     */
    public function getPendingModuleRequests(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Build base query
            $query = UserAccess::with(['user', 'department']);
            
            // Apply role-based filtering
            if (array_intersect($userRoles, ['head_of_department']) && !array_intersect($userRoles, ['admin', 'super_admin'])) {
                // HODs only see requests from their department
                $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
                if ($hodDepartment) {
                    $query->where('department_id', $hodDepartment->id);
                } else {
                    // If HOD has no department assigned, return empty result
                    $query->whereRaw('1 = 0');
                }
                
                // HODs see requests pending their approval
                $query->where('status', 'pending');
            } else if (array_intersect($userRoles, ['divisional_director'])) {
                // Divisional directors see HOD-approved requests
                $query->where('status', 'hod_approved');
            } else if (array_intersect($userRoles, ['ict_director'])) {
                // ICT directors see divisional-approved requests
                $query->where('status', 'divisional_approved');
            } else if (array_intersect($userRoles, ['ict_officer'])) {
                // ICT officers see director-approved requests
                $query->where('status', 'ict_director_approved');
            } else if (array_intersect($userRoles, ['admin', 'super_admin'])) {
                // Admins see all requests
                // No additional filtering
            } else {
                // Other roles see only their own requests
                $query->where('user_id', $currentUser->id);
            }
            
            $requests = $query->orderBy('created_at', 'desc')->get();
            
            $transformedRequests = $requests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'pf_number' => $request->pf_number,
                    'staff_name' => $request->staff_name,
                    'department' => $request->department?->name ?? '',
                    'request_type' => $request->request_type ?? [],
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                    'wellsoft_modules_count' => count($request->wellsoft_modules_selected ?? []),
                    'jeeva_modules_count' => count($request->jeeva_modules_selected ?? []),
                    'has_hod_approval' => !empty($request->hod_approved_at),
                    'has_divisional_approval' => !empty($request->divisional_approved_at),
                    'has_ict_director_approval' => !empty($request->ict_director_approved_at),
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $transformedRequests,
                'total' => $transformedRequests->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting pending module requests', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get pending module requests'
            ], 500);
        }
    }

    /**
     * Divisional Director approval for a user access request
     */
    public function approveDivisionalDirector(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Check if user is Divisional Director
            if (!array_intersect($userRoles, ['divisional_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Divisional Director can perform this action.'
                ], 403);
            }
            
            Log::info('🔍 DIVISIONAL DIRECTOR APPROVAL START', [
                'user_access_id' => $userAccessId,
                'divisional_director_user_id' => $currentUser->id,
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'has_files' => $request->hasFile('divisional_director_signature'),
                'all_input_keys' => array_keys($request->all())
            ]);
            
            // Validation
            $validated = $request->validate([
                'divisional_director_name' => 'required|string|max:255',
                'divisional_director_signature' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'approved_date' => 'required|date',
                'comments' => 'nullable|string|max:1000',
            ], [
                'divisional_director_name.required' => 'Divisional Director name is required',
                'divisional_director_signature.required' => 'Divisional Director signature file is required',
                'divisional_director_signature.mimes' => 'Signature must be in JPEG, JPG, PNG, or PDF format',
                'divisional_director_signature.max' => 'Signature file must not exceed 2MB',
                'approved_date.required' => 'Approval date is required',
                'approved_date.date' => 'Please provide a valid approval date',
            ]);
            
            Log::info('✅ Validation passed', [
                'validated_data' => Arr::except($validated, ['divisional_director_signature']),
                'has_signature_file' => isset($validated['divisional_director_signature'])
            ]);
            
            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Verify request is in correct status (must be HOD approved)
            if ($userAccess->status !== 'hod_approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be HOD approved before Divisional Director can approve it.'
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Handle signature upload
            $divisionalSignaturePath = null;
            if ($request->hasFile('divisional_director_signature')) {
                try {
                    $signatureFile = $request->file('divisional_director_signature');
                    
                    if (!$signatureFile->isValid()) {
                        throw new \Exception('Uploaded signature file is invalid');
                    }
                    
                    $signatureDir = 'signatures/divisional_director';
                    if (!Storage::disk('public')->exists($signatureDir)) {
                        Storage::disk('public')->makeDirectory($signatureDir);
                    }
                    
                    $filename = 'divisional_director_signature_' . $userAccess->pf_number . '_' . time() . '.' . $signatureFile->getClientOriginalExtension();
                    $divisionalSignaturePath = $signatureFile->storeAs($signatureDir, $filename, 'public');
                    
                    if (!Storage::disk('public')->exists($divisionalSignaturePath)) {
                        throw new \Exception('Failed to store signature file');
                    }
                    
                    Log::info('✅ Divisional Director signature uploaded successfully', [
                        'original_name' => $signatureFile->getClientOriginalName(),
                        'stored_path' => $divisionalSignaturePath,
                        'file_size' => $signatureFile->getSize(),
                        'mime_type' => $signatureFile->getMimeType()
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('❌ Divisional Director signature upload failed', [
                        'error' => $e->getMessage(),
                        'file_info' => [
                            'name' => $request->file('divisional_director_signature')->getClientOriginalName(),
                            'size' => $request->file('divisional_director_signature')->getSize(),
                            'mime' => $request->file('divisional_director_signature')->getMimeType()
                        ]
                    ]);
                    
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload Divisional Director signature: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Update the user access record
            $updateData = [
                'divisional_director_name' => $validated['divisional_director_name'],
                'divisional_director_signature_path' => $divisionalSignaturePath,
                'divisional_approved_at' => $validated['approved_date'],
                'divisional_director_comments' => $validated['comments'] ?? null,
                'status' => 'divisional_approved'
            ];
            
            Log::info('🔄 Updating user access record for divisional approval', [
                'user_access_id' => $userAccess->id,
                'update_data' => Arr::except($updateData, ['divisional_director_signature_path']),
                'has_signature_path' => !empty($updateData['divisional_director_signature_path'])
            ]);
            
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            Log::info('✅ User access record updated successfully for divisional approval', [
                'user_access_id' => $userAccess->id,
                'divisional_director_name' => $userAccess->divisional_director_name,
                'divisional_director_signature_path' => $userAccess->divisional_director_signature_path,
                'divisional_approved_at' => $userAccess->divisional_approved_at,
                'status' => $userAccess->status
            ]);
            
            // Create notification for HOD about divisional director approval
            try {
                // Find the HOD who approved this request
                $hodDepartment = Department::where('id', $userAccess->department_id)->first();
                if ($hodDepartment && $hodDepartment->hod_user_id) {
                    $notificationTitle = 'Request Approved by Divisional Director';
                    $notificationMessage = "The access request for {$userAccess->staff_name} (PF: {$userAccess->pf_number}) has been approved by the Divisional Director and is proceeding to the next stage.";
                    
                    NotificationController::createNotification(
                        $hodDepartment->hod_user_id,  // recipient (HOD)
                        $currentUser->id,             // sender (Divisional Director)
                        $userAccess->id,              // access request ID
                        'divisional_approval',        // notification type
                        $notificationTitle,
                        $notificationMessage,
                        [
                            'staff_name' => $userAccess->staff_name,
                            'pf_number' => $userAccess->pf_number,
                            'department' => $userAccess->department->name ?? 'Unknown',
                            'approved_by' => $validated['divisional_director_name'],
                            'approved_at' => $validated['approved_date'],
                            'status' => 'approved'
                        ]
                    );
                    
                    Log::info('✅ Notification created for HOD about divisional approval', [
                        'hod_user_id' => $hodDepartment->hod_user_id,
                        'access_request_id' => $userAccess->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ Failed to create notification for divisional approval', [
                    'error' => $e->getMessage(),
                    'user_access_id' => $userAccess->id
                ]);
                // Don't fail the main operation if notification fails
            }
            
            DB::commit();
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'Divisional Director approval updated successfully.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->status,
                    'divisional_approval' => [
                        'name' => $userAccess->divisional_director_name,
                        'approved_at' => $userAccess->divisional_approved_at->format('Y-m-d H:i:s'),
                        'approved_at_formatted' => $userAccess->divisional_approved_at->format('m/d/Y'),
                        'comments' => $userAccess->divisional_director_comments,
                        'signature_url' => Storage::url($userAccess->divisional_director_signature_path),
                        'signature_path' => $userAccess->divisional_director_signature_path
                    ],
                    'next_step' => 'ict_director_approval'
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Validation failed for Divisional Director approval', [
                'errors' => $e->errors(),
                'user_access_id' => $userAccessId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ Error updating Divisional Director approval', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Divisional Director approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Divisional Director rejection for a user access request
     */
    public function rejectDivisionalDirector(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Check if user is Divisional Director
            if (!array_intersect($userRoles, ['divisional_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Divisional Director can perform this action.'
                ], 403);
            }
            
            Log::info('🔍 DIVISIONAL DIRECTOR REJECTION START', [
                'user_access_id' => $userAccessId,
                'divisional_director_user_id' => $currentUser->id,
                'request_method' => $request->method(),
            ]);
            
            // Validation
            $validated = $request->validate([
                'divisional_director_name' => 'required|string|max:255',
                'rejection_reason' => 'required|string|max:1000',
                'rejection_date' => 'required|date',
            ], [
                'divisional_director_name.required' => 'Divisional Director name is required',
                'rejection_reason.required' => 'Rejection reason is required',
                'rejection_date.required' => 'Rejection date is required',
                'rejection_date.date' => 'Please provide a valid rejection date',
            ]);
            
            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Verify request is in correct status (must be HOD approved)
            if ($userAccess->status !== 'hod_approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be HOD approved before Divisional Director can reject it.'
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Update the user access record
            $updateData = [
                'divisional_director_name' => $validated['divisional_director_name'],
                'divisional_director_comments' => $validated['rejection_reason'],
                'divisional_approved_at' => $validated['rejection_date'],
                'status' => 'divisional_rejected'
            ];
            
            Log::info('🔄 Updating user access record for divisional rejection', [
                'user_access_id' => $userAccess->id,
                'update_data' => $updateData
            ]);
            
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            Log::info('✅ User access record updated successfully for divisional rejection', [
                'user_access_id' => $userAccess->id,
                'divisional_director_name' => $userAccess->divisional_director_name,
                'status' => $userAccess->status
            ]);
            
            // Create notification for HOD about divisional director rejection
            try {
                // Find the HOD who approved this request
                $hodDepartment = Department::where('id', $userAccess->department_id)->first();
                if ($hodDepartment && $hodDepartment->hod_user_id) {
                    $notificationTitle = 'Request Rejected by Divisional Director';
                    $notificationMessage = "The access request for {$userAccess->staff_name} (PF: {$userAccess->pf_number}) has been rejected by the Divisional Director. Reason: {$validated['rejection_reason']}";
                    
                    NotificationController::createNotification(
                        $hodDepartment->hod_user_id,  // recipient (HOD)
                        $currentUser->id,             // sender (Divisional Director)
                        $userAccess->id,              // access request ID
                        'divisional_rejection',       // notification type
                        $notificationTitle,
                        $notificationMessage,
                        [
                            'staff_name' => $userAccess->staff_name,
                            'pf_number' => $userAccess->pf_number,
                            'department' => $userAccess->department->name ?? 'Unknown',
                            'rejected_by' => $validated['divisional_director_name'],
                            'rejected_at' => $validated['rejection_date'],
                            'rejection_reason' => $validated['rejection_reason'],
                            'status' => 'rejected'
                        ]
                    );
                    
                    Log::info('✅ Notification created for HOD about divisional rejection', [
                        'hod_user_id' => $hodDepartment->hod_user_id,
                        'access_request_id' => $userAccess->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ Failed to create notification for divisional rejection', [
                    'error' => $e->getMessage(),
                    'user_access_id' => $userAccess->id
                ]);
                // Don't fail the main operation if notification fails
            }
            
            DB::commit();
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'Request rejected by Divisional Director.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->status,
                    'divisional_rejection' => [
                        'name' => $userAccess->divisional_director_name,
                        'rejected_at' => $userAccess->divisional_approved_at->format('Y-m-d H:i:s'),
                        'rejected_at_formatted' => $userAccess->divisional_approved_at->format('m/d/Y'),
                        'reason' => $userAccess->divisional_director_comments
                    ]
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Validation failed for Divisional Director rejection', [
                'errors' => $e->errors(),
                'user_access_id' => $userAccessId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ Error updating Divisional Director rejection', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request: ' . $e->getMessage()
            ], 500);
        }
    }
}
