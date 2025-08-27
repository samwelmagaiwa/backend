<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ModuleAccessRequest;
use App\Models\User;
use App\Models\Department;
use App\Models\UserAccess;
use App\Http\Requests\BothServiceForm\CreateBothServiceFormRequest;
use App\Http\Requests\BothServiceForm\ApprovalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PDF;

class BothServiceFormController extends Controller
{
    /**
     * Get user information for auto-population from user_access table
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get latest user access request for department info
            $latestAccess = UserAccess::where('user_id', $user->id)
                ->with('department')
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'pf_number' => $user->pf_number ?? '',
                    'staff_name' => $user->name ?? '',
                    'phone_number' => $user->phone ?? '',
                    'department_id' => $latestAccess?->department_id,
                    'department_name' => $latestAccess?->department?->name,
                    'role' => $user->role?->name ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user info for both-service-form', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get user information'
            ], 500);
        }
    }

    /**
     * Get personal information from user_access table for HOD dashboard
     */
    public function getPersonalInfoFromUserAccess(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            $userRole = $currentUser->getPrimaryRole()?->name; // For backward compatibility

            // Check if user has permission to access this data
            $allowedRoles = ['head_of_department', 'divisional_director', 'ict_director', 'ict_officer', 'admin', 'super_admin'];
            if (!array_intersect($userRoles, $allowedRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Insufficient permissions.'
                ], 403);
            }

            // Get user access record with related data
            $userAccess = UserAccess::with(['user', 'department'])
                ->findOrFail($userAccessId);

            // For HOD, ensure they can only access requests from their department
            if (array_intersect($userRoles, ['head_of_department'])) {
                $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
                if (!$hodDepartment || $userAccess->department_id !== $hodDepartment->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied. You can only access requests from your department.'
                    ], 403);
                }
            }

            // Prepare personal information data
            $personalInfo = [
                'pf_number' => $userAccess->pf_number,
                'staff_name' => $userAccess->staff_name,
                'department' => $userAccess->department?->name ?? 'N/A',
                'department_id' => $userAccess->department_id,
                'contact_number' => $userAccess->phone_number,
                'signature' => [
                    'path' => $userAccess->signature_path,
                    'url' => $userAccess->signature_path ? Storage::url($userAccess->signature_path) : null,
                    'exists' => $userAccess->signature_path ? Storage::exists($userAccess->signature_path) : false
                ],
                'request_details' => [
                    'id' => $userAccess->id,
                    'request_type' => $userAccess->request_type,
                    'purpose' => $userAccess->purpose,
                    'status' => $userAccess->status,
                    'created_at' => $userAccess->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $userAccess->updated_at->format('Y-m-d H:i:s')
                ],
                'user_details' => [
                    'id' => $userAccess->user->id,
                    'email' => $userAccess->user->email,
                    'role' => $userAccess->user->role?->name ?? 'N/A'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $personalInfo,
                'meta' => [
                    'accessed_by' => $currentUser->name,
                    'accessed_by_role' => $userRole,
                    'access_time' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting personal info from user_access', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get personal information'
            ], 500);
        }
    }

    /**
     * Get all user access requests for HOD dashboard with personal information
     */
    public function getUserAccessRequestsForHOD(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            $userRole = $currentUser->getPrimaryRole()?->name; // For backward compatibility

            // Check if user is HOD
            if (!array_intersect($userRoles, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only HOD can access this endpoint.'
                ], 403);
            }

            // Get HOD's department
            Log::info('HOD Department Lookup Debug', [
                'current_user_id' => $currentUser->id,
                'current_user_email' => $currentUser->email,
                'current_user_role' => $currentUser->role?->name,
            ]);
            
            $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
            
            // Debug: Log all departments and their HOD assignments
            $allDepartments = Department::select('id', 'name', 'code', 'hod_user_id')->get();
            Log::info('All Departments Debug', [
                'departments' => $allDepartments->toArray()
            ]);
            
            if (!$hodDepartment) {
                Log::error('No department found for HOD', [
                    'user_id' => $currentUser->id,
                    'user_email' => $currentUser->email,
                    'all_departments' => $allDepartments->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No department assigned to HOD.',
                    'debug' => [
                        'user_id' => $currentUser->id,
                        'user_email' => $currentUser->email,
                        'departments_count' => $allDepartments->count(),
                        'departments_with_hod' => $allDepartments->whereNotNull('hod_user_id')->values()
                    ]
                ], 404);
            }

            // Get user access requests from HOD's department
            $userAccessRequests = UserAccess::with(['user', 'department'])
                ->where('department_id', $hodDepartment->id)
                ->where('status', 'pending') // Only pending requests for HOD review
                ->orderBy('created_at', 'asc') // FIFO order
                ->get();

            // Transform data for HOD dashboard
            $requestsData = $userAccessRequests->map(function ($userAccess) {
                return [
                    'id' => $userAccess->id,
                    'personal_information' => [
                        'pf_number' => $userAccess->pf_number,
                        'staff_name' => $userAccess->staff_name,
                        'department' => $userAccess->department?->name ?? 'N/A',
                        'contact_number' => $userAccess->phone_number,
                        'signature' => [
                            'exists' => $userAccess->signature_path ? true : false,
                            'url' => $userAccess->signature_path ? Storage::url($userAccess->signature_path) : null,
                            'status' => $userAccess->signature_path ? 'Uploaded' : 'No signature uploaded'
                        ]
                    ],
                    'request_details' => [
                        'request_type' => $userAccess->request_type,
                        'purpose' => $userAccess->purpose,
                        'status' => $userAccess->status,
                        'submission_date' => $userAccess->created_at->format('Y-m-d'),
                        'submission_time' => $userAccess->created_at->format('H:i:s'),
                        'days_pending' => $userAccess->created_at->diffInDays(now())
                    ],
                    'user_info' => [
                        'email' => $userAccess->user->email,
                        'role' => $userAccess->user->role?->name ?? 'N/A'
                    ],
                    'actions' => [
                        'can_view' => true,
                        'can_approve' => true,
                        'can_reject' => true,
                        'can_edit_comments' => true
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $requestsData,
                'meta' => [
                    'total_requests' => $requestsData->count(),
                    'department' => $hodDepartment->name,
                    'hod_name' => $currentUser->name,
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting user access requests for HOD', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get user access requests'
            ], 500);
        }
    }

    /**
     * Get departments list
     */
    public function getDepartments(): JsonResponse
    {
        try {
            $departments = Department::active()
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $departments
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting departments', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get departments'
            ], 500);
        }
    }

    /**
     * Create a new both-service-form request
     */
    public function store(CreateBothServiceFormRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get validated data from the request
            $validated = $request->validated();

            DB::beginTransaction();

            // Handle signature upload
            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures/both-service-forms', 'public');
            }

            // Create the both-service-form request
            $bothServiceForm = ModuleAccessRequest::create([
                'user_id' => $user->id,
                'form_type' => ModuleAccessRequest::FORM_TYPE_BOTH_SERVICE,
                'services_requested' => $validated['services_requested'],
                'access_type' => $validated['access_type'],
                'temporary_until' => $validated['temporary_until'] ?? null,
                'modules' => $validated['modules'] ?? [],
                'comments' => $validated['comments'] ?? null,
                'department_id' => $validated['department_id'],
                'signature_path' => $signaturePath,
                'overall_status' => ModuleAccessRequest::STATUS_PENDING,
                'current_approval_stage' => ModuleAccessRequest::STAGE_HOD,
            ]);

            // Auto-populate personal information
            $bothServiceForm->populatePersonalInfo();
            $bothServiceForm->save();

            DB::commit();

            Log::info('Both-service-form created successfully', [
                'form_id' => $bothServiceForm->id,
                'user_id' => $user->id,
                'services' => $validated['services_requested']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Both-service-form request submitted successfully',
                'data' => $bothServiceForm->load(['user', 'department'])
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating both-service-form', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create both-service-form request'
            ], 500);
        }
    }

    /**
     * Get forms for the current user's role
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userRole = $user->role?->name;

            if (!$userRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'User role not found'
                ], 403);
            }

            $query = ModuleAccessRequest::bothServiceForm()
                ->with(['user', 'department', 'hodUser', 'divisionalDirectorUser', 'dictUser', 'hodItUser', 'ictOfficerUser']);

            // Filter based on user role
            if (in_array($userRole, ['head_of_department'])) {
                // HOD can see forms from their department
                $query->whereHas('department', function ($q) use ($user) {
                    $q->where('hod_user_id', $user->id);
                });
            } elseif (in_array($userRole, ['divisional_director', 'ict_director', 'ict_officer'])) {
                // Other approvers can see forms in their approval stage
                $query->forRole($userRole);
            } else {
                // Regular users can only see their own forms
                $query->where('user_id', $user->id);
            }

            $forms = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $forms
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting both-service-forms', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get forms'
            ], 500);
        }
    }

    /**
     * Get a specific form with role-based access
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $form = ModuleAccessRequest::bothServiceForm()
                ->with(['user', 'department', 'hodUser', 'divisionalDirectorUser', 'dictUser', 'hodItUser', 'ictOfficerUser'])
                ->findOrFail($id);

            // Check if user can view this form
            if (!$this->canUserViewForm($user, $form)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this form'
                ], 403);
            }

            // Add role-based field access information
            $formData = $form->toArray();
            $formData['user_permissions'] = $this->getUserPermissions($user, $form);

            return response()->json([
                'success' => true,
                'data' => $formData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting both-service-form', [
                'error' => $e->getMessage(),
                'form_id' => $id,
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get form'
            ], 500);
        }
    }

    /**
     * HOD approval (required comments)
     */
    public function approveAsHOD(Request $request, int $id): JsonResponse
    {
        return $this->processApproval($request, $id, 'hod', [
            'action' => 'required|in:approve,reject',
            'comments' => 'required|string|min:10|max:1000', // HOD comments are required
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ]);
    }

    /**
     * Divisional Director approval
     */
    public function approveAsDivisionalDirector(Request $request, int $id): JsonResponse
    {
        return $this->processApproval($request, $id, 'divisional_director', [
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ]);
    }

    /**
     * DICT approval
     */
    public function approveAsDICT(Request $request, int $id): JsonResponse
    {
        return $this->processApproval($request, $id, 'dict', [
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ]);
    }

    /**
     * Head of IT approval
     */
    public function approveAsHODIT(Request $request, int $id): JsonResponse
    {
        // HOD_IT role has been removed
        return response()->json(['error' => 'HOD_IT role has been removed'], 404);
        // return $this->processApproval($request, $id, 'hod_it', [
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ]);
    }

    /**
     * ICT Officer approval (final)
     */
    public function approveAsICTOfficer(Request $request, int $id): JsonResponse
    {
        return $this->processApproval($request, $id, 'ict_officer', [
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ]);
    }

    /**
     * Generic approval processing method
     */
    private function processApproval(Request $request, int $id, string $role, array $validationRules): JsonResponse
    {
        try {
            $user = $request->user();
            $form = ModuleAccessRequest::bothServiceForm()->findOrFail($id);

            // Check if user can approve at this stage
            if (!$form->canUserApprove($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to approve at this stage'
                ], 403);
            }

            $validated = $request->validate($validationRules);

            DB::beginTransaction();

            // Handle signature upload
            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store("signatures/both-service-forms/{$role}", 'public');
            }

            // Update approval fields based on role
            $approvalStatus = $validated['action'] === 'approve' ? ModuleAccessRequest::STATUS_APPROVED : ModuleAccessRequest::STATUS_REJECTED;
            
            $form->update([
                "{$role}_approval_status" => $approvalStatus,
                "{$role}_comments" => $validated['comments'] ?? null,
                "{$role}_signature_path" => $signaturePath,
                "{$role}_approved_at" => now(),
                "{$role}_user_id" => $user->id,
            ]);

            if ($validated['action'] === 'approve') {
                $form->moveToNextStage();
            } else {
                $form->rejectRequest();
            }

            $form->save();

            DB::commit();

            Log::info("Both-service-form {$validated['action']}d by {$role}", [
                'form_id' => $form->id,
                'user_id' => $user->id,
                'role' => $role,
                'action' => $validated['action']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Form {$validated['action']}d successfully",
                'data' => $form->fresh(['user', 'department', 'hodUser', 'divisionalDirectorUser', 'dictUser', 'hodItUser', 'ictOfficerUser'])
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
            Log::error("Error processing {$role} approval", [
                'error' => $e->getMessage(),
                'form_id' => $id,
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process approval'
            ], 500);
        }
    }

    /**
     * Check if user can view the form
     */
    private function canUserViewForm(User $user, ModuleAccessRequest $form): bool
    {
        $userRole = $user->role?->name;

        // Form owner can always view
        if ($form->user_id === $user->id) {
            return true;
        }

        // HOD can view forms from their department
        if (in_array($userRole, ['head_of_department'])) {
            return $form->department && $form->department->hod_user_id === $user->id;
        }

        // Other approvers can view forms in their approval stage or completed forms
        if (in_array($userRole, ['divisional_director', 'ict_director', 'ict_officer'])) {
            return true; // They can view all forms for approval purposes
        }

        // Admin can view all
        if ($userRole === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Get user permissions for form fields
     */
    private function getUserPermissions(User $user, ModuleAccessRequest $form): array
    {
        $userRole = $user->role?->name;
        $canApprove = $form->canUserApprove($user);

        return [
            'can_edit_personal_info' => false, // Personal info is auto-populated
            'can_edit_services' => $form->user_id === $user->id && $form->overall_status === ModuleAccessRequest::STATUS_PENDING,
            'can_approve' => $canApprove,
            'approval_stage' => $form->current_approval_stage,
            'user_role' => $userRole,
            'readonly_sections' => $this->getReadonlySections($user, $form),
        ];
    }

    /**
     * Get readonly sections based on user role
     */
    private function getReadonlySections(User $user, ModuleAccessRequest $form): array
    {
        $userRole = $user->role?->name;
        $currentStage = $form->current_approval_stage;
        
        $allSections = ['hod', 'divisional_director', 'dict', 'ict_officer'];
        $readonlySections = [];

        foreach ($allSections as $section) {
            $canEditSection = false;

            // User can edit their own approval section if it's the current stage
            if ($currentStage === $section && $form->canUserApprove($user)) {
                $canEditSection = true;
            }

            if (!$canEditSection) {
                $readonlySections[] = $section;
            }
        }

        return $readonlySections;
    }

    /**
     * Get both-service-form data with specific columns for table display
     */
    public function getTableData(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userRole = $user->role?->name;

            if (!$userRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'User role not found'
                ], 403);
            }

            $query = ModuleAccessRequest::bothServiceForm()
                ->with(['user', 'department', 'hodUser', 'divisionalDirectorUser', 'dictUser', 'hodItUser', 'ictOfficerUser']);

            // Filter based on user role
            if (in_array($userRole, ['head_of_department'])) {
                // HOD can see forms from their department
                $query->whereHas('department', function ($q) use ($user) {
                    $q->where('hod_user_id', $user->id);
                });
            } elseif (in_array($userRole, ['divisional_director', 'ict_director', 'ict_officer'])) {
                // Other approvers can see forms in their approval stage
                $query->forRole($userRole);
            } else {
                // Regular users can only see their own forms
                $query->where('user_id', $user->id);
            }

            $forms = $query->orderBy('created_at', 'desc')->get();

            // Transform data to match the requested columns
            $tableData = $forms->map(function ($form) use ($user) {
                return [
                    'request_id' => $form->id,
                    'request_type' => $this->getRequestTypeDisplay($form->services_requested),
                    'personal_information' => [
                        'pf_number' => $form->pf_number,
                        'staff_name' => $form->staff_name,
                        'phone_number' => $form->phone_number,
                        'department' => $form->department?->name ?? 'N/A'
                    ],
                    'module_requested_for' => $this->getModuleRequestedFor($form),
                    'module_request' => $form->modules ?? [],
                    'access_rights' => ucfirst($form->access_type),
                    'approval' => $this->getApprovalStatus($form),
                    'comments' => $this->getAllComments($form),
                    'for_implementation' => $this->getImplementationStatus($form),
                    'submission_date' => $form->created_at->format('Y-m-d H:i:s'),
                    'current_status' => $this->getCurrentStatusDisplay($form),
                    'actions' => $this->getAvailableActions($form, $user)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tableData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting both-service-form table data', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get table data'
            ], 500);
        }
    }

    /**
     * Get form for viewing/editing by HOD
     */
    public function getFormForHOD(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $userRole = $user->role?->name;

            // Check if user is HOD
            if (!in_array($userRole, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only HOD can access this endpoint.'
                ], 403);
            }

            $form = ModuleAccessRequest::bothServiceForm()
                ->with(['user', 'department', 'hodUser', 'divisionalDirectorUser', 'dictUser', 'hodItUser', 'ictOfficerUser'])
                ->findOrFail($id);

            // Check if HOD can view this form (from their department)
            if (!$this->canUserViewForm($user, $form)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this form'
                ], 403);
            }

            // Prepare form data with HOD-specific permissions
            $formData = [
                'id' => $form->id,
                'request_id' => $form->id,
                'personal_information' => [
                    'pf_number' => $form->pf_number,
                    'staff_name' => $form->staff_name,
                    'phone_number' => $form->phone_number,
                    'department' => $form->department?->name ?? 'N/A',
                    'department_id' => $form->department_id
                ],
                'services_requested' => $form->services_requested,
                'access_type' => $form->access_type,
                'temporary_until' => $form->temporary_until,
                'modules' => $form->modules,
                'comments' => $form->comments,
                'signature_path' => $form->signature_path,
                'overall_status' => $form->overall_status,
                'current_approval_stage' => $form->current_approval_stage,
                'created_at' => $form->created_at,
                
                // HOD Approval Section
                'hod_section' => [
                    'approval_status' => $form->hod_approval_status,
                    'comments' => $form->hod_comments,
                    'signature_path' => $form->hod_signature_path,
                    'approved_at' => $form->hod_approved_at,
                    'approved_by' => $form->hodUser?->name,
                    'can_edit' => $form->canUserApprove($user) && $form->current_approval_stage === 'hod',
                    'is_required' => true
                ],
                
                // Other sections (readonly for HOD)
                'divisional_director_section' => [
                    'approval_status' => $form->divisional_director_approval_status,
                    'comments' => $form->divisional_director_comments,
                    'approved_at' => $form->divisional_director_approved_at,
                    'approved_by' => $form->divisionalDirectorUser?->name,
                    'can_edit' => false,
                    'is_readonly' => true
                ],
                'dict_section' => [
                    'approval_status' => $form->dict_approval_status,
                    'comments' => $form->dict_comments,
                    'approved_at' => $form->dict_approved_at,
                    'approved_by' => $form->dictUser?->name,
                    'can_edit' => false,
                    'is_readonly' => true
                ],

                    'can_edit' => false,
                    'is_readonly' => true
                ],
                'ict_officer_section' => [
                    'approval_status' => $form->ict_officer_approval_status,
                    'comments' => $form->ict_officer_comments,
                    'approved_at' => $form->ict_officer_approved_at,
                    'approved_by' => $form->ictOfficerUser?->name,
                    'can_edit' => false,
                    'is_readonly' => true
                ],
                
                // User permissions
                'user_permissions' => [
                    'can_approve' => $form->canUserApprove($user),
                    'can_reject' => $form->canUserApprove($user),
                    'current_user_role' => $userRole,
                    'is_hod' => true,
                    'can_submit_to_next_stage' => $form->canUserApprove($user) && $form->current_approval_stage === 'hod'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $formData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting form for HOD', [
                'error' => $e->getMessage(),
                'form_id' => $id,
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get form'
            ], 500);
        }
    }

    /**
     * HOD submit to next stage
     */
    public function hodSubmitToNextStage(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $userRole = $user->role?->name;

            // Check if user is HOD
            if (!in_array($userRole, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only HOD can submit to next stage.'
                ], 403);
            }

            $form = ModuleAccessRequest::bothServiceForm()->findOrFail($id);

            // Check if HOD can approve this form
            if (!$form->canUserApprove($user) || $form->current_approval_stage !== 'hod') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot submit to next stage. Form is not in HOD approval stage or you are not authorized.'
                ], 403);
            }

            // Validate HOD input
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'comments' => 'required|string|min:10|max:1000', // HOD comments are required
                'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
            ]);

            DB::beginTransaction();

            // Handle signature upload
            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures/both-service-forms/hod', 'public');
            }

            // Update HOD approval
            $approvalStatus = $validated['action'] === 'approve' ? ModuleAccessRequest::STATUS_APPROVED : ModuleAccessRequest::STATUS_REJECTED;
            
            $form->update([
                'hod_approval_status' => $approvalStatus,
                'hod_comments' => $validated['comments'],
                'hod_signature_path' => $signaturePath,
                'hod_approved_at' => now(),
                'hod_user_id' => $user->id,
            ]);

            if ($validated['action'] === 'approve') {
                $form->moveToNextStage();
                $message = 'Form approved and submitted to Divisional Director';
            } else {
                $form->rejectRequest();
                $message = 'Form rejected';
            }

            $form->save();

            DB::commit();

            Log::info("HOD {$validated['action']}d both-service-form", [
                'form_id' => $form->id,
                'user_id' => $user->id,
                'action' => $validated['action']
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'form_id' => $form->id,
                    'overall_status' => $form->overall_status,
                    'current_approval_stage' => $form->current_approval_stage,
                    'hod_approval_status' => $form->hod_approval_status,
                    'next_stage' => $form->current_approval_stage === 'completed' ? 'Completed' : ucfirst(str_replace('_', ' ', $form->current_approval_stage))
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
            Log::error('Error in HOD submit to next stage', [
                'error' => $e->getMessage(),
                'form_id' => $id,
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit to next stage'
            ], 500);
        }
    }

    /**
     * Helper method to get request type display
     */
    private function getRequestTypeDisplay(array $services): string
    {
        if (empty($services)) {
            return 'N/A';
        }

        $serviceNames = array_map(function($service) {
            return ModuleAccessRequest::AVAILABLE_SERVICES[$service] ?? $service;
        }, $services);

        return implode(', ', $serviceNames);
    }

    /**
     * Helper method to get module requested for
     */
    private function getModuleRequestedFor(ModuleAccessRequest $form): string
    {
        $purposes = [];
        
        if ($form->hasService('wellsoft')) {
            $purposes[] = 'Wellsoft System Access';
        }
        if ($form->hasService('jeeva')) {
            $purposes[] = 'Jeeva System Access';
        }
        if ($form->hasService('internet_access')) {
            $purposes[] = 'Internet Access';
        }

        return implode(', ', $purposes);
    }

    /**
     * Helper method to get approval status
     */
    private function getApprovalStatus(ModuleAccessRequest $form): array
    {
        return [
            'hod' => [
                'status' => $form->hod_approval_status,
                'approved_by' => $form->hodUser?->name,
                'approved_at' => $form->hod_approved_at?->format('Y-m-d H:i:s')
            ],
            'divisional_director' => [
                'status' => $form->divisional_director_approval_status,
                'approved_by' => $form->divisionalDirectorUser?->name,
                'approved_at' => $form->divisional_director_approved_at?->format('Y-m-d H:i:s')
            ],
            'dict' => [
                'status' => $form->dict_approval_status,
                'approved_by' => $form->dictUser?->name,
                'approved_at' => $form->dict_approved_at?->format('Y-m-d H:i:s')
            ],

            'ict_officer' => [
                'status' => $form->ict_officer_approval_status,
                'approved_by' => $form->ictOfficerUser?->name,
                'approved_at' => $form->ict_officer_approved_at?->format('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Helper method to get all comments
     */
    private function getAllComments(ModuleAccessRequest $form): array
    {
        $comments = [];
        
        if ($form->comments) {
            $comments['initial'] = $form->comments;
        }
        if ($form->hod_comments) {
            $comments['hod'] = $form->hod_comments;
        }
        if ($form->divisional_director_comments) {
            $comments['divisional_director'] = $form->divisional_director_comments;
        }
        if ($form->dict_comments) {
            $comments['dict'] = $form->dict_comments;
        }

        if ($form->ict_officer_comments) {
            $comments['ict_officer'] = $form->ict_officer_comments;
        }

        return $comments;
    }

    /**
     * Helper method to get implementation status
     */
    private function getImplementationStatus(ModuleAccessRequest $form): string
    {
        if ($form->overall_status === 'approved' && $form->current_approval_stage === 'completed') {
            return 'Ready for Implementation';
        } elseif ($form->overall_status === 'rejected') {
            return 'Rejected - No Implementation';
        } elseif ($form->overall_status === 'in_review') {
            return 'Pending Approval';
        } else {
            return 'Awaiting Approval';
        }
    }

    /**
     * Helper method to get current status display
     */
    private function getCurrentStatusDisplay(ModuleAccessRequest $form): string
    {
        $stage = ucfirst(str_replace('_', ' ', $form->current_approval_stage));
        $status = ucfirst($form->overall_status);
        
        if ($form->current_approval_stage === 'completed') {
            return 'Completed - ' . $status;
        }
        
        return "At {$stage} - {$status}";
    }

    /**
     * Helper method to get available actions
     */
    private function getAvailableActions(ModuleAccessRequest $form, User $user): array
    {
        $actions = ['view'];
        
        if ($form->canUserApprove($user)) {
            $actions[] = 'approve';
            $actions[] = 'reject';
        }
        
        if ($this->canUserViewForm($user, $form)) {
            $actions[] = 'export_pdf';
        }
        
        return $actions;
    }



    /**
     * Debug endpoint to check HOD department assignments
     */
    public function debugHodAssignments(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get all relevant data for debugging
            $allUsers = User::with('role')->get();
            $allDepartments = Department::with('headOfDepartment.role')->get();
            $hodUsers = User::whereHas('role', function($query) {
                $query->whereIn('name', ['head_of_department', 'hod_it', 'ict_director']);
            })->with('role')->get();
            
            $currentUserDepartment = Department::where('hod_user_id', $user->id)->first();
            
            return response()->json([
                'success' => true,
                'debug_info' => [
                    'current_user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role?->name,
                        'assigned_department' => $currentUserDepartment ? [
                            'id' => $currentUserDepartment->id,
                            'name' => $currentUserDepartment->name,
                            'code' => $currentUserDepartment->code
                        ] : null
                    ],
                    'all_users' => $allUsers->map(function($u) {
                        return [
                            'id' => $u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'role' => $u->role?->name
                        ];
                    }),
                    'all_departments' => $allDepartments->map(function($dept) {
                        return [
                            'id' => $dept->id,
                            'name' => $dept->name,
                            'code' => $dept->code,
                            'hod_user_id' => $dept->hod_user_id,
                            'hod_user' => $dept->headOfDepartment ? [
                                'id' => $dept->headOfDepartment->id,
                                'name' => $dept->headOfDepartment->name,
                                'email' => $dept->headOfDepartment->email,
                                'role' => $dept->headOfDepartment->role?->name
                            ] : null
                        ];
                    }),
                    'hod_users' => $hodUsers->map(function($hodUser) {
                        $assignedDepts = Department::where('hod_user_id', $hodUser->id)->get();
                        return [
                            'id' => $hodUser->id,
                            'name' => $hodUser->name,
                            'email' => $hodUser->email,
                            'role' => $hodUser->role->name,
                            'assigned_departments' => $assignedDepts->map(function($dept) {
                                return [
                                    'id' => $dept->id,
                                    'name' => $dept->name,
                                    'code' => $dept->code
                                ];
                            })
                        ];
                    }),
                    'statistics' => [
                        'total_users' => $allUsers->count(),
                        'total_departments' => $allDepartments->count(),
                        'hod_users_count' => $hodUsers->count(),
                        'departments_with_hod' => $allDepartments->whereNotNull('hod_user_id')->count(),
                        'departments_without_hod' => $allDepartments->whereNull('hod_user_id')->count()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debug endpoint error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export form as PDF
     */
    public function exportPdf(Request $request, int $id)
    {
        try {
            $user = $request->user();
            $form = ModuleAccessRequest::bothServiceForm()
                ->with(['user', 'department', 'hodUser', 'divisionalDirectorUser', 'dictUser', 'hodItUser', 'ictOfficerUser'])
                ->findOrFail($id);

            // Check if user can view this form
            if (!$this->canUserViewForm($user, $form)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to export this form'
                ], 403);
            }

            // Generate PDF
            $pdf = PDF::loadView('pdf.both-service-form.form', compact('form'));
            $pdf->setPaper('A4', 'portrait');

            $filename = "both-service-form-{$form->pf_number}-{$form->id}.pdf";

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error exporting both-service-form PDF', [
                'error' => $e->getMessage(),
                'form_id' => $id,
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export PDF'
            ], 500);
        }
    }
}