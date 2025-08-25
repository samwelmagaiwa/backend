<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserOnboarding;
use App\Models\Declaration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OnboardingController extends Controller
{
    /**
     * Get user's onboarding status
     */
    public function getStatus(Request $request)
    {
        try {
            $user = $request->user();
            $onboarding = $user->getOrCreateOnboarding();

            return response()->json([
                'success' => true,
                'data' => [
                    'needs_onboarding' => $user->needsOnboarding(),
                    'current_step' => $onboarding->current_step,
                    'progress' => [
                        'terms_accepted' => $onboarding->terms_accepted,
                        'terms_accepted_at' => $onboarding->terms_accepted_at,
                        'ict_policy_accepted' => $onboarding->ict_policy_accepted,
                        'ict_policy_accepted_at' => $onboarding->ict_policy_accepted_at,
                        'declaration_submitted' => $onboarding->declaration_submitted,
                        'declaration_submitted_at' => $onboarding->declaration_submitted_at,
                        'completed' => $onboarding->completed,
                        'completed_at' => $onboarding->completed_at,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting onboarding status', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get onboarding status'
            ], 500);
        }
    }

    /**
     * Accept terms of service
     */
    public function acceptTerms(Request $request)
    {
        try {
            $user = $request->user();
            $onboarding = $user->getOrCreateOnboarding();

            $onboarding->acceptTerms();

            Log::info('User accepted terms', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Terms accepted successfully',
                'data' => [
                    'current_step' => $onboarding->current_step,
                    'next_step' => $onboarding->getNextStep()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting terms', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept terms'
            ], 500);
        }
    }

    /**
     * Accept ICT policy
     */
    public function acceptIctPolicy(Request $request)
    {
        try {
            $user = $request->user();
            $onboarding = $user->getOrCreateOnboarding();

            $onboarding->acceptIctPolicy();

            Log::info('User accepted ICT policy', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'ICT policy accepted successfully',
                'data' => [
                    'current_step' => $onboarding->current_step,
                    'next_step' => $onboarding->getNextStep()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting ICT policy', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept ICT policy'
            ], 500);
        }
    }

    /**
     * Submit declaration form
     * Note: This method now redirects to the DeclarationController for better separation of concerns
     */
    public function submitDeclaration(Request $request)
    {
        try {
            // Handle both direct form data and nested declaration_data structure
            $formData = $request->has('declaration_data') ? $request->declaration_data : $request->all();
            
            // Log received data for debugging
            Log::info('Declaration form data received via onboarding', [
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
                // Create or update declaration record
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

                // Update onboarding status
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

                Log::info('User submitted declaration via onboarding', [
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

        } catch (\Exception $e) {
            Log::error('Error submitting declaration via onboarding', [
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
            Log::error('Error uploading signature', [
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
     * Complete onboarding process
     */
    public function complete(Request $request)
    {
        try {
            $user = $request->user();
            $onboarding = $user->getOrCreateOnboarding();

            // Verify all steps are completed
            if (!$onboarding->terms_accepted || !$onboarding->ict_policy_accepted || !$onboarding->declaration_submitted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot complete onboarding. Some steps are missing.',
                    'missing_steps' => [
                        'terms_accepted' => !$onboarding->terms_accepted,
                        'ict_policy_accepted' => !$onboarding->ict_policy_accepted,
                        'declaration_submitted' => !$onboarding->declaration_submitted,
                    ]
                ], 400);
            }

            $onboarding->complete();

            Log::info('User completed onboarding', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Onboarding completed successfully',
                'data' => [
                    'completed' => true,
                    'completed_at' => $onboarding->completed_at,
                    'needs_onboarding' => false
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing onboarding', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete onboarding'
            ], 500);
        }
    }

    /**
     * Update current step
     */
    public function updateStep(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'step' => 'required|string|in:terms-popup,terms-page,policy-popup,policy-page,declaration,success'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid step provided',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $onboarding = $user->getOrCreateOnboarding();

            $onboarding->update(['current_step' => $request->step]);

            return response()->json([
                'success' => true,
                'message' => 'Step updated successfully',
                'data' => [
                    'current_step' => $onboarding->current_step
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating onboarding step', [
                'user_id' => $request->user()->id,
                'step' => $request->step,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update step'
            ], 500);
        }
    }

    /**
     * Reset onboarding progress (Admin only)
     */
    public function reset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'type' => 'sometimes|string|in:terms,ict,all'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if current user is admin
            $currentUser = $request->user();
            if (!$currentUser->role || $currentUser->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $targetUserId = $request->user_id;
            $resetType = $request->type ?? 'all';

            $onboarding = UserOnboarding::where('user_id', $targetUserId)->first();
            
            if (!$onboarding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Onboarding record not found for user'
                ], 404);
            }

            $onboarding->reset($resetType);

            Log::info('Admin reset user onboarding', [
                'admin_id' => $currentUser->id,
                'target_user_id' => $targetUserId,
                'reset_type' => $resetType
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Onboarding reset successfully',
                'data' => [
                    'current_step' => $onboarding->current_step,
                    'reset_type' => $resetType
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting onboarding', [
                'admin_id' => $request->user()->id,
                'target_user_id' => $request->user_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset onboarding'
            ], 500);
        }
    }
}