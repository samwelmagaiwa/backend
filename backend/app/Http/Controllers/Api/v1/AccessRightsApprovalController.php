<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AccessRightsApprovalController extends Controller
{
    /**
     * Store access rights and approval workflow data.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                'user_access_id' => 'required|exists:user_access,id',
                
                // Access Rights validation
                'access_type' => 'required|string|in:permanent,temporary',
                'temporary_until' => 'required_if:access_type,temporary|date|after:today',
                
                // HoD/BM approval validation
                'hod_name' => 'sometimes|string|max:255',
                'hod_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'hod_date' => 'sometimes|date',
                'hod_comments' => 'sometimes|string|max:1000',
                
                // Divisional Director approval validation
                'divisional_director_name' => 'sometimes|string|max:255',
                'divisional_director_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'divisional_director_date' => 'sometimes|date',
                'divisional_director_comments' => 'sometimes|string|max:1000',
                
                // ICT Director approval validation
                'ict_director_name' => 'sometimes|string|max:255',
                'ict_director_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'ict_director_date' => 'sometimes|date',
                'ict_director_comments' => 'sometimes|string|max:1000',
            ], [
                'access_type.required' => 'Please select an access type (Permanent or Temporary).',
                'access_type.in' => 'Access type must be either permanent or temporary.',
                'temporary_until.required_if' => 'Please specify the temporary access end date.',
                'temporary_until.after' => 'Temporary access end date must be in the future.',
                '*.mimes' => 'Signature files must be in JPEG, JPG, PNG, or PDF format.',
                '*.max' => 'Signature files must not exceed 2MB.',
            ]);

            Log::info('🔄 PROCESSING ACCESS RIGHTS AND APPROVAL WORKFLOW', [
                'user_id' => $user->id,
                'user_access_id' => $validatedData['user_access_id'],
                'access_type' => $validatedData['access_type'],
                'has_hod_data' => isset($validatedData['hod_name']),
                'has_divisional_data' => isset($validatedData['divisional_director_name']),
                'has_ict_director_data' => isset($validatedData['ict_director_name'])
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($validatedData['user_access_id']);
            
            // Check if the user owns this request or has permission to modify it
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director', 'ict_director'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to modify this request.'
                ], 403);
            }

            // Prepare update data
            $updateData = [
                'access_type' => $validatedData['access_type'],
                'temporary_until' => $validatedData['access_type'] === 'temporary' ? $validatedData['temporary_until'] : null,
            ];

            // Handle HoD approval data
            if (isset($validatedData['hod_name'])) {
                $updateData['hod_name'] = $validatedData['hod_name'];
                $updateData['hod_comments'] = $validatedData['hod_comments'] ?? null;
                $updateData['hod_approved_at'] = isset($validatedData['hod_date']) ? $validatedData['hod_date'] : now();
                
                // Handle HoD signature upload
                if ($request->hasFile('hod_signature')) {
                    $hodSignaturePath = $this->handleSignatureUpload(
                        $request->file('hod_signature'),
                        'hod',
                        $userAccess->pf_number
                    );
                    $updateData['hod_signature_path'] = $hodSignaturePath;
                }
            }

            // Handle Divisional Director approval data
            if (isset($validatedData['divisional_director_name'])) {
                $updateData['divisional_director_name'] = $validatedData['divisional_director_name'];
                $updateData['divisional_director_comments'] = $validatedData['divisional_director_comments'] ?? null;
                $updateData['divisional_approved_at'] = isset($validatedData['divisional_director_date']) ? $validatedData['divisional_director_date'] : now();
                
                // Handle Divisional Director signature upload
                if ($request->hasFile('divisional_director_signature')) {
                    $divisionalSignaturePath = $this->handleSignatureUpload(
                        $request->file('divisional_director_signature'),
                        'divisional_director',
                        $userAccess->pf_number
                    );
                    $updateData['divisional_director_signature_path'] = $divisionalSignaturePath;
                }
            }

            // Handle ICT Director approval data
            if (isset($validatedData['ict_director_name'])) {
                $updateData['ict_director_name'] = $validatedData['ict_director_name'];
                $updateData['ict_director_comments'] = $validatedData['ict_director_comments'] ?? null;
                $updateData['ict_director_approved_at'] = isset($validatedData['ict_director_date']) ? $validatedData['ict_director_date'] : now();
                
                // Handle ICT Director signature upload
                if ($request->hasFile('ict_director_signature')) {
                    $ictDirectorSignaturePath = $this->handleSignatureUpload(
                        $request->file('ict_director_signature'),
                        'ict_director',
                        $userAccess->pf_number
                    );
                    $updateData['ict_director_signature_path'] = $ictDirectorSignaturePath;
                }
            }

            // Update the user access record
            $userAccess->update($updateData);

            Log::info('✅ ACCESS RIGHTS AND APPROVAL WORKFLOW STORED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'access_type' => $userAccess->access_type,
                'temporary_until' => $userAccess->temporary_until,
                'hod_approved' => !empty($userAccess->hod_name),
                'divisional_approved' => !empty($userAccess->divisional_director_name),
                'ict_director_approved' => !empty($userAccess->ict_director_name)
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Access rights and approval workflow stored successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'access_type' => $userAccess->access_type,
                    'temporary_until' => $userAccess->temporary_until,
                    'approval_status' => [
                        'hod_approved' => !empty($userAccess->hod_name),
                        'hod_approved_at' => $userAccess->hod_approved_at,
                        'divisional_approved' => !empty($userAccess->divisional_director_name),
                        'divisional_approved_at' => $userAccess->divisional_approved_at,
                        'ict_director_approved' => !empty($userAccess->ict_director_name),
                        'ict_director_approved_at' => $userAccess->ict_director_approved_at,
                    ],
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
            
            Log::error('Error storing access rights and approval workflow', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'request_data' => $request->except(['hod_signature', 'divisional_director_signature', 'ict_director_signature'])
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store access rights and approval workflow.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get access rights and approval workflow details for a specific user access record.
     */
    public function show(int $userAccessId): JsonResponse
    {
        try {
            $userAccess = UserAccess::with(['user', 'department'])
                                  ->findOrFail($userAccessId);

            $user = request()->user();
            
            // Check if the user has permission to view this request
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director', 'ict_director'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to view this request.'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'access_rights' => [
                        'access_type' => $userAccess->access_type,
                        'temporary_until' => $userAccess->temporary_until,
                    ],
                    'approval_workflow' => [
                        'hod' => [
                            'name' => $userAccess->hod_name,
                            'signature_path' => $userAccess->hod_signature_path,
                            'approved_at' => $userAccess->hod_approved_at,
                            'comments' => $userAccess->hod_comments,
                        ],
                        'divisional_director' => [
                            'name' => $userAccess->divisional_director_name,
                            'signature_path' => $userAccess->divisional_director_signature_path,
                            'approved_at' => $userAccess->divisional_approved_at,
                            'comments' => $userAccess->divisional_director_comments,
                        ],
                        'ict_director' => [
                            'name' => $userAccess->ict_director_name,
                            'signature_path' => $userAccess->ict_director_signature_path,
                            'approved_at' => $userAccess->ict_director_approved_at,
                            'comments' => $userAccess->ict_director_comments,
                        ],
                    ],
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
            Log::error('Error fetching access rights and approval workflow details', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch access rights and approval workflow details.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle signature file upload.
     */
    private function handleSignatureUpload($file, string $type, string $pfNumber): string
    {
        $filename = "{$type}_signature_{$pfNumber}_" . time() . "." . $file->getClientOriginalExtension();
        $path = $file->storeAs("signatures/{$type}", $filename, 'public');
        
        Log::info("✅ Signature uploaded successfully", [
            'type' => $type,
            'filename' => $filename,
            'path' => $path
        ]);
        
        return $path;
    }

    /**
     * Update an existing access rights and approval workflow.
     */
    public function update(Request $request, int $userAccessId): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                // Access Rights validation
                'access_type' => 'required|string|in:permanent,temporary',
                'temporary_until' => 'required_if:access_type,temporary|date|after:today',
                
                // HoD/BM approval validation
                'hod_name' => 'sometimes|string|max:255',
                'hod_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'hod_date' => 'sometimes|date',
                'hod_comments' => 'sometimes|string|max:1000',
                
                // Divisional Director approval validation
                'divisional_director_name' => 'sometimes|string|max:255',
                'divisional_director_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'divisional_director_date' => 'sometimes|date',
                'divisional_director_comments' => 'sometimes|string|max:1000',
                
                // ICT Director approval validation
                'ict_director_name' => 'sometimes|string|max:255',
                'ict_director_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'ict_director_date' => 'sometimes|date',
                'ict_director_comments' => 'sometimes|string|max:1000',
            ], [
                'access_type.required' => 'Please select an access type (Permanent or Temporary).',
                'access_type.in' => 'Access type must be either permanent or temporary.',
                'temporary_until.required_if' => 'Please specify the temporary access end date.',
                'temporary_until.after' => 'Temporary access end date must be in the future.',
                '*.mimes' => 'Signature files must be in JPEG, JPG, PNG, or PDF format.',
                '*.max' => 'Signature files must not exceed 2MB.',
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Check if the user owns this request or has permission to modify it
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director', 'ict_director'])) {
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

            Log::info('🔄 UPDATING ACCESS RIGHTS AND APPROVAL WORKFLOW', [
                'user_id' => $user->id,
                'user_access_id' => $userAccessId,
                'access_type' => $validatedData['access_type']
            ]);

            // Prepare update data (same logic as store method)
            $updateData = [
                'access_type' => $validatedData['access_type'],
                'temporary_until' => $validatedData['access_type'] === 'temporary' ? $validatedData['temporary_until'] : null,
            ];

            // Handle HoD approval data
            if (isset($validatedData['hod_name'])) {
                $updateData['hod_name'] = $validatedData['hod_name'];
                $updateData['hod_comments'] = $validatedData['hod_comments'] ?? null;
                $updateData['hod_approved_at'] = isset($validatedData['hod_date']) ? $validatedData['hod_date'] : now();
                
                if ($request->hasFile('hod_signature')) {
                    $hodSignaturePath = $this->handleSignatureUpload(
                        $request->file('hod_signature'),
                        'hod',
                        $userAccess->pf_number
                    );
                    $updateData['hod_signature_path'] = $hodSignaturePath;
                }
            }

            // Handle Divisional Director approval data
            if (isset($validatedData['divisional_director_name'])) {
                $updateData['divisional_director_name'] = $validatedData['divisional_director_name'];
                $updateData['divisional_director_comments'] = $validatedData['divisional_director_comments'] ?? null;
                $updateData['divisional_approved_at'] = isset($validatedData['divisional_director_date']) ? $validatedData['divisional_director_date'] : now();
                
                if ($request->hasFile('divisional_director_signature')) {
                    $divisionalSignaturePath = $this->handleSignatureUpload(
                        $request->file('divisional_director_signature'),
                        'divisional_director',
                        $userAccess->pf_number
                    );
                    $updateData['divisional_director_signature_path'] = $divisionalSignaturePath;
                }
            }

            // Handle ICT Director approval data
            if (isset($validatedData['ict_director_name'])) {
                $updateData['ict_director_name'] = $validatedData['ict_director_name'];
                $updateData['ict_director_comments'] = $validatedData['ict_director_comments'] ?? null;
                $updateData['ict_director_approved_at'] = isset($validatedData['ict_director_date']) ? $validatedData['ict_director_date'] : now();
                
                if ($request->hasFile('ict_director_signature')) {
                    $ictDirectorSignaturePath = $this->handleSignatureUpload(
                        $request->file('ict_director_signature'),
                        'ict_director',
                        $userAccess->pf_number
                    );
                    $updateData['ict_director_signature_path'] = $ictDirectorSignaturePath;
                }
            }

            // Update the user access record
            $userAccess->update($updateData);

            Log::info('✅ ACCESS RIGHTS AND APPROVAL WORKFLOW UPDATED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'access_type' => $userAccess->access_type
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Access rights and approval workflow updated successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'access_type' => $userAccess->access_type,
                    'temporary_until' => $userAccess->temporary_until,
                    'approval_status' => [
                        'hod_approved' => !empty($userAccess->hod_name),
                        'hod_approved_at' => $userAccess->hod_approved_at,
                        'divisional_approved' => !empty($userAccess->divisional_director_name),
                        'divisional_approved_at' => $userAccess->divisional_approved_at,
                        'ict_director_approved' => !empty($userAccess->ict_director_name),
                        'ict_director_approved_at' => $userAccess->ict_director_approved_at,
                    ],
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
            
            Log::error('Error updating access rights and approval workflow', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'user_access_id' => $userAccessId,
                'request_data' => $request->except(['hod_signature', 'divisional_director_signature', 'ict_director_signature'])
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update access rights and approval workflow.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}