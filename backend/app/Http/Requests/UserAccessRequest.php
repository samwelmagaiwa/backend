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
        $rules = [
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
                'required',
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
                'in:jeeva_access,wellsoft,internet_access_request',
            ],
        ];

        // Internet purposes validation will be handled in withValidator method
        // after prepareForValidation has run
        $rules['internetPurposes'] = [
            'nullable',
            'array',
            'max:4',
        ];
        $rules['internetPurposes.*'] = [
            'nullable',
            'string',
            'max:255',
        ];
        
        // \u2705 ADD MODULE SELECTION VALIDATION RULES
        $rules['selectedWellsoft'] = ['nullable', 'array'];
        $rules['selectedWellsoft.*'] = ['string', 'max:255'];
        $rules['selectedJeeva'] = ['nullable', 'array'];
        $rules['selectedJeeva.*'] = ['string', 'max:255'];
        $rules['wellsoftRequestType'] = ['nullable', 'string', 'in:use,revoke'];
        
        // \u2705 ADD ACCESS TYPE VALIDATION RULES
        $rules['accessType'] = ['nullable', 'string', 'in:permanent,temporary'];
        $rules['temporaryUntil'] = ['nullable', 'date', 'after:today'];
        
        // \u2705 ADD APPROVAL DATA VALIDATION RULES
        $rules['hodName'] = ['nullable', 'string', 'max:255'];
        $rules['hodSignature'] = ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'];
        $rules['hodDate'] = ['nullable', 'date'];
        $rules['hodComments'] = ['nullable', 'string', 'max:1000'];
        
        $rules['divisionalDirectorName'] = ['nullable', 'string', 'max:255'];
        $rules['divisionalDirectorSignature'] = ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'];
        $rules['divisionalDirectorDate'] = ['nullable', 'date'];
        $rules['divisionalDirectorComments'] = ['nullable', 'string', 'max:1000'];
        
        $rules['ictDirectorName'] = ['nullable', 'string', 'max:255'];
        $rules['ictDirectorSignature'] = ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'];
        $rules['ictDirectorDate'] = ['nullable', 'date'];
        $rules['ictDirectorComments'] = ['nullable', 'string', 'max:1000'];
        
        // \u2705 ADD IMPLEMENTATION DATA VALIDATION RULES
        $rules['headITName'] = ['nullable', 'string', 'max:255'];
        $rules['headITSignature'] = ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'];
        $rules['headITDate'] = ['nullable', 'date'];
        $rules['headITComments'] = ['nullable', 'string', 'max:1000'];
        
        $rules['ictOfficerName'] = ['nullable', 'string', 'max:255'];
        $rules['ictOfficerSignature'] = ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'];
        $rules['ictOfficerDate'] = ['nullable', 'date'];
        $rules['ictOfficerComments'] = ['nullable', 'string', 'max:1000'];
        $rules['implementationComments'] = ['nullable', 'string', 'max:1000'];

        return $rules;
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
            'signature.required' => 'Digital signature is required.',
            'signature.mimes' => 'Digital signature must be a PNG, JPG, or JPEG file.',
            'signature.max' => 'Digital signature file size must not exceed 5MB.',
            'request_type.required' => 'At least one service must be selected.',
            'request_type.min' => 'At least one service must be selected.',
            'request_type.*.required' => 'Service type is required.',
            'request_type.*.in' => 'Invalid service type selected.',
            'internetPurposes.required' => 'Internet purposes are required when internet access is selected.',
            'internetPurposes.min' => 'At least one internet purpose must be provided.',
            'internetPurposes.max' => 'Maximum 4 internet purposes are allowed.',
            'internetPurposes.*.max' => 'Each internet purpose must not exceed 255 characters.',
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
            'request_type' => 'Services',
            'internetPurposes' => 'Internet Purposes',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        \Log::info('prepareForValidation - Raw input:', [
            'all' => $this->all(),
            'request_type' => $this->input('request_type'),
            'services' => $this->input('services')
        ]);
        
        // Clean and format the data
        $this->merge([
            'pf_number' => $this->cleanPfNumber($this->pf_number),
            'staff_name' => $this->cleanStaffName($this->staff_name),
            'phone_number' => $this->cleanPhoneNumber($this->phone_number),
        ]);

        // Convert services object to request_type array if services are provided and request_type is not already set
        if ($this->has('services') && is_array($this->services) && !$this->has('request_type')) {
            $requestTypes = [];
            
            if ($this->convertToBoolean($this->services['jeeva'] ?? false)) {
                $requestTypes[] = 'jeeva_access';
            }
            if ($this->convertToBoolean($this->services['wellsoft'] ?? false)) {
                $requestTypes[] = 'wellsoft';
            }
            if ($this->convertToBoolean($this->services['internet'] ?? false)) {
                $requestTypes[] = 'internet_access_request';
            }
            
            $this->merge(['request_type' => $requestTypes]);
        }
        
        // If request_type exists but services doesn't, create services from request_type
        if ($this->has('request_type') && is_array($this->request_type) && !$this->has('services')) {
            $services = [
                'jeeva' => in_array('jeeva_access', $this->request_type),
                'wellsoft' => in_array('wellsoft', $this->request_type),
                'internet' => in_array('internet_access_request', $this->request_type)
            ];
            
            $this->merge(['services' => $services]);
        }
        
        // Ensure services array has all keys with proper boolean values
        if ($this->has('services') && is_array($this->services)) {
            $services = [
                'jeeva' => $this->convertToBoolean($this->services['jeeva'] ?? false),
                'wellsoft' => $this->convertToBoolean($this->services['wellsoft'] ?? false),
                'internet' => $this->convertToBoolean($this->services['internet'] ?? false)
            ];
            
            $this->merge(['services' => $services]);
        }

        // Clean internet purposes - remove empty values
        if ($this->has('internetPurposes') && is_array($this->internetPurposes)) {
            $cleanPurposes = array_filter($this->internetPurposes, function($purpose) {
                return !empty(trim($purpose));
            });
            $this->merge(['internetPurposes' => array_values($cleanPurposes)]);
        }
        
        \Log::info('prepareForValidation - After processing:', [
            'request_type' => $this->input('request_type'),
            'internetPurposes' => $this->input('internetPurposes')
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

    /**
     * Convert string boolean values to actual booleans.
     */
    private function convertToBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }
        
        return (bool) $value;
    }

    /**
     * Additional validation logic.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if at least one service is selected in request_type
            $requestTypes = $this->input('request_type', []);
            
            if (empty($requestTypes)) {
                $validator->errors()->add('request_type', 'At least one service must be selected.');
            }
            
            // Validate that all request types are valid
            $validTypes = ['jeeva_access', 'wellsoft', 'internet_access_request'];
            foreach ($requestTypes as $type) {
                if (!in_array($type, $validTypes)) {
                    $validator->errors()->add('request_type', "Invalid service type: {$type}");
                }
            }
            
            // Validate internet purposes if internet access is selected
            if (in_array('internet_access_request', $requestTypes)) {
                $internetPurposes = $this->input('internetPurposes', []);
                
                if (empty($internetPurposes) || !array_filter($internetPurposes, function($purpose) {
                    return !empty(trim($purpose));
                })) {
                    $validator->errors()->add('internetPurposes', 'Internet purposes are required when internet access is selected.');
                }
            }
        });
    }
}