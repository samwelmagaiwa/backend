<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\Department;
use App\Models\User;
use App\Http\Controllers\NotificationController;
use App\Services\SmsModule;
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
     * Helper method to safely count JSON array fields
     */
    private function getJsonArrayCount($jsonField)
    {
        if (is_null($jsonField)) {
            return 0;
        }
        
        // If it's already an array, count it
        if (is_array($jsonField)) {
            return count($jsonField);
        }
        
        // If it's a string, try to decode as JSON
        if (is_string($jsonField)) {
            try {
                $decoded = json_decode($jsonField, true);
                if (is_array($decoded)) {
                    return count($decoded);
                }
            } catch (Exception $e) {
                // If JSON decode fails, return 0
                return 0;
            }
        }
        
        return 0;
    }
    
    /**
     * Helper method to format signature status with visual indicators
     */
    private function formatSignatureStatus($signaturePath, $approvalDate = null, $approverName = null)
    {
        if (!empty($signaturePath)) {
            return [
                'signature_status' => 'Signed',
                'signature_status_color' => 'green',
                'has_signature_file' => true,
                'signature_display' => 'Signed'
            ];
        }
        
        return [
            'signature_status' => 'No signature',
            'signature_status_color' => 'red',
            'has_signature_file' => false,
            'signature_display' => 'No signature'
        ];
    }
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
                'hod_status' => 'approved' // Set the new hod_status column
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
                'calculated_status' => $userAccess->getCalculatedOverallStatus()
            ]);
            
            DB::commit();
            
            // Send SMS notifications to divisional director
            try {
                // Get next approver (Divisional Director for this specific department)
                // IMPORTANT: Divisional directors can oversee multiple departments,
                // so we must get the director assigned to THIS request's department
                $department = $userAccess->department;
                $nextApprover = $department && $department->divisional_director_id 
                    ? User::find($department->divisional_director_id)
                    : User::whereHas('roles', fn($q) => $q->where('name', 'divisional_director'))->first();
                
                if ($nextApprover) {
                    // Send SMS notifications
                    $sms = app(SmsModule::class);
                    $sms->notifyRequestApproved(
                        $userAccess,
                        $currentUser,
                        'hod',
                        $nextApprover
                    );
                    
                    Log::info('HOD SMS notifications sent', [
                        'request_id' => $userAccess->id,
                        'next_approver' => $nextApprover->name
                    ]);
                } else {
                    Log::warning('No divisional director found for SMS notification', [
                        'request_id' => $userAccess->id,
                        'department_id' => $userAccess->department_id
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('HOD SMS notification failed', [
                    'request_id' => $userAccess->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the approval if SMS fails
            }
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'HOD approval updated successfully.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
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
            $allowedRoles = ['head_of_department', 'divisional_director', 'ict_director', 'head_of_it', 'ict_officer', 'admin', 'super_admin'];
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
                // Removed signature_url to reduce I/O operations - frontend can construct URL
                
                // Proper module data - ensure arrays are properly formatted
                'wellsoft_modules' => $userAccess->wellsoft_modules ?? [],
                'wellsoft_modules_selected' => is_array($userAccess->wellsoft_modules_selected) 
                    ? $userAccess->wellsoft_modules_selected 
                    : ($userAccess->wellsoft_modules_selected ? json_decode($userAccess->wellsoft_modules_selected, true) ?? [] : []),
                'jeeva_modules' => $userAccess->jeeva_modules ?? [],
                'jeeva_modules_selected' => is_array($userAccess->jeeva_modules_selected) 
                    ? $userAccess->jeeva_modules_selected 
                    : ($userAccess->jeeva_modules_selected ? json_decode($userAccess->jeeva_modules_selected, true) ?? [] : []),
                'module_requested_for' => $userAccess->module_requested_for ?? 'use',
                
                'internet_purposes' => is_array($userAccess->internet_purposes) 
                    ? $userAccess->internet_purposes 
                    : ($userAccess->internet_purposes ? json_decode($userAccess->internet_purposes, true) ?? [] : []),
                'access_type' => $userAccess->access_type ?? 'permanent',
                'temporary_until' => $userAccess->temporary_until,
                'request_type' => $userAccess->request_type ?? [],
                'status' => $userAccess->getCalculatedOverallStatus(),
                
                // Direct database status fields for easier access
                'hod_status' => $userAccess->hod_status,
                'divisional_status' => $userAccess->divisional_status,
                'ict_director_status' => $userAccess->ict_director_status,
                'head_it_status' => $userAccess->head_it_status,
                'ict_officer_status' => $userAccess->ict_officer_status,
                
                // Flattened comment fields for easier access
                'hod_comments' => $userAccess->hod_comments,
                'divisional_comments' => $userAccess->divisional_director_comments,
                'ict_director_comments' => $userAccess->ict_director_comments,
                'head_it_comments' => $userAccess->head_it_comments,
                'ict_officer_comments' => $userAccess->ict_officer_comments,
                
                // Flattened approval date fields for easier access
                'hod_approved_at' => $userAccess->hod_approved_at,
                'divisional_approved_at' => $userAccess->divisional_approved_at,
                'ict_director_approved_at' => $userAccess->ict_director_approved_at,
                'dict_approved_at' => $userAccess->dict_approved_at,
                'head_it_approved_at' => $userAccess->head_it_approved_at,
                'ict_officer_implemented_at' => $userAccess->ict_officer_implemented_at,
                
                // Complete approval information with signature status indicators
                // Removed signature_url from all approvals to reduce Storage::url() I/O calls
                'approvals' => [
                    'hod' => array_merge([
                        'name' => $userAccess->hod_name,
                        'signature' => $userAccess->hod_signature_path,
                        'date' => $userAccess->hod_approved_at,
                        'comments' => $userAccess->hod_comments,
                        'approved_by' => $userAccess->hod_approved_by_name,
                        'has_signature' => !empty($userAccess->hod_signature_path),
                        'is_approved' => !empty($userAccess->hod_approved_at)
                    ], $this->formatSignatureStatus($userAccess->hod_signature_path, $userAccess->hod_approved_at, $userAccess->hod_name)),
                    'divisionalDirector' => array_merge([
                        'name' => $userAccess->divisional_director_name,
                        'signature' => $userAccess->divisional_director_signature_path,
                        'date' => $userAccess->divisional_approved_at,
                        'comments' => $userAccess->divisional_director_comments,
                        'has_signature' => !empty($userAccess->divisional_director_signature_path),
                        'is_approved' => !empty($userAccess->divisional_approved_at)
                    ], $this->formatSignatureStatus($userAccess->divisional_director_signature_path, $userAccess->divisional_approved_at, $userAccess->divisional_director_name)),
                    'directorICT' => array_merge([
                        'name' => $userAccess->ict_director_name,
                        'signature' => $userAccess->ict_director_signature_path,
                        'date' => $userAccess->ict_director_approved_at,
                        'comments' => $userAccess->ict_director_comments,
                        'has_signature' => !empty($userAccess->ict_director_signature_path),
                        'is_approved' => !empty($userAccess->ict_director_approved_at)
                    ], $this->formatSignatureStatus($userAccess->ict_director_signature_path, $userAccess->ict_director_approved_at, $userAccess->ict_director_name))
                ],
                
                // Implementation data with signature status indicators
                'implementation' => [
                    'headIT' => array_merge([
                        'name' => $userAccess->head_it_name,
                        'signature' => $userAccess->head_it_signature_path,
                        'date' => $userAccess->head_it_approved_at,
                        'comments' => $userAccess->head_it_comments,
                        'has_signature' => !empty($userAccess->head_it_signature_path),
                        'is_approved' => !empty($userAccess->head_it_approved_at)
                    ], $this->formatSignatureStatus($userAccess->head_it_signature_path, $userAccess->head_it_approved_at, $userAccess->head_it_name)),
                    'ictOfficer' => array_merge([
                        'name' => $userAccess->ict_officer_name,
                        'signature' => $userAccess->ict_officer_signature_path,
                        'date' => $userAccess->ict_officer_implemented_at,
                        'comments' => $userAccess->ict_officer_comments,
                        'implementation_comments' => $userAccess->implementation_comments,
                        'has_signature' => !empty($userAccess->ict_officer_signature_path),
                        'is_implemented' => !empty($userAccess->ict_officer_implemented_at)
                    ], $this->formatSignatureStatus($userAccess->ict_officer_signature_path, $userAccess->ict_officer_implemented_at, $userAccess->ict_officer_name))
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
            
            // Get the user access request with optimized relationship loading
            // Only load relationships that are actually used
            $userAccess = UserAccess::with(['department:id,name'])
                ->findOrFail($id);
            
            // Check permissions
            $allowedRoles = ['head_of_department', 'divisional_director', 'ict_director', 'head_of_it', 'ict_officer', 'admin', 'super_admin'];
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
                // Removed signature_url to reduce I/O operations - frontend can construct URL
                
                // Module data - ensuring arrays are properly handled and JSON strings are decoded
                'wellsoft_modules' => $userAccess->wellsoft_modules ?? [],
                'wellsoft_modules_selected' => is_array($userAccess->wellsoft_modules_selected) 
                    ? $userAccess->wellsoft_modules_selected 
                    : ($userAccess->wellsoft_modules_selected ? json_decode($userAccess->wellsoft_modules_selected, true) ?? [] : []),
                'jeeva_modules' => $userAccess->jeeva_modules ?? [],
                'jeeva_modules_selected' => is_array($userAccess->jeeva_modules_selected) 
                    ? $userAccess->jeeva_modules_selected 
                    : ($userAccess->jeeva_modules_selected ? json_decode($userAccess->jeeva_modules_selected, true) ?? [] : []),
                'module_requested_for' => $userAccess->module_requested_for ?? 'use',
                
                'internet_purposes' => is_array($userAccess->internet_purposes) 
                    ? $userAccess->internet_purposes 
                    : ($userAccess->internet_purposes ? json_decode($userAccess->internet_purposes, true) ?? [] : []),
                'access_type' => $userAccess->access_type ?? 'permanent',
                'temporary_until' => $userAccess->temporary_until,
                'request_type' => $userAccess->request_type ?? [],
                'status' => $userAccess->getCalculatedOverallStatus(),
                
                // Direct database status fields for easier access
                'hod_status' => $userAccess->hod_status,
                'divisional_status' => $userAccess->divisional_status,
                'ict_director_status' => $userAccess->ict_director_status,
                'head_it_status' => $userAccess->head_it_status,
                'ict_officer_status' => $userAccess->ict_officer_status,
                
                // Flattened comment fields for easier access
                'hod_comments' => $userAccess->hod_comments,
                'divisional_comments' => $userAccess->divisional_director_comments,
                'ict_director_comments' => $userAccess->ict_director_comments,
                'head_it_comments' => $userAccess->head_it_comments,
                'ict_officer_comments' => $userAccess->ict_officer_comments,
                
                // Flattened approval date fields for easier access
                'hod_approved_at' => $userAccess->hod_approved_at,
                'divisional_approved_at' => $userAccess->divisional_approved_at,
                'ict_director_approved_at' => $userAccess->ict_director_approved_at,
                'dict_approved_at' => $userAccess->dict_approved_at,
                'head_it_approved_at' => $userAccess->head_it_approved_at,
                'ict_officer_implemented_at' => $userAccess->ict_officer_implemented_at,
                
                // Complete approval information with signature status indicators
                // Removed signature_url from all approvals to reduce Storage::url() I/O calls
                'approvals' => [
                    'hod' => array_merge([
                        'name' => $userAccess->hod_name,
                        'signature' => $userAccess->hod_signature_path,
                        'date' => $userAccess->hod_approved_at,
                        'comments' => $userAccess->hod_comments,
                        'approved_by' => $userAccess->hod_approved_by_name,
                        'has_signature' => !empty($userAccess->hod_signature_path),
                        'is_approved' => !empty($userAccess->hod_approved_at)
                    ], $this->formatSignatureStatus($userAccess->hod_signature_path, $userAccess->hod_approved_at, $userAccess->hod_name)),
                    'divisionalDirector' => array_merge([
                        'name' => $userAccess->divisional_director_name,
                        'signature' => $userAccess->divisional_director_signature_path,
                        'date' => $userAccess->divisional_approved_at,
                        'comments' => $userAccess->divisional_director_comments,
                        'has_signature' => !empty($userAccess->divisional_director_signature_path),
                        'is_approved' => !empty($userAccess->divisional_approved_at)
                    ], $this->formatSignatureStatus($userAccess->divisional_director_signature_path, $userAccess->divisional_approved_at, $userAccess->divisional_director_name)),
                    'directorICT' => array_merge([
                        'name' => $userAccess->ict_director_name,
                        'signature' => $userAccess->ict_director_signature_path,
                        'date' => $userAccess->ict_director_approved_at,
                        'comments' => $userAccess->ict_director_comments,
                        'has_signature' => !empty($userAccess->ict_director_signature_path),
                        'is_approved' => !empty($userAccess->ict_director_approved_at)
                    ], $this->formatSignatureStatus($userAccess->ict_director_signature_path, $userAccess->ict_director_approved_at, $userAccess->ict_director_name))
                ],
                
                // Implementation data with signature status indicators
                'implementation' => [
                    'headIT' => array_merge([
                        'name' => $userAccess->head_it_name,
                        'signature' => $userAccess->head_it_signature_path,
                        'date' => $userAccess->head_it_approved_at,
                        'comments' => $userAccess->head_it_comments,
                        'has_signature' => !empty($userAccess->head_it_signature_path),
                        'is_approved' => !empty($userAccess->head_it_approved_at)
                    ], $this->formatSignatureStatus($userAccess->head_it_signature_path, $userAccess->head_it_approved_at, $userAccess->head_it_name)),
                    'ictOfficer' => array_merge([
                        'name' => $userAccess->ict_officer_name,
                        'signature' => $userAccess->ict_officer_signature_path,
                        'date' => $userAccess->ict_officer_implemented_at,
                        'comments' => $userAccess->ict_officer_comments,
                        'implementation_comments' => $userAccess->implementation_comments,
                        'has_signature' => !empty($userAccess->ict_officer_signature_path),
                        'is_implemented' => !empty($userAccess->ict_officer_implemented_at)
                    ], $this->formatSignatureStatus($userAccess->ict_officer_signature_path, $userAccess->ict_officer_implemented_at, $userAccess->ict_officer_name))
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
                'calculated_status' => $userAccess->getCalculatedOverallStatus(),
                'has_hod_signature' => !empty($userAccess->hod_signature_path),
                'jeeva_modules_selected' => $userAccess->jeeva_modules_selected,
                'jeeva_modules_selected_type' => gettype($userAccess->jeeva_modules_selected),
                'jeeva_modules_count' => $this->getJsonArrayCount($userAccess->jeeva_modules_selected),
                'wellsoft_modules_selected' => $userAccess->wellsoft_modules_selected,
                'wellsoft_modules_selected_type' => gettype($userAccess->wellsoft_modules_selected),
                'wellsoft_modules_count' => $this->getJsonArrayCount($userAccess->wellsoft_modules_selected),
                'module_requested_for' => $userAccess->module_requested_for
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
            
            // Apply role-based filtering using role-specific status columns
            if (array_intersect($userRoles, ['head_of_department']) && !array_intersect($userRoles, ['admin', 'super_admin'])) {
                // HODs only see requests from their department
                $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
                if ($hodDepartment) {
                    $query->where('department_id', $hodDepartment->id);
                } else {
                    // If HOD has no department assigned, return empty result
                    $query->whereRaw('1 = 0');
                }
                
                // HODs see requests pending their approval (hod_status is null or 'pending')
                $query->where(function($q) {
                    $q->whereNull('hod_status')
                      ->orWhere('hod_status', 'pending');
                });
            } else if (array_intersect($userRoles, ['divisional_director'])) {
                // Divisional directors see HOD-approved requests
                $query->where('hod_status', 'approved')
                      ->where(function($q) {
                          $q->whereNull('divisional_status')
                            ->orWhere('divisional_status', 'pending');
                      });
            } else if (array_intersect($userRoles, ['ict_director'])) {
                // ICT directors see divisional-approved requests
                $query->where('divisional_status', 'approved')
                      ->where(function($q) {
                          $q->whereNull('ict_director_status')
                            ->orWhere('ict_director_status', 'pending');
                      });
            } else if (array_intersect($userRoles, ['head_of_it'])) {
                // Head of IT sees ICT director-approved requests
                $query->where('ict_director_status', 'approved')
                      ->where(function($q) {
                          $q->whereNull('head_it_status')
                            ->orWhere('head_it_status', 'pending');
                      });
            } else if (array_intersect($userRoles, ['ict_officer'])) {
                // ICT officers see head IT-approved requests
                $query->where('head_it_status', 'approved')
                      ->where(function($q) {
                          $q->whereNull('ict_officer_status')
                            ->orWhere('ict_officer_status', 'pending');
                      });
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
                    'status' => $request->getCalculatedOverallStatus(),
                    'created_at' => $request->created_at,
                    'wellsoft_modules_count' => $this->getJsonArrayCount($request->wellsoft_modules_selected),
                    'jeeva_modules_count' => $this->getJsonArrayCount($request->jeeva_modules_selected),
                    'has_hod_approval' => !empty($request->hod_approved_at),
                    'has_divisional_approval' => !empty($request->divisional_approved_at),
                    'has_ict_director_approval' => !empty($request->ict_director_approved_at),
                    // Add role-specific status information
                    'hod_status' => $request->hod_status ?? 'pending',
                    'divisional_status' => $request->divisional_status ?? 'pending',
                    'ict_director_status' => $request->ict_director_status ?? 'pending',
                    'head_it_status' => $request->head_it_status ?? 'pending',
                    'ict_officer_status' => $request->ict_officer_status ?? 'pending',
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
            if ($userAccess->hod_status !== 'approved') {
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
                'divisional_status' => 'approved' // Set the new divisional_status column
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
                'calculated_status' => $userAccess->getCalculatedOverallStatus()
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
            
            // Send SMS notifications
            try {
                // Get next approver (ICT Director)
                $nextApprover = User::whereHas('roles', fn($q) => 
                    $q->where('name', 'ict_director')
                )->first();
                
                if ($nextApprover) {
                    $sms = app(SmsModule::class);
                    $sms->notifyRequestApproved(
                        $userAccess,
                        $currentUser,
                        'divisional',
                        $nextApprover
                    );
                    
                    Log::info('✅ SMS notifications sent for divisional approval', [
                        'request_id' => $userAccess->id,
                        'next_approver' => $nextApprover->name
                    ]);
                } else {
                    Log::warning('⚠️ ICT Director not found for SMS notification', [
                        'request_id' => $userAccess->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('❌ Failed to send SMS notifications for divisional approval', [
                    'request_id' => $userAccess->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the main operation if SMS fails
            }
            
            DB::commit();
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'Divisional Director approval updated successfully.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
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
            if ($userAccess->hod_status !== 'approved') {
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
                'divisional_status' => 'rejected' // Set the new divisional_status column
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
                'calculated_status' => $userAccess->getCalculatedOverallStatus()
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
                    'status' => $userAccess->getCalculatedOverallStatus(),
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

    /**
     * ICT Director approval for a user access request
     */
    public function approveIctDirector(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Check if user is ICT Director
            if (!array_intersect($userRoles, ['ict_director', 'dict'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only ICT Director can perform this action.'
                ], 403);
            }
            
            Log::info('🔍 ICT DIRECTOR APPROVAL START', [
                'user_access_id' => $userAccessId,
                'ict_director_user_id' => $currentUser->id,
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'has_files' => $request->hasFile('ict_director_signature'),
                'all_input_keys' => array_keys($request->all())
            ]);
            
            // Validation
            $validated = $request->validate([
                'ict_director_name' => 'required|string|max:255',
                'ict_director_signature' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'approved_date' => 'required|date',
                'comments' => 'nullable|string|max:1000',
            ], [
                'ict_director_name.required' => 'ICT Director name is required',
                'ict_director_signature.required' => 'ICT Director signature file is required',
                'ict_director_signature.mimes' => 'Signature must be in JPEG, JPG, PNG, or PDF format',
                'ict_director_signature.max' => 'Signature file must not exceed 2MB',
                'approved_date.required' => 'Approval date is required',
                'approved_date.date' => 'Please provide a valid approval date',
            ]);
            
            Log::info('✅ Validation passed', [
                'validated_data' => Arr::except($validated, ['ict_director_signature']),
                'has_signature_file' => isset($validated['ict_director_signature'])
            ]);
            
            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Verify request is in correct status (must be Divisional approved)
            if ($userAccess->divisional_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be Divisional Director approved before ICT Director can approve it.'
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Handle signature upload
            $ictDirectorSignaturePath = null;
            if ($request->hasFile('ict_director_signature')) {
                try {
                    $signatureFile = $request->file('ict_director_signature');
                    
                    if (!$signatureFile->isValid()) {
                        throw new \Exception('Uploaded signature file is invalid');
                    }
                    
                    $signatureDir = 'signatures/ict_director';
                    if (!Storage::disk('public')->exists($signatureDir)) {
                        Storage::disk('public')->makeDirectory($signatureDir);
                    }
                    
                    $filename = 'ict_director_signature_' . $userAccess->pf_number . '_' . time() . '.' . $signatureFile->getClientOriginalExtension();
                    $ictDirectorSignaturePath = $signatureFile->storeAs($signatureDir, $filename, 'public');
                    
                    if (!Storage::disk('public')->exists($ictDirectorSignaturePath)) {
                        throw new \Exception('Failed to store signature file');
                    }
                    
                    Log::info('✅ ICT Director signature uploaded successfully', [
                        'original_name' => $signatureFile->getClientOriginalName(),
                        'stored_path' => $ictDirectorSignaturePath,
                        'file_size' => $signatureFile->getSize(),
                        'mime_type' => $signatureFile->getMimeType()
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('❌ ICT Director signature upload failed', [
                        'error' => $e->getMessage(),
                        'file_info' => [
                            'name' => $request->file('ict_director_signature')->getClientOriginalName(),
                            'size' => $request->file('ict_director_signature')->getSize(),
                            'mime' => $request->file('ict_director_signature')->getMimeType()
                        ]
                    ]);
                    
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload ICT Director signature: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Update the user access record
            $updateData = [
                'ict_director_name' => $validated['ict_director_name'],
                'ict_director_signature_path' => $ictDirectorSignaturePath,
                'ict_director_approved_at' => $validated['approved_date'],
                'ict_director_comments' => $validated['comments'] ?? null,
                'ict_director_status' => 'approved' // Set the new ict_director_status column
            ];
            
            Log::info('🔄 Updating user access record for ICT Director approval', [
                'user_access_id' => $userAccess->id,
                'update_data' => Arr::except($updateData, ['ict_director_signature_path']),
                'has_signature_path' => !empty($updateData['ict_director_signature_path'])
            ]);
            
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            Log::info('✅ User access record updated successfully for ICT Director approval', [
                'user_access_id' => $userAccess->id,
                'ict_director_name' => $userAccess->ict_director_name,
                'ict_director_signature_path' => $userAccess->ict_director_signature_path,
                'ict_director_approved_at' => $userAccess->ict_director_approved_at,
                'calculated_status' => $userAccess->getCalculatedOverallStatus()
            ]);
            
            // Create notification for Divisional Director about ICT director approval
            try {
                // Find the Divisional Director who approved this request - for now we'll skip this
                // as we don't have a direct relationship to find the divisional director
                
                Log::info('✅ ICT Director approval notification skipped (no direct divisional director relationship)');
            } catch (\Exception $e) {
                Log::error('❌ Failed to create notification for ICT Director approval', [
                    'error' => $e->getMessage(),
                    'user_access_id' => $userAccess->id
                ]);
                // Don't fail the main operation if notification fails
            }
            
            // Send SMS notifications
            try {
                // Get next approver (Head of IT)
                $nextApprover = User::whereHas('roles', fn($q) => 
                    $q->where('name', 'head_of_it')
                )->first();
                
                if ($nextApprover) {
                    $sms = app(SmsModule::class);
                    $sms->notifyRequestApproved(
                        $userAccess,
                        $currentUser,
                        'ict_director',
                        $nextApprover
                    );
                    
                    Log::info('✅ SMS notifications sent for ICT Director approval', [
                        'request_id' => $userAccess->id,
                        'next_approver' => $nextApprover->name
                    ]);
                } else {
                    Log::warning('⚠️ Head of IT not found for SMS notification', [
                        'request_id' => $userAccess->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('❌ Failed to send SMS notifications for ICT Director approval', [
                    'request_id' => $userAccess->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the main operation if SMS fails
            }
            
            DB::commit();
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'ICT Director approval updated successfully.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
                    'ict_director_approval' => [
                        'name' => $userAccess->ict_director_name,
                        'approved_at' => $userAccess->ict_director_approved_at->format('Y-m-d H:i:s'),
                        'approved_at_formatted' => $userAccess->ict_director_approved_at->format('m/d/Y'),
                        'comments' => $userAccess->ict_director_comments,
                        'signature_url' => Storage::url($userAccess->ict_director_signature_path),
                        'signature_path' => $userAccess->ict_director_signature_path
                    ],
                    'next_step' => 'head_it_approval'
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Validation failed for ICT Director approval', [
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
            
            Log::error('❌ Error updating ICT Director approval', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ICT Director approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ICT Director rejection for a user access request
     */
    public function rejectIctDirector(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Check if user is ICT Director
            if (!array_intersect($userRoles, ['ict_director', 'dict'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only ICT Director can perform this action.'
                ], 403);
            }
            
            Log::info('🔍 ICT DIRECTOR REJECTION START', [
                'user_access_id' => $userAccessId,
                'ict_director_user_id' => $currentUser->id,
                'request_method' => $request->method(),
            ]);
            
            // Validation
            $validated = $request->validate([
                'ict_director_name' => 'required|string|max:255',
                'rejection_reason' => 'required|string|max:1000',
                'rejection_date' => 'required|date',
            ], [
                'ict_director_name.required' => 'ICT Director name is required',
                'rejection_reason.required' => 'Rejection reason is required',
                'rejection_date.required' => 'Rejection date is required',
                'rejection_date.date' => 'Please provide a valid rejection date',
            ]);
            
            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Verify request is in correct status (must be Divisional approved)
            if ($userAccess->divisional_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be Divisional Director approved before ICT Director can reject it.'
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Update the user access record
            $updateData = [
                'ict_director_name' => $validated['ict_director_name'],
                'ict_director_comments' => $validated['rejection_reason'],
                'ict_director_approved_at' => $validated['rejection_date'],
                'ict_director_status' => 'rejected' // Set the new ict_director_status column
            ];
            
            Log::info('🔄 Updating user access record for ICT Director rejection', [
                'user_access_id' => $userAccess->id,
                'update_data' => $updateData
            ]);
            
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            Log::info('✅ User access record updated successfully for ICT Director rejection', [
                'user_access_id' => $userAccess->id,
                'ict_director_name' => $userAccess->ict_director_name,
                'calculated_status' => $userAccess->getCalculatedOverallStatus()
            ]);
            
            // Create notification for Divisional Director about ICT director rejection
            try {
                // For now we'll skip this as we don't have a direct relationship to find the divisional director
                Log::info('✅ ICT Director rejection notification skipped (no direct divisional director relationship)');
            } catch (\Exception $e) {
                Log::error('❌ Failed to create notification for ICT Director rejection', [
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
                'message' => 'Request rejected by ICT Director.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
                    'ict_director_rejection' => [
                        'name' => $userAccess->ict_director_name,
                        'rejected_at' => $userAccess->ict_director_approved_at->format('Y-m-d H:i:s'),
                        'rejected_at_formatted' => $userAccess->ict_director_approved_at->format('m/d/Y'),
                        'reason' => $userAccess->ict_director_comments
                    ]
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Validation failed for ICT Director rejection', [
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
            
            Log::error('❌ Error updating ICT Director rejection', [
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
    
    /**
     * Head of IT approval for a user access request
     */
    public function approveHeadOfIT(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Check if user is Head of IT
            if (!in_array('head_of_it', $userRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Head of IT can perform this action.'
                ], 403);
            }
            
            Log::info('🔍 HEAD OF IT APPROVAL START', [
                'user_access_id' => $userAccessId,
                'head_it_user_id' => $currentUser->id,
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'has_files' => $request->hasFile('head_it_signature'),
                'all_input_keys' => array_keys($request->all())
            ]);
            
            // Validation
            $validated = $request->validate([
                'head_it_name' => 'required|string|max:255',
                'head_it_signature' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'approved_date' => 'required|date',
                'comments' => 'nullable|string|max:1000',
            ], [
                'head_it_name.required' => 'Head of IT name is required',
                'head_it_signature.required' => 'Head of IT signature file is required',
                'head_it_signature.mimes' => 'Signature must be in JPEG, JPG, PNG, or PDF format',
                'head_it_signature.max' => 'Signature file must not exceed 2MB',
                'approved_date.required' => 'Approval date is required',
                'approved_date.date' => 'Please provide a valid approval date',
            ]);
            
            Log::info('✅ Validation passed', [
                'validated_data' => Arr::except($validated, ['head_it_signature']),
                'has_signature_file' => isset($validated['head_it_signature'])
            ]);
            
            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Verify request is in correct status (must be ICT Director approved)
            if ($userAccess->ict_director_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be ICT Director approved before Head of IT can approve it.'
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Handle signature upload
            $headItSignaturePath = null;
            if ($request->hasFile('head_it_signature')) {
                try {
                    $signatureFile = $request->file('head_it_signature');
                    
                    if (!$signatureFile->isValid()) {
                        throw new \Exception('Uploaded signature file is invalid');
                    }
                    
                    $signatureDir = 'signatures/head_of_it';
                    if (!Storage::disk('public')->exists($signatureDir)) {
                        Storage::disk('public')->makeDirectory($signatureDir);
                    }
                    
                    $filename = 'head_it_signature_' . $userAccess->pf_number . '_' . time() . '.' . $signatureFile->getClientOriginalExtension();
                    $headItSignaturePath = $signatureFile->storeAs($signatureDir, $filename, 'public');
                    
                    if (!Storage::disk('public')->exists($headItSignaturePath)) {
                        throw new \Exception('Failed to store signature file');
                    }
                    
                    Log::info('✅ Head of IT signature uploaded successfully', [
                        'original_name' => $signatureFile->getClientOriginalName(),
                        'stored_path' => $headItSignaturePath,
                        'file_size' => $signatureFile->getSize(),
                        'mime_type' => $signatureFile->getMimeType()
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('❌ Head of IT signature upload failed', [
                        'error' => $e->getMessage(),
                        'file_info' => [
                            'name' => $request->file('head_it_signature')->getClientOriginalName(),
                            'size' => $request->file('head_it_signature')->getSize(),
                            'mime' => $request->file('head_it_signature')->getMimeType()
                        ]
                    ]);
                    
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload Head of IT signature: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Update the user access record
            $updateData = [
                'head_it_name' => $validated['head_it_name'],
                'head_it_signature_path' => $headItSignaturePath,
                'head_it_approved_at' => $validated['approved_date'],
                'head_it_comments' => $validated['comments'] ?? null,
                'head_it_status' => 'approved',
                'ict_officer_status' => 'pending' // Advance to ICT Officer for implementation
            ];
            
            Log::info('🔄 Updating user access record for Head of IT approval', [
                'user_access_id' => $userAccess->id,
                'update_data' => Arr::except($updateData, ['head_it_signature_path']),
                'has_signature_path' => !empty($updateData['head_it_signature_path'])
            ]);
            
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            Log::info('✅ User access record updated successfully for Head of IT approval', [
                'user_access_id' => $userAccess->id,
                'head_it_name' => $userAccess->head_it_name,
                'head_it_signature_path' => $userAccess->head_it_signature_path,
                'head_it_approved_at' => $userAccess->head_it_approved_at,
                'calculated_status' => $userAccess->getCalculatedOverallStatus()
            ]);
            
            // Send SMS notifications (notify requester that request is approved and awaiting ICT Officer assignment)
            try {
                $sms = app(SmsModule::class);
                $sms->notifyRequestApproved(
                    $userAccess,
                    $currentUser,
                    'head_it',
                    null // No next approver - awaiting ICT Officer assignment by Head of IT
                );
                
                Log::info('✅ SMS notifications sent for Head of IT approval', [
                    'request_id' => $userAccess->id
                ]);
            } catch (\Exception $e) {
                Log::warning('❌ Failed to send SMS notifications for Head of IT approval', [
                    'request_id' => $userAccess->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the main operation if SMS fails
            }
            
            DB::commit();
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'Request approved by Head of IT successfully.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
                    'head_it_approval' => [
                        'name' => $userAccess->head_it_name,
                        'approved_at' => $userAccess->head_it_approved_at->format('Y-m-d H:i:s'),
                        'approved_at_formatted' => $userAccess->head_it_approved_at->format('m/d/Y'),
                        'comments' => $userAccess->head_it_comments,
                        'signature_path' => $userAccess->head_it_signature_path
                    ]
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Validation failed for Head of IT approval', [
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
            
            Log::error('❌ Error updating Head of IT approval', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ICT Officer implementation (grant access)
     */
    public function approveIctOfficer(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();

            // Check if user is ICT Officer
            if (!in_array('ict_officer', $userRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only ICT Officer can perform this action.'
                ], 403);
            }

            Log::info('🔍 ICT OFFICER IMPLEMENTATION START', [
                'user_access_id' => $userAccessId,
                'ict_officer_user_id' => $currentUser->id,
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'has_files' => $request->hasFile('ict_officer_signature'),
                'all_input_keys' => array_keys($request->all())
            ]);

            // Validation
            $validated = $request->validate([
                'ict_officer_name' => 'required|string|max:255',
                'ict_officer_signature' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'approved_date' => 'required|date',
                'comments' => 'nullable|string|max:1000',
                'status' => 'nullable|string|in:implemented,pending,rejected'
            ], [
                'ict_officer_name.required' => 'ICT Officer name is required',
                'ict_officer_signature.required' => 'ICT Officer signature file is required',
                'ict_officer_signature.mimes' => 'Signature must be in JPEG, JPG, PNG, or PDF format',
                'ict_officer_signature.max' => 'Signature file must not exceed 2MB',
                'approved_date.required' => 'Implementation date is required',
                'approved_date.date' => 'Please provide a valid date',
            ]);

            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);

            // Verify request is in correct status (must be Head of IT approved)
            if ($userAccess->head_it_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be Head of IT approved before ICT Officer can implement it.'
                ], 422);
            }

            DB::beginTransaction();

            // Handle signature upload
            $ictOfficerSignaturePath = null;
            if ($request->hasFile('ict_officer_signature')) {
                $signatureFile = $request->file('ict_officer_signature');
                if (!$signatureFile->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Uploaded signature file is invalid'
                    ], 422);
                }
                $signatureDir = 'signatures/ict_officer';
                if (!Storage::disk('public')->exists($signatureDir)) {
                    Storage::disk('public')->makeDirectory($signatureDir);
                }
                $filename = 'ict_officer_signature_' . $userAccess->pf_number . '_' . time() . '.' . $signatureFile->getClientOriginalExtension();
                $ictOfficerSignaturePath = $signatureFile->storeAs($signatureDir, $filename, 'public');
                if (!Storage::disk('public')->exists($ictOfficerSignaturePath)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to store signature file'
                    ], 500);
                }
            }

            // Update the user access record
            $status = $validated['status'] ?? 'implemented';
            $updateData = [
                'ict_officer_name' => $validated['ict_officer_name'],
                'ict_officer_signature_path' => $ictOfficerSignaturePath,
                'ict_officer_implemented_at' => $validated['approved_date'],
                'ict_officer_comments' => $validated['comments'] ?? null,
                'ict_officer_status' => $status
            ];
            $userAccess->update($updateData);
            $userAccess->refresh();

            // CRITICAL: Complete the ICT task assignment to update badge counts
            // This MUST succeed for the notification badge to clear properly
            $latestAssignment = \App\Models\IctTaskAssignment::where('user_access_id', $userAccess->id)
                ->whereIn('status', [
                    \App\Models\IctTaskAssignment::STATUS_ASSIGNED,
                    \App\Models\IctTaskAssignment::STATUS_IN_PROGRESS
                ])
                ->orderByDesc('assigned_at')
                ->first();
            
            if ($latestAssignment) {
                $taskUpdateSuccess = $latestAssignment->update([
                    'status' => \App\Models\IctTaskAssignment::STATUS_COMPLETED,
                    'started_at' => $latestAssignment->started_at ?: now(),
                    'completed_at' => now(),
                    'completion_notes' => $validated['comments'] ?? 'Access granted via BothServiceFormController'
                ]);
                
                if ($taskUpdateSuccess) {
                    Log::info('✅ ICT task assignment marked as completed', [
                        'assignment_id' => $latestAssignment->id,
                        'user_access_id' => $userAccess->id,
                        'ict_officer_id' => $currentUser->id
                    ]);
                } else {
                    Log::error('❌ CRITICAL: Failed to mark task as completed - badge count will be wrong!', [
                        'assignment_id' => $latestAssignment->id,
                        'user_access_id' => $userAccess->id,
                        'ict_officer_id' => $currentUser->id
                    ]);
                }
            } else {
                Log::warning('⚠️ No active task assignment found to complete', [
                    'user_access_id' => $userAccess->id,
                    'ict_officer_id' => $currentUser->id,
                    'note' => 'Badge may show stale count if assignment exists in other status'
                ]);
            }

            // Send SMS notification to requester that access has been granted
            try {
                $smsModule = app(\App\Services\SmsModule::class);
                $smsResults = $smsModule->notifyAccessGranted($userAccess, $currentUser, $validated['comments'] ?? null);
                
                Log::info('✅ SMS notification sent to requester for access granted', [
                    'request_id' => $userAccessId,
                    'ict_officer_id' => $currentUser->id,
                    'sms_results' => $smsResults
                ]);
            } catch (\Exception $smsError) {
                Log::warning('❌ Failed to send access granted SMS notification', [
                    'request_id' => $userAccessId,
                    'ict_officer_id' => $currentUser->id,
                    'error' => $smsError->getMessage()
                ]);
                // Don't fail the main operation if SMS fails
            }

            DB::commit();

            // Load relationships for response
            $userAccess->load(['user', 'department']);

            return response()->json([
                'success' => true,
                'message' => 'ICT Officer implementation saved successfully.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
                    'ict_officer_implementation' => [
                        'name' => $userAccess->ict_officer_name,
                        'implemented_at' => $userAccess->ict_officer_implemented_at,
                        'comments' => $userAccess->ict_officer_comments,
                        'signature_url' => $userAccess->ict_officer_signature_path ? Storage::url($userAccess->ict_officer_signature_path) : null,
                        'signature_path' => $userAccess->ict_officer_signature_path,
                        'implementation_status' => $userAccess->ict_officer_status
                    ],
                    'next_step' => 'completed'
                ]
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving ICT Officer implementation', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save ICT Officer implementation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Head of IT rejection for a user access request
     */
    public function rejectHeadOfIT(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Check if user is Head of IT
            if (!in_array('head_of_it', $userRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Head of IT can perform this action.'
                ], 403);
            }
            
            Log::info('🔍 HEAD OF IT REJECTION START', [
                'user_access_id' => $userAccessId,
                'head_it_user_id' => $currentUser->id,
                'request_method' => $request->method(),
                'all_input_keys' => array_keys($request->all())
            ]);
            
            // Validation
            $validated = $request->validate([
                'head_it_name' => 'required|string|max:255',
                'rejection_date' => 'required|date',
                'rejection_reason' => 'required|string|max:1000',
            ], [
                'head_it_name.required' => 'Head of IT name is required',
                'rejection_date.required' => 'Rejection date is required',
                'rejection_date.date' => 'Please provide a valid rejection date',
                'rejection_reason.required' => 'Rejection reason is required'
            ]);
            
            Log::info('✅ Validation passed', [
                'validated_data' => $validated
            ]);
            
            // Get user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Verify request is in correct status (must be ICT Director approved)
            if ($userAccess->ict_director_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be ICT Director approved before Head of IT can reject it.'
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Update the user access record
            $updateData = [
                'head_it_name' => $validated['head_it_name'],
                'head_it_approved_at' => $validated['rejection_date'],
                'head_it_comments' => $validated['rejection_reason'],
                'head_it_status' => 'rejected'
            ];
            
            Log::info('🔄 Updating user access record for Head of IT rejection', [
                'user_access_id' => $userAccess->id,
                'update_data' => $updateData
            ]);
            
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            Log::info('✅ User access record updated successfully for Head of IT rejection', [
                'user_access_id' => $userAccess->id,
                'head_it_name' => $userAccess->head_it_name,
                'head_it_approved_at' => $userAccess->head_it_approved_at,
                'calculated_status' => $userAccess->getCalculatedOverallStatus()
            ]);
            
            DB::commit();
            
            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'Request rejected by Head of IT.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
                    'head_it_rejection' => [
                        'name' => $userAccess->head_it_name,
                        'rejected_at' => $userAccess->head_it_approved_at->format('Y-m-d H:i:s'),
                        'rejected_at_formatted' => $userAccess->head_it_approved_at->format('m/d/Y'),
                        'reason' => $userAccess->head_it_comments
                    ]
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Validation failed for Head of IT rejection', [
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
            
            Log::error('❌ Error updating Head of IT rejection', [
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
