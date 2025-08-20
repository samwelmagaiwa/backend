<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserWellsoftFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        
        // Check if user has a role and it's 'staff'
        return $user && 
               $user->role && 
               $user->role->name === 'staff';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pf_number' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\-\/]+$/', // Allow alphanumeric, hyphens, and slashes
            ],
            'staff_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/', // Allow letters, spaces, dots, hyphens, apostrophes
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/', // Allow international format
                'min:10',
                'max:20',
            ],
            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],
            'signature' => [
                'nullable', // Made nullable since we'll auto-load from storage if available
                'file',
                'mimes:png,jpg,jpeg',
                'max:2048', // 2MB max for user form
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pf_number.required' => 'PF Number is required.',
            'pf_number.regex' => 'PF Number format is invalid. Only letters, numbers, hyphens, and slashes are allowed.',
            'pf_number.max' => 'PF Number cannot exceed 50 characters.',
            
            'staff_name.required' => 'Staff name is required.',
            'staff_name.regex' => 'Staff name contains invalid characters. Only letters, spaces, dots, hyphens, and apostrophes are allowed.',
            'staff_name.max' => 'Staff name cannot exceed 255 characters.',
            
            'phone_number.required' => 'Phone number is required.',
            'phone_number.regex' => 'Phone number format is invalid.',
            'phone_number.min' => 'Phone number must be at least 10 digits.',
            'phone_number.max' => 'Phone number cannot exceed 20 characters.',
            
            'department_id.required' => 'Department selection is required.',
            'department_id.exists' => 'Selected department does not exist.',
            'department_id.integer' => 'Invalid department selection.',
            
            'signature.mimes' => 'Digital signature must be a PNG, JPG, or JPEG file.',
            'signature.max' => 'Digital signature file size must not exceed 2MB.',
            'signature.file' => 'Digital signature must be a valid file.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'pf_number' => 'PF Number',
            'staff_name' => 'Staff Name',
            'phone_number' => 'Phone Number',
            'department_id' => 'Department',
            'signature' => 'Digital Signature',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format the data
        $this->merge([
            'pf_number' => $this->cleanPfNumber($this->pf_number),
            'staff_name' => $this->cleanStaffName($this->staff_name),
            'phone_number' => $this->cleanPhoneNumber($this->phone_number),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic
            $this->validatePfNumberOwnership($validator);
            $this->validateSignatureRequirement($validator);
        });
    }

    /**
     * Validate that PF number belongs to the authenticated user.
     */
    private function validatePfNumberOwnership($validator): void
    {
        $user = auth()->user();
        $pfNumber = $this->pf_number;

        // If user has a PF number in their profile, it must match
        // But allow users to set their PF number if they don't have one
        if ($user->pf_number && trim($user->pf_number) !== '' && $user->pf_number !== $pfNumber) {
            $validator->errors()->add('pf_number', 'PF Number does not match your profile. Your profile PF Number: ' . $user->pf_number);
        }
        
        // If user doesn't have a PF number, update their profile with the submitted one
        if (!$user->pf_number || trim($user->pf_number) === '') {
            // This will be handled in the controller after validation passes
            // Just log for debugging
            \Log::info('User does not have PF number in profile, will update after validation', [
                'user_id' => $user->id,
                'submitted_pf' => $pfNumber
            ]);
        }
    }

    /**
     * Validate signature requirement.
     */
    private function validateSignatureRequirement($validator): void
    {
        $pfNumber = $this->pf_number;
        
        // Check if signature exists in storage or is being uploaded
        $hasUploadedSignature = $this->hasFile('signature');
        
        if (!$hasUploadedSignature && $pfNumber) {
            // Check if signature exists in storage
            $signatureService = app(\App\Services\SignatureService::class);
            $existingSignature = $signatureService->findExistingSignature($pfNumber);
            
            if (!$existingSignature) {
                $validator->errors()->add('signature', 'Digital signature is required. Please upload your signature file.');
            }
        }
    }

    /**
     * Clean PF Number format.
     */
    private function cleanPfNumber(?string $pfNumber): ?string
    {
        if (!$pfNumber) return null;
        
        return strtoupper(trim($pfNumber));
    }

    /**
     * Clean staff name format.
     */
    private function cleanStaffName(?string $staffName): ?string
    {
        if (!$staffName) return null;
        
        // Capitalize each word properly
        return ucwords(strtolower(trim($staffName)));
    }

    /**
     * Clean phone number format.
     */
    private function cleanPhoneNumber(?string $phoneNumber): ?string
    {
        if (!$phoneNumber) return null;
        
        // Remove extra spaces and format consistently
        return preg_replace('/\s+/', ' ', trim($phoneNumber));
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function failedAuthorization()
    {
        return response()->json([
            'success' => false,
            'message' => 'You are not authorized to submit Wellsoft access requests.',
            'error' => 'Only staff members can submit Wellsoft access requests.'
        ], 403);
    }
}