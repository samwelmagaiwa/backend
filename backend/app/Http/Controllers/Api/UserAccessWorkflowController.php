<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Services\UserAccessWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserAccessWorkflowController extends Controller
{
    protected UserAccessWorkflowService $workflowService;

    public function __construct(UserAccessWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of requests for approval
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'request_type', 'per_page']);
            $requests = $this->workflowService->getRequestsForApproval(auth()->user(), $filters);
            
            $transformedRequests = $requests->getCollection()->map(function ($request) {
                return $this->workflowService->transformRequestData($request);
            });
            
            $requests->setCollection($transformedRequests);
            
            return response()->json([
                'success' => true,
                'message' => 'Requests retrieved successfully',
                'data' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created request
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $userAccess = $this->workflowService->createRequest($request);
            
            return response()->json([
                'success' => true,
                'message' => 'Access request created successfully',
                'data' => $this->workflowService->transformRequestData($userAccess)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified request
     */
    public function show(UserAccess $userAccess): JsonResponse
    {
        try {
            $userAccess->load(['user', 'department']);
            
            return response()->json([
                'success' => true,
                'message' => 'Request retrieved successfully',
                'data' => $this->workflowService->transformRequestData($userAccess)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified request
     */
    public function update(Request $request, UserAccess $userAccess): JsonResponse
    {
        try {
            // Only allow updates if request is still pending
            if (!in_array($userAccess->status, ['pending', 'pending_hod'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update request that is already in approval process'
                ], 403);
            }

            $updatedRequest = $this->workflowService->updateRequest($userAccess, $request);
            
            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully',
                'data' => $this->workflowService->transformRequestData($updatedRequest)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process HOD approval/rejection
     */
    public function processHodApproval(Request $request, UserAccess $userAccess): JsonResponse
    {
        try {
            // Verify user has HOD role
            if (!auth()->user()->hasRole() || auth()->user()->role->name !== 'HEAD_OF_DEPARTMENT') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only HOD can perform this action'
                ], 403);
            }

            if (!in_array($userAccess->status, ['pending', 'pending_hod'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in HOD approval stage'
                ], 400);
            }

            $updatedRequest = $this->workflowService->processHodApproval($userAccess, $request);
            
            return response()->json([
                'success' => true,
                'message' => $request->action === 'approve' ? 'Request approved by HOD' : 'Request rejected by HOD',
                'data' => $this->workflowService->transformRequestData($updatedRequest)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process HOD approval',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process Divisional Director approval/rejection
     */
    public function processDivisionalApproval(Request $request, UserAccess $userAccess): JsonResponse
    {
        try {
            // Verify user has Divisional Director role
            if (!auth()->user()->hasRole() || auth()->user()->role->name !== 'DIVISIONAL_DIRECTOR') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only Divisional Director can perform this action'
                ], 403);
            }

            if ($userAccess->status !== 'pending_divisional') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in Divisional Director approval stage'
                ], 400);
            }

            $updatedRequest = $this->workflowService->processDivisionalApproval($userAccess, $request);
            
            return response()->json([
                'success' => true,
                'message' => $request->action === 'approve' ? 'Request approved by Divisional Director' : 'Request rejected by Divisional Director',
                'data' => $this->workflowService->transformRequestData($updatedRequest)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process Divisional approval',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process ICT Director approval/rejection
     */
    public function processIctDirectorApproval(Request $request, UserAccess $userAccess): JsonResponse
    {
        try {
            // Verify user has ICT Director role
            if (!auth()->user()->hasRole() || auth()->user()->role->name !== 'ICT_DIRECTOR') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only ICT Director can perform this action'
                ], 403);
            }

            if ($userAccess->status !== 'pending_ict_director') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in ICT Director approval stage'
                ], 400);
            }

            $updatedRequest = $this->workflowService->processIctDirectorApproval($userAccess, $request);
            
            return response()->json([
                'success' => true,
                'message' => $request->action === 'approve' ? 'Request approved by ICT Director' : 'Request rejected by ICT Director',
                'data' => $this->workflowService->transformRequestData($updatedRequest)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process ICT Director approval',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process Head IT approval/rejection
     */
    public function processHeadItApproval(Request $request, UserAccess $userAccess): JsonResponse
    {
        try {
            // Verify user has Head IT role
            if (!auth()->user()->hasRole() || auth()->user()->role->name !== 'HEAD_IT') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only Head IT can perform this action'
                ], 403);
            }

            if ($userAccess->status !== 'pending_head_it') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in Head IT approval stage'
                ], 400);
            }

            $updatedRequest = $this->workflowService->processHeadItApproval($userAccess, $request);
            
            return response()->json([
                'success' => true,
                'message' => $request->action === 'approve' ? 'Request approved by Head IT' : 'Request rejected by Head IT',
                'data' => $this->workflowService->transformRequestData($updatedRequest)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process Head IT approval',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process ICT Officer implementation
     */
    public function processIctOfficerImplementation(Request $request, UserAccess $userAccess): JsonResponse
    {
        try {
            // Verify user has ICT Officer role
            if (!auth()->user()->hasRole() || auth()->user()->role->name !== 'ICT_OFFICER') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only ICT Officer can perform this action'
                ], 403);
            }

            if ($userAccess->status !== 'pending_ict_officer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in ICT Officer implementation stage'
                ], 400);
            }

            $updatedRequest = $this->workflowService->processIctOfficerImplementation($userAccess, $request);
            
            return response()->json([
                'success' => true,
                'message' => 'Request implemented by ICT Officer',
                'data' => $this->workflowService->transformRequestData($updatedRequest)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process ICT Officer implementation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->workflowService->getStatistics(auth()->user());
            
            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available request types and modules
     */
    public function getFormOptions(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Form options retrieved successfully',
                'data' => [
                    'request_types' => UserAccess::REQUEST_TYPES,
                    'access_types' => UserAccess::ACCESS_TYPES,
                    'statuses' => UserAccess::STATUSES,
                    'wellsoft_modules' => [
                        'Registrar',
                        'Specialist',
                        'Cashier',
                        'Resident Nurse',
                        'Intern Doctor',
                        'Intern Nurse',
                        'Medical Recorder',
                        'Social Worker',
                        'Quality Officer',
                        'Administrator',
                        'Health Attendant'
                    ],
                    'jeeva_modules' => [
                        'FINANCIAL ACCOUNTING',
                        'DOCTOR CONSULTATION',
                        'MEDICAL RECORDS',
                        'OUTPATIENT',
                        'NURSING STATION',
                        'INPATIENT',
                        'IP CASHIER',
                        'HIV',
                        'LINEN & LAUNDRY',
                        'FIXED ASSETS',
                        'PMTCT',
                        'PHARMACY',
                        'BILL NOTE',
                        'BLOOD BANK',
                        'ORDER MANAGEMENT',
                        'PRIVATE CREDIT',
                        'LABORATORY',
                        'GENERAL STORE',
                        'IP BILLING',
                        'RADIOLOGY',
                        'PURCHASE',
                        'SCROLLING',
                        'OPERATION THEATRE',
                        'CSSD',
                        'WEB INDENT',
                        'MORTUARY',
                        'GENERAL MAINTENANCE',
                        'PERSONNEL',
                        'MAINTENANCE',
                        'PAYROLL',
                        'CMS',
                        'MIS STATISTICS'
                    ],
                    'internet_purposes' => [
                        'Purpose 1',
                        'Purpose 2',
                        'Purpose 3',
                        'Purpose 4'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve form options',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a request
     */
    public function cancel(Request $request, UserAccess $userAccess): JsonResponse
    {
        try {
            $request->validate([
                'cancellation_reason' => 'required|string|max:1000'
            ]);

            // Only allow cancellation by request owner or admin
            if (auth()->id() !== $userAccess->user_id && !auth()->user()->hasRole('ADMIN')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this request'
                ], 403);
            }

            $userAccess->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Request cancelled successfully',
                'data' => $this->workflowService->transformRequestData($userAccess->fresh())
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export requests to Excel/CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            // This would typically generate and return a file
            // For now, return success message
            return response()->json([
                'success' => true,
                'message' => 'Export functionality to be implemented',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
