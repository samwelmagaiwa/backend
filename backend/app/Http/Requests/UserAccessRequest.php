<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAccessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
            'digital_signature' => [
                'nullable', // Made nullable since we'll auto-load from storage
                'file',
                'mimes:png,jpg,jpeg',
                'max:5120', // 5MB max
            ],
            'request_type' => [
                'required',
                'array',
                'min:1',
            ],
            'request_type.*' => [
                'required',
                'string',
                Rule::in(['jeeva_access', 'wellsoft', 'internet_access_request']),
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
            'pf_number.regex' => 'PF Number format is invalid.',
            'staff_name.required' => 'Staff name is required.',
            'staff_name.regex' => 'Staff name contains invalid characters.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.regex' => 'Phone number format is invalid.',
            'phone_number.min' => 'Phone number must be at least 10 digits.',
            'department_id.required' => 'Department selection is required.',
            'department_id.exists' => 'Selected department does not exist.',
            'digital_signature.mimes' => 'Digital signature must be a PNG, JPG, or JPEG file.',
            'digital_signature.max' => 'Digital signature file size must not exceed 5MB.',
            'request_type.required' => 'At least one request type must be selected.',
            'request_type.min' => 'At least one request type must be selected.',
            'request_type.*.in' => 'Invalid request type selected.',
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
            'digital_signature' => 'Digital Signature',
            'request_type' => 'Request Type',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure request_type is always an array
        if ($this->has('request_type') && !is_array($this->request_type)) {
            $this->merge([
                'request_type' => [$this->request_type]
            ]);
        }

        // Clean and format the data
        $this->merge([
            'pf_number' => $this->cleanPfNumber($this->pf_number),
            'staff_name' => $this->cleanStaffName($this->staff_name),
            'phone_number' => $this->cleanPhoneNumber($this->phone_number),
        ]);
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
}