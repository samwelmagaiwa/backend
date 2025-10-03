<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleAccessApprovalRequest;
use App\Models\UserAccess;
use App\Events\ApprovalStatusChanged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleAccessApprovalController extends Controller
{
    /**
     * Get a request for approval form display
     */
    public function getRequestForApproval($id, Request $request): JsonResponse
    {
        try {
            $userAccess = UserAccess::with(['user', 'department'])->findOrFail($id);
            $currentUser = Auth::user();
            
            // Check authorization based on user role and current approval stage
            if (!$this->canUserApproveRequest($currentUser, $userAccess)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to approve this request at this stage.'
                ], 403);
            }
            
            // Get the current stage for this approver
            $currentStage = $this->getCurrentStageForUser($currentUser, $userAccess);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'request' => $this->transformRequestForApproval($userAccess),
                    'current_stage' => $currentStage,
                    'approver_info' => [
                        'name' => $currentUser->name,
                        'role' => $currentUser->getPrimaryRole()?->name,
                        'stage' => $currentStage
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting request for approval', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve request for approval'
            ], 500);
        }
    }
    
    /**
     * Process approval at any stage
     */
    public function processApproval($id, ModuleAccessApprovalRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $userAccess = UserAccess::findOrFail($id);
            $currentUser = Auth::user();
            $approvalData = $request->getProcessedData();
            
            Log::info('Processing module access approval', [
                'request_id' => $id,
                'stage' => $approvalData['stage'],
                'status' => $approvalData['approval_status'],
                'approver_id' => $currentUser->id,
                'wellsoft_modules_count' => count($approvalData['wellsoft_modules'] ?? []),
                'jeeva_modules_count' => count($approvalData['jeeva_modules'] ?? []),
                'internet_purposes_count' => count($approvalData['internet_purposes'] ?? [])
            ]);
            
            // Check authorization
            if (!$this->canUserApproveRequest($currentUser, $userAccess)) {
                throw new \Exception('Unauthorized to approve this request');
            }
            
            // Update request with approval data and module selections
            $updateData = $this->buildUpdateData($approvalData, $currentUser);
            $userAccess->update($updateData);
            
            // Update workflow status
            $newStatus = $this->getNextWorkflowStatus($approvalData['stage'], $approvalData['approval_status']);
            $userAccess->update(['status' => $newStatus]);
            
            $freshUserAccess = $userAccess->fresh();
            
            // Fire SMS notification event
            try {
                $user = $freshUserAccess->user;
                $approver = $currentUser;
                $oldStatus = $this->getPreviousStatus($approvalData['stage']);
                
                // Get additional users to notify (next approvers if approved)
                $additionalNotifyUsers = [];
                if ($approvalData['approval_status'] === 'approved') {
                    $additionalNotifyUsers = $this->getNextApprovers($freshUserAccess);
                }
                
                ApprovalStatusChanged::dispatch(
                    $user,
                    $freshUserAccess,
                    'module_access',
                    $oldStatus,
                    $newStatus,
                    $approver,
                    $approvalData['comments'] ?? null,
                    $additionalNotifyUsers
                );
                
                Log::info('SMS notification event fired for module access approval', [
                    'request_id' => $freshUserAccess->id,
                    'stage' => $approvalData['stage'],
                    'action' => $approvalData['approval_status'],
                    'approver_id' => $approver->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fire SMS notification for module access approval', [
                    'request_id' => $freshUserAccess->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            DB::commit();
            
            Log::info('Module access approval processed successfully', [
                'request_id' => $id,
                'stage' => $approvalData['stage'],
                'new_status' => $newStatus
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Approval processed successfully',
                'data' => $this->transformRequestForApproval($freshUserAccess)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error processing approval', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process approval: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check if user can approve the request at current stage
     */
    private function canUserApproveRequest($user, $userAccess): bool
    {
        $userRoles = $user->roles()->pluck('name')->toArray();
        $currentStage = $this->getCurrentStageForUser($user, $userAccess);
        
        if (!$currentStage) {
            return false;
        }
        
        // Role-based authorization logic
        switch ($currentStage) {
            case 'hod':
                if (!in_array('head_of_department', $userRoles)) return false;
                // Check if HOD manages the request's department
                $hodDepartmentIds = $user->departmentsAsHOD()->pluck('id')->toArray();
                return in_array($userAccess->department_id, $hodDepartmentIds);
                
            case 'divisional_director':
                if (!in_array('divisional_director', $userRoles)) return false;
                // Add departmental check for divisional director if needed
                return true;
                
            case 'ict_director':
                return in_array('ict_director', $userRoles);
                
            case 'head_it':
                return in_array('head_it', $userRoles);
                
            case 'ict_officer':
                return in_array('ict_officer', $userRoles);
                
            default:
                return false;
        }
    }
    
    /**
     * Get current approval stage for the user
     */
    private function getCurrentStageForUser($user, $userAccess): ?string
    {
        $userRoles = $user->roles()->pluck('name')->toArray();
        
        // Determine stage based on request status and user role
        switch ($userAccess->status) {
            case 'pending':
            case 'pending_hod':
                if (in_array('head_of_department', $userRoles)) return 'hod';
                break;
                
            case 'hod_approved':
            case 'pending_divisional':
                if (in_array('divisional_director', $userRoles)) return 'divisional_director';
                break;
                
            case 'divisional_approved':
            case 'pending_ict_director':
                if (in_array('ict_director', $userRoles)) return 'ict_director';
                break;
                
            case 'ict_director_approved':
            case 'pending_head_it':
                if (in_array('head_it', $userRoles)) return 'head_it';
                break;
                
            case 'head_it_approved':
            case 'pending_ict_officer':
                if (in_array('ict_officer', $userRoles)) return 'ict_officer';
                break;
        }
        
        return null;
    }
    
    /**
     * Build update data array based on approval stage and form data
     */
    private function buildUpdateData($approvalData, $currentUser): array
    {
        $stage = $approvalData['stage'];
        $status = $approvalData['approval_status'];
        $timestamp = now();
        
        $updateData = [];
        
        // Add module form data (applies to all stages)
        $updateData['module_requested_for'] = $approvalData['module_requested_for'];
        $updateData['wellsoft_modules_selected'] = $approvalData['wellsoft_modules'] ?? [];
        $updateData['jeeva_modules_selected'] = $approvalData['jeeva_modules'] ?? [];
        $updateData['internet_purposes'] = $approvalData['internet_purposes'] ?? [];
        $updateData['access_type'] = $approvalData['access_type'];
        
        if (!empty($approvalData['temporary_until'])) {
            $updateData['temporary_until'] = $approvalData['temporary_until'];
        }
        
        // Stage-specific approval fields
        switch ($stage) {
            case 'hod':
                $updateData['hod_name'] = $currentUser->name;
                $updateData['hod_comments'] = $approvalData['comments'] ?? '';
                $updateData['hod_approved_at'] = $timestamp;
                $updateData['hod_approved_by'] = $currentUser->id;
                $updateData['hod_approved_by_name'] = $currentUser->name;
                break;
                
            case 'divisional_director':
                $updateData['divisional_director_name'] = $currentUser->name;
                $updateData['divisional_director_comments'] = $approvalData['comments'] ?? '';
                $updateData['divisional_approved_at'] = $timestamp;
                break;
                
            case 'ict_director':
                $updateData['ict_director_name'] = $currentUser->name;
                $updateData['ict_director_comments'] = $approvalData['comments'] ?? '';
                $updateData['ict_director_approved_at'] = $timestamp;
                break;
                
            case 'head_it':
                $updateData['head_it_name'] = $currentUser->name;
                $updateData['head_it_comments'] = $approvalData['comments'] ?? '';
                $updateData['head_it_approved_at'] = $timestamp;
                break;
                
            case 'ict_officer':
                $updateData['ict_officer_name'] = $currentUser->name;
                $updateData['ict_officer_comments'] = $approvalData['comments'] ?? '';
                $updateData['ict_officer_implemented_at'] = $timestamp;
                $updateData['implementation_comments'] = $approvalData['comments'] ?? '';
                break;
        }
        
        return $updateData;
    }
    
    /**
     * Get next workflow status based on stage and approval result
     */
    private function getNextWorkflowStatus($stage, $approvalStatus): string
    {
        if ($approvalStatus === 'rejected') {
            return $stage . '_rejected';
        }
        
        // If approved, move to next stage
        switch ($stage) {
            case 'hod':
                return 'hod_approved';
            case 'divisional_director':
                return 'divisional_approved';
            case 'ict_director':
                return 'ict_director_approved';
            case 'head_it':
                return 'head_it_approved';
            case 'ict_officer':
                return 'implemented';
            default:
                return 'approved';
        }
    }
    
    /**
     * Transform request data for approval form display
     */
    private function transformRequestForApproval($userAccess): array
    {
        return [
            'id' => $userAccess->id,
            'request_id' => 'REQ-' . str_pad($userAccess->id, 6, '0', STR_PAD_LEFT),
            'staff_name' => $userAccess->staff_name,
            'pf_number' => $userAccess->pf_number,
            'phone_number' => $userAccess->phone_number,
            'department' => $userAccess->department?->name,
            'department_id' => $userAccess->department_id,
            'request_type' => $userAccess->request_type,
            'purpose' => $userAccess->purpose,
            'status' => $userAccess->status,
            'signature_path' => $userAccess->signature_path,
            
            // Module form data
            'module_requested_for' => $userAccess->module_requested_for ?? 'use',
            'wellsoft_modules' => $userAccess->wellsoft_modules ?? [],
            'jeeva_modules' => $userAccess->jeeva_modules ?? [],
            'wellsoft_modules_selected' => $userAccess->wellsoft_modules_selected ?? [],
            'jeeva_modules_selected' => $userAccess->jeeva_modules_selected ?? [],
            'internet_purposes' => $userAccess->internet_purposes ?? [],
            'access_type' => $userAccess->access_type ?? 'permanent',
            'temporary_until' => $userAccess->temporary_until?->format('Y-m-d'),
            
            // Approval trail
            'hod_approval' => [
                'name' => $userAccess->hod_name,
                'comments' => $userAccess->hod_comments,
                'approved_at' => $userAccess->hod_approved_at?->format('Y-m-d H:i:s'),
            ],
            'divisional_approval' => [
                'name' => $userAccess->divisional_director_name,
                'comments' => $userAccess->divisional_director_comments,
                'approved_at' => $userAccess->divisional_approved_at?->format('Y-m-d H:i:s'),
            ],
            'ict_director_approval' => [
                'name' => $userAccess->ict_director_name,
                'comments' => $userAccess->ict_director_comments,
                'approved_at' => $userAccess->ict_director_approved_at?->format('Y-m-d H:i:s'),
            ],
            'head_it_approval' => [
                'name' => $userAccess->head_it_name,
                'comments' => $userAccess->head_it_comments,
                'approved_at' => $userAccess->head_it_approved_at?->format('Y-m-d H:i:s'),
            ],
            'ict_officer_implementation' => [
                'name' => $userAccess->ict_officer_name,
                'comments' => $userAccess->ict_officer_comments,
                'implemented_at' => $userAccess->ict_officer_implemented_at?->format('Y-m-d H:i:s'),
                'implementation_comments' => $userAccess->implementation_comments,
            ],
            
            'created_at' => $userAccess->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $userAccess->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
    
    /**
     * Get previous status based on current stage
     */
    private function getPreviousStatus($stage): string
    {
        switch ($stage) {
            case 'hod':
                return 'pending';
            case 'divisional_director':
                return 'hod_approved';
            case 'ict_director':
                return 'divisional_approved';
            case 'head_it':
                return 'ict_director_approved';
            case 'ict_officer':
                return 'head_it_approved';
            default:
                return 'pending';
        }
    }
    
    /**
     * Get next approvers based on current status
     */
    private function getNextApprovers(UserAccess $userAccess): array
    {
        $approvers = [];
        
        switch ($userAccess->status) {
            case 'hod_approved':
            case 'pending_divisional':
                $approvers = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'divisional_director');
                })->whereNotNull('phone')->get()->toArray();
                break;
                
            case 'divisional_approved':
            case 'pending_ict_director':
                $approvers = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'ict_director');
                })->whereNotNull('phone')->get()->toArray();
                break;
                
            case 'ict_director_approved':
            case 'pending_head_it':
                $approvers = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'head_it');
                })->whereNotNull('phone')->get()->toArray();
                break;
                
            case 'head_it_approved':
            case 'pending_ict_officer':
                $approvers = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'ict_officer');
                })->whereNotNull('phone')->get()->toArray();
                break;
        }
        
        return $approvers;
    }
}
