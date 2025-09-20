<?php

namespace App\Services;

use App\Models\UserAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UserAccessWorkflowService
{
    /**
     * Create a new user access request with complete form data
     */
    public function createRequest(Request $request): UserAccess
    {
        return DB::transaction(function () use ($request) {
            $data = $this->validateAndPrepareRequestData($request);
            
            // Handle signature upload if provided
            if ($request->hasFile('signature')) {
                $data['signature_path'] = $this->handleSignatureUpload($request->file('signature'), $data['pf_number']);
            }
            
            return UserAccess::create($data);
        });
    }

    /**
     * Update existing request with new form data
     */
    public function updateRequest(UserAccess $userAccess, Request $request): UserAccess
    {
        return DB::transaction(function () use ($userAccess, $request) {
            $data = $this->validateAndPrepareRequestData($request);
            
            // Handle signature upload if provided
            if ($request->hasFile('signature')) {
                // Delete old signature if exists
                if ($userAccess->signature_path) {
                    Storage::disk('public')->delete($userAccess->signature_path);
                }
                $data['signature_path'] = $this->handleSignatureUpload($request->file('signature'), $data['pf_number']);
            }
            
            $userAccess->update($data);
            return $userAccess->fresh();
        });
    }

    /**
     * Process HOD approval
     */
    public function processHodApproval(UserAccess $userAccess, Request $request): UserAccess
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'required|string|max:1000',
            'hod_name' => 'required|string|max:255',
            'signature' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        return DB::transaction(function () use ($userAccess, $request) {
            $data = [
                'hod_name' => $request->hod_name,
                'hod_comments' => $request->comments,
                'hod_approved_at' => now(),
            ];

            if ($request->action === 'approve') {
                $data['status'] = 'pending_divisional';
            } else {
                $data['status'] = 'hod_rejected';
            }

            // Handle HOD signature if provided
            if ($request->hasFile('signature')) {
                $data['hod_signature_path'] = $this->handleSignatureUpload(
                    $request->file('signature'), 
                    $userAccess->pf_number . '_hod'
                );
            }

            $userAccess->update($data);
            return $userAccess->fresh();
        });
    }

    /**
     * Process Divisional Director approval
     */
    public function processDivisionalApproval(UserAccess $userAccess, Request $request): UserAccess
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'divisional_director_name' => 'required|string|max:255',
            'signature' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        return DB::transaction(function () use ($userAccess, $request) {
            $data = [
                'divisional_director_name' => $request->divisional_director_name,
                'divisional_director_comments' => $request->comments,
                'divisional_approved_at' => now(),
            ];

            if ($request->action === 'approve') {
                $data['status'] = 'pending_ict_director';
            } else {
                $data['status'] = 'divisional_rejected';
            }

            if ($request->hasFile('signature')) {
                $data['divisional_director_signature_path'] = $this->handleSignatureUpload(
                    $request->file('signature'), 
                    $userAccess->pf_number . '_divisional'
                );
            }

            $userAccess->update($data);
            return $userAccess->fresh();
        });
    }

    /**
     * Process ICT Director approval
     */
    public function processIctDirectorApproval(UserAccess $userAccess, Request $request): UserAccess
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'ict_director_name' => 'required|string|max:255',
            'signature' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        return DB::transaction(function () use ($userAccess, $request) {
            $data = [
                'ict_director_name' => $request->ict_director_name,
                'ict_director_comments' => $request->comments,
                'ict_director_approved_at' => now(),
            ];

            if ($request->action === 'approve') {
                $data['status'] = 'pending_head_it';
            } else {
                $data['status'] = 'ict_director_rejected';
            }

            if ($request->hasFile('signature')) {
                $data['ict_director_signature_path'] = $this->handleSignatureUpload(
                    $request->file('signature'), 
                    $userAccess->pf_number . '_ict_director'
                );
            }

            $userAccess->update($data);
            return $userAccess->fresh();
        });
    }

    /**
     * Process Head IT approval
     */
    public function processHeadItApproval(UserAccess $userAccess, Request $request): UserAccess
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'head_it_name' => 'required|string|max:255',
            'signature' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        return DB::transaction(function () use ($userAccess, $request) {
            $data = [
                'head_it_name' => $request->head_it_name,
                'head_it_comments' => $request->comments,
                'head_it_approved_at' => now(),
            ];

            if ($request->action === 'approve') {
                $data['status'] = 'pending_ict_officer';
            } else {
                $data['status'] = 'head_it_rejected';
            }

            if ($request->hasFile('signature')) {
                $data['head_it_signature_path'] = $this->handleSignatureUpload(
                    $request->file('signature'), 
                    $userAccess->pf_number . '_head_it'
                );
            }

            $userAccess->update($data);
            return $userAccess->fresh();
        });
    }

    /**
     * Process ICT Officer implementation
     */
    public function processIctOfficerImplementation(UserAccess $userAccess, Request $request): UserAccess
    {
        $request->validate([
            'ict_officer_name' => 'required|string|max:255',
            'implementation_comments' => 'nullable|string|max:1000',
            'signature' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        return DB::transaction(function () use ($userAccess, $request) {
            $data = [
                'ict_officer_name' => $request->ict_officer_name,
                'ict_officer_comments' => $request->implementation_comments,
                'implementation_comments' => $request->implementation_comments,
                'ict_officer_implemented_at' => now(),
                'status' => 'implemented',
            ];

            if ($request->hasFile('signature')) {
                $data['ict_officer_signature_path'] = $this->handleSignatureUpload(
                    $request->file('signature'), 
                    $userAccess->pf_number . '_ict_officer'
                );
            }

            $userAccess->update($data);
            return $userAccess->fresh();
        });
    }

    /**
     * Get requests based on user role and approval stage
     */
    public function getRequestsForApproval(User $user, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = UserAccess::with(['user', 'department']);

        // Filter by status based on user role
        $status = $this->getStatusForUserRole($user);
        if ($status) {
            $query->whereIn('status', $status);
        }
        
        // DEPARTMENT FILTERING: HODs only see requests from their department(s)
        if ($user->hasRole('head_of_department')) {
            $hodDepartmentIds = $user->departmentsAsHOD()->pluck('id')->toArray();
            if (!empty($hodDepartmentIds)) {
                $query->whereIn('department_id', $hodDepartmentIds);
            } else {
                // If user has HOD role but no departments assigned, show no requests
                $query->whereRaw('1 = 0');
            }
        }

        // Apply additional filters
        if (isset($filters['search']) && $filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('pf_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('staff_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['request_type']) && $filters['request_type']) {
            $query->whereJsonContains('request_type', $filters['request_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(User $user): array
    {
        $baseQuery = UserAccess::query();
        
        // Filter by user role permissions
        $allowedStatuses = $this->getStatusForUserRole($user);
        if ($allowedStatuses) {
            $baseQuery->whereIn('status', $allowedStatuses);
        }
        
        // DEPARTMENT FILTERING: HODs only see statistics from their department(s)
        if ($user->hasRole('head_of_department')) {
            $hodDepartmentIds = $user->departmentsAsHOD()->pluck('id')->toArray();
            if (!empty($hodDepartmentIds)) {
                $baseQuery->whereIn('department_id', $hodDepartmentIds);
            } else {
                // If user has HOD role but no departments assigned, show zero stats
                $baseQuery->whereRaw('1 = 0');
            }
        }

        return [
            'total' => $baseQuery->count(),
            'pending' => (clone $baseQuery)->where('status', 'like', 'pending%')->count(),
            'approved' => (clone $baseQuery)->whereIn('status', ['approved', 'implemented'])->count(),
            'rejected' => (clone $baseQuery)->where('status', 'like', '%rejected')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Validate and prepare request data
     */
    private function validateAndPrepareRequestData(Request $request): array
    {
        $request->validate([
            'pf_number' => 'required|string|max:50',
            'staff_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'request_type' => 'required|array',
            'request_type.*' => 'in:jeeva_access,wellsoft,internet_access_request',
            'wellsoft_modules_selected' => 'nullable|array',
            'jeeva_modules_selected' => 'nullable|array',
            'internet_purposes' => 'nullable|array',
            'access_type' => 'required|in:permanent,temporary',
            'temporary_until' => 'required_if:access_type,temporary|nullable|date|after:today',
        ]);

        return [
            'user_id' => auth()->id(),
            'pf_number' => $request->pf_number,
            'staff_name' => $request->staff_name,
            'phone_number' => $request->phone_number,
            'department_id' => $request->department_id,
            'request_type' => $request->request_type,
            'wellsoft_modules_selected' => $request->wellsoft_modules_selected,
            'jeeva_modules_selected' => $request->jeeva_modules_selected,
            'internet_purposes' => $request->internet_purposes,
            'access_type' => $request->access_type,
            'temporary_until' => $request->temporary_until,
            'status' => 'pending_hod',
        ];
    }

    /**
     * Handle signature file upload
     */
    private function handleSignatureUpload($file, string $prefix): string
    {
        $timestamp = time();
        $extension = $file->getClientOriginalExtension();
        $filename = "signature_{$timestamp}.{$extension}";
        $path = "signatures/{$prefix}";
        
        return $file->storeAs($path, $filename, 'public');
    }

    /**
     * Get allowed statuses based on user role
     */
    private function getStatusForUserRole(User $user): ?array
    {
        if (!$user->hasRole()) {
            return null;
        }

        return match($user->role->name) {
            'HEAD_OF_DEPARTMENT' => ['pending_hod', 'hod_approved', 'hod_rejected'],
            'DIVISIONAL_DIRECTOR' => ['pending_divisional', 'divisional_approved', 'divisional_rejected'],
            'ICT_DIRECTOR' => ['pending_ict_director', 'ict_director_approved', 'ict_director_rejected'],
            'HEAD_IT' => ['pending_head_it', 'head_it_approved', 'head_it_rejected'],
            'ICT_OFFICER' => ['pending_ict_officer', 'implemented'],
            'ADMIN' => UserAccess::STATUSES,
            default => null
        };
    }

    /**
     * Transform request for API response
     */
    public function transformRequestData(UserAccess $request): array
    {
        return [
            'id' => $request->id,
            'pf_number' => $request->pf_number,
            'staff_name' => $request->staff_name,
            'phone_number' => $request->phone_number,
            'department' => $request->department->name ?? 'Unknown',
            'request_type' => $request->request_type,
            'request_type_name' => $request->request_type_name,
            'wellsoft_modules_selected' => $request->wellsoft_modules_selected,
            'jeeva_modules_selected' => $request->jeeva_modules_selected,
            'internet_purposes' => $request->internet_purposes,
            'access_type' => $request->access_type,
            'access_type_name' => $request->access_type_name,
            'temporary_until' => $request->temporary_until?->format('Y-m-d'),
            'status' => $request->status,
            'status_name' => $request->status_name,
            'signature_path' => $request->signature_path,
            'all_modules' => $request->all_modules,
            'next_approval_stage' => $request->getNextApprovalStage(),
            
            // HOD Approval
            'hod_name' => $request->hod_name,
            'hod_comments' => $request->hod_comments,
            'hod_approved_at' => $request->hod_approved_at?->format('Y-m-d H:i:s'),
            
            // Divisional Director
            'divisional_director_name' => $request->divisional_director_name,
            'divisional_director_signature_path' => $request->divisional_director_signature_path,
            'divisional_director_comments' => $request->divisional_director_comments,
            'divisional_approved_at' => $request->divisional_approved_at?->format('Y-m-d H:i:s'),
            
            // ICT Director
            'ict_director_name' => $request->ict_director_name,
            'ict_director_signature_path' => $request->ict_director_signature_path,
            'ict_director_comments' => $request->ict_director_comments,
            'ict_director_approved_at' => $request->ict_director_approved_at?->format('Y-m-d H:i:s'),
            
            // Head IT
            'head_it_name' => $request->head_it_name,
            'head_it_signature_path' => $request->head_it_signature_path,
            'head_it_comments' => $request->head_it_comments,
            'head_it_approved_at' => $request->head_it_approved_at?->format('Y-m-d H:i:s'),
            
            // ICT Officer
            'ict_officer_name' => $request->ict_officer_name,
            'ict_officer_signature_path' => $request->ict_officer_signature_path,
            'ict_officer_comments' => $request->ict_officer_comments,
            'ict_officer_implemented_at' => $request->ict_officer_implemented_at?->format('Y-m-d H:i:s'),
            'implementation_comments' => $request->implementation_comments,
            
            'created_at' => $request->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $request->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
