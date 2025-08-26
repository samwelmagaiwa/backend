<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeclarationController extends Controller
{
    /**
     * Get all departments for the declaration form
     */
    public function getDepartments()
    {
        try {
            $departments = Department::active()
                ->select('id', 'name', 'code', 'description')
                ->orderBy('name')
                ->get()
                ->map(function ($department) {
                    return [
                        'id' => $department->id,
                        'name' => $department->name,
                        'code' => $department->code,
                        'full_name' => $department->full_name,
                        'description' => $department->description,
                        'color' => 'blue', // As requested - all departments should be blue
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $departments
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching departments for declaration', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch departments'
            ], 500);
        }
    }

    /**
     * Submit declaration form
     */
    public function store(Request $request)
    {
        try {
            // Handle both direct form data and nested declaration_data structure
            $formData = $request->has('declaration_data') ? $request->declaration_data : $request->all();
            
            // Log received data for debugging
            Log::info('Declaration form data received', [
                'user_id' => $request->user()->id,
                'form_data' => $formData,
                'agreement_type' => gettype($formData['agreement'] ?? null),
                'agreement_value' => $formData['agreement'] ?? null
            ]);
            
            // Convert string boolean values to actual booleans (for FormData)
            if (isset($formData['agreement'])) {
                if (is_string($formData['agreement'])) {
                    $originalValue = $formData['agreement'];
                    // Handle different string representations of boolean
                    if (in_array(strtolower($formData['agreement']), ['true', '1', 'yes', 'on'])) {
                        $formData['agreement'] = true;
                    } elseif (in_array(strtolower($formData['agreement']), ['false', '0', 'no', 'off', ''])) {
                        $formData['agreement'] = false;
                    } else {
                        $formData['agreement'] = filter_var($formData['agreement'], FILTER_VALIDATE_BOOLEAN);
                    }
                    
                    Log::info('Converted agreement value', [
                        'original' => $originalValue,
                        'converted' => $formData['agreement'],
                        'type' => gettype($formData['agreement'])
                    ]);
                }
            }
            
            $validator = Validator::make($formData, [
                'fullName' => 'required|string|max:255',
                'pfNumber' => [
                    'required',
                    'string',
                    'max:100',
                    // Check if PF number is unique across all declarations
                    function ($attribute, $value, $fail) use ($request) {
                        $existingDeclaration = Declaration::where('pf_number', $value)
                            ->where('user_id', '!=', $request->user()->id)
                            ->first();
                        
                        if ($existingDeclaration) {
                            $fail('This PF number has already been used in another declaration.');
                        }
                    }
                ],
                'department' => 'required|string|max:255',
                'jobTitle' => 'required|string|max:255',
                'date' => 'required|date',
                'agreement' => 'required|boolean|accepted',
            ], [
                'pfNumber.required' => 'PF Number is required.',
                'pfNumber.max' => 'PF Number must not exceed 100 characters.',
                'agreement.accepted' => 'You must confirm the declaration statement.',
                'fullName.required' => 'Full Name is required.',
                'department.required' => 'Department is required.',
                'jobTitle.required' => 'Job Title is required.',
                'date.required' => 'Signature Date is required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Handle signature file if present
            $signatureInfo = null;
            if ($request->hasFile('signature')) {
                $signatureInfo = $this->handleSignatureUpload($request->file('signature'), $user->id);
            }

            // Use database transaction to ensure data consistency
            DB::beginTransaction();

            try {
                // Check if user already has a declaration and update it, or create new one
                $declaration = Declaration::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'full_name' => $formData['fullName'],
                        'pf_number' => $formData['pfNumber'],
                        'department' => $formData['department'],
                        'job_title' => $formData['jobTitle'],
                        'signature_date' => $formData['date'],
                        'agreement_accepted' => $formData['agreement'],
                        'signature_info' => $signatureInfo,
                        'submitted_at' => now(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]
                );

                // Update user's onboarding status
                $onboarding = $user->getOrCreateOnboarding();
                $onboarding->submitDeclaration([
                    'declaration_id' => $declaration->id,
                    'full_name' => $formData['fullName'],
                    'pf_number' => $formData['pfNumber'],
                    'department' => $formData['department'],
                    'job_title' => $formData['jobTitle'],
                    'signature_date' => $formData['date'],
                    'agreement_accepted' => $formData['agreement'],
                    'signature_info' => $signatureInfo,
                    'submitted_at' => now()->toISOString(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                DB::commit();

                Log::info('Declaration submitted successfully', [
                    'user_id' => $user->id,
                    'declaration_id' => $declaration->id,
                    'pf_number' => $formData['pfNumber']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Declaration submitted successfully',
                    'data' => [
                        'declaration_id' => $declaration->id,
                        'current_step' => $onboarding->current_step,
                        'next_step' => $onboarding->getNextStep(),
                        'declaration_submitted_at' => $onboarding->declaration_submitted_at,
                        'ready_for_completion' => $this->isReadyForCompletion($onboarding)
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error submitting declaration', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit declaration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's declaration
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            $declaration = Declaration::where('user_id', $user->id)->first();

            if (!$declaration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Declaration not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $declaration->id,
                    'full_name' => $declaration->full_name,
                    'pf_number' => $declaration->pf_number,
                    'department' => $declaration->department,
                    'job_title' => $declaration->job_title,
                    'signature_date' => $declaration->signature_date->format('Y-m-d'),
                    'agreement_accepted' => $declaration->agreement_accepted,
                    'has_signature' => $declaration->hasSignature(),
                    'signature_url' => $declaration->signature_url,
                    'submitted_at' => $declaration->formatted_submitted_at,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching declaration', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch declaration'
            ], 500);
        }
    }

    /**
     * Check if PF number is available
     */
    public function checkPfNumber(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pf_number' => 'required|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PF number format',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pfNumber = $request->pf_number;
            $user = $request->user();

            // Check if PF number exists for other users
            $existingDeclaration = Declaration::where('pf_number', $pfNumber)
                ->where('user_id', '!=', $user->id)
                ->first();

            $isAvailable = !$existingDeclaration;

            return response()->json([
                'success' => true,
                'data' => [
                    'pf_number' => $pfNumber,
                    'is_available' => $isAvailable,
                    'message' => $isAvailable 
                        ? 'PF number is available' 
                        : 'PF number is already in use'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking PF number', [
                'pf_number' => $request->pf_number ?? null,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check PF number availability'
            ], 500);
        }
    }

    /**
     * Handle signature file upload
     */
    private function handleSignatureUpload($file, $userId)
    {
        try {
            // Validate file
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception('Invalid file type. Only PNG, JPG, JPEG, and PDF files are allowed.');
            }

            // Validate file size (max 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                throw new \Exception('File size must be less than 5MB.');
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = 'declaration_signature_' . $userId . '_' . time() . '.' . $extension;
            
            // Store file in storage/app/public/signatures/declarations
            $path = $file->storeAs('public/signatures/declarations', $filename);

            return [
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'path' => str_replace('public/', '', $path), // Remove 'public/' for URL generation
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Error uploading declaration signature', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if onboarding is ready for completion
     */
    private function isReadyForCompletion($onboarding)
    {
        return $onboarding->terms_accepted && 
               $onboarding->ict_policy_accepted && 
               $onboarding->declaration_submitted;
    }

    /**
     * Get all declarations (Admin only)
     */
    public function index(Request $request)
    {
        try {
            // Check if user is admin
            $user = $request->user();
            if (!$user->role || $user->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $declarations = Declaration::with('user:id,name,email')
                ->orderBy('submitted_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $declarations
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching declarations', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch declarations'
            ], 500);
        }
    }
}