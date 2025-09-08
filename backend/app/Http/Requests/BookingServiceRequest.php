<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\BookingService;
use App\Models\Department;

class BookingServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $deviceTypes = array_keys(BookingService::getDeviceTypes());
        $departmentIds = Department::pluck('id')->toArray();

        return [
            'booking_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'borrower_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/' // Only letters, spaces, dots, hyphens, apostrophes
            ],
            'device_type' => [
                'required',
                'string'
                // Remove the Rule::in validation to allow inventory device codes
            ],
            'device_inventory_id' => [
                'nullable',
                'integer',
                'exists:device_inventory,id'
            ],
            'custom_device' => [
                'nullable',
                Rule::requiredIf(function () {
                    return $this->input('device_type') === 'others' && !$this->filled('device_inventory_id');
                }),
                'string',
                'max:255'
            ],
            'department' => [
                'required',
                'integer',
                Rule::in($departmentIds)
            ],
            'phone_number' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Phone number format
            ],
            'return_date' => [
                'required',
                'date',
                'after:booking_date'
            ],
            'return_time' => [
                'required',
                'string',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'
            ],
            'return_date_time' => [
                'nullable',
                'date'
            ],
            'reason' => [
                'required',
                'string',
                'min:10',
                'max:1000'
            ],
            'signature' => [
                'required',
                'file',
                'mimes:png,jpg,jpeg',
                'max:2048' // 2MB max
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'booking_date.required' => 'Booking date is required.',
            'booking_date.date' => 'Please provide a valid booking date.',
            'booking_date.after_or_equal' => 'Booking date cannot be in the past.',
            
            'borrower_name.required' => 'Borrower name is required.',
            'borrower_name.regex' => 'Borrower name can only contain letters, spaces, dots, hyphens, and apostrophes.',
            'borrower_name.max' => 'Borrower name cannot exceed 255 characters.',
            
            'device_type.required' => 'Please select a device type.',
            'device_type.in' => 'Please select a valid device type.',
            
            'custom_device.required_if' => 'Please specify the device when "Others" is selected.',
            'custom_device.required' => 'Please specify the device when "Others" is selected.',
            'custom_device.max' => 'Custom device name cannot exceed 255 characters.',
            
            'department.required' => 'Please select a department.',
            'department.in' => 'Please select a valid department.',
            
            'phone_number.required' => 'Phone number is required.',
            'phone_number.min' => 'Phone number must be at least 10 digits.',
            'phone_number.max' => 'Phone number cannot exceed 20 characters.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            
            'return_date.required' => 'Return date is required.',
            'return_date.date' => 'Please provide a valid return date.',
            'return_date.after' => 'Return date must be after the booking date.',
            
            'return_time.required' => 'Return time is required.',
            'return_time.regex' => 'Please provide a valid time in HH:MM format (24-hour format).',
            
            'reason.required' => 'Reason for borrowing is required.',
            'reason.min' => 'Reason must be at least 10 characters.',
            'reason.max' => 'Reason cannot exceed 1000 characters.',
            
            'signature.required' => 'Digital signature is required.',
            'signature.file' => 'Signature must be a valid file.',
            'signature.mimes' => 'Signature must be a PNG, JPG, or JPEG image.',
            'signature.max' => 'Signature file size cannot exceed 2MB.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'booking_date' => 'booking date',
            'borrower_name' => 'borrower name',
            'device_type' => 'device type',
            'custom_device' => 'custom device',
            'department' => 'department',
            'phone_number' => 'phone number',
            'return_date' => 'return date',
            'return_time' => 'return time',
            'reason' => 'reason for borrowing',
            'signature' => 'digital signature'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        \Log::info('prepareForValidation - Raw input:', $this->all());
        
        // Clean up phone number
        if ($this->has('phone_number')) {
            $this->merge([
                'phone_number' => preg_replace('/[^\+0-9\s\-\(\)]/', '', $this->phone_number)
            ]);
        }

        // Clean up borrower name
        if ($this->has('borrower_name')) {
            $this->merge([
                'borrower_name' => trim($this->borrower_name)
            ]);
        }

        // Clean up reason
        if ($this->has('reason')) {
            $this->merge([
                'reason' => trim($this->reason)
            ]);
        }

        // Clean up custom device
        if ($this->has('custom_device')) {
            $this->merge([
                'custom_device' => trim($this->custom_device)
            ]);
        }
        
        // Ensure department is integer
        if ($this->has('department')) {
            $this->merge([
                'department' => (int) $this->department
            ]);
        }
        
        \Log::info('prepareForValidation - After processing:', $this->all());
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            \Log::info('Custom validation running', [
                'booking_date' => $this->booking_date,
                'return_date' => $this->return_date,
                'return_time' => $this->return_time,
                'device_type' => $this->device_type,
                'device_inventory_id' => $this->device_inventory_id,
                'custom_device' => $this->custom_device
            ]);
            
            // Validate device type
            $this->validateDeviceType($validator);
            
            // Additional validation for return date/time combination
            if ($this->has(['return_date', 'return_time'])) {
                try {
                    $returnDateTime = \Carbon\Carbon::parse($this->return_date . ' ' . $this->return_time);
                    $bookingDateTime = \Carbon\Carbon::parse($this->booking_date . ' 00:00:00');
                    
                    \Log::info('DateTime validation', [
                        'return_datetime' => $returnDateTime->toDateTimeString(),
                        'booking_datetime' => $bookingDateTime->toDateTimeString(),
                        'diff_hours' => $returnDateTime->diffInHours($bookingDateTime)
                    ]);
                    
                    if ($returnDateTime->lessThanOrEqualTo($bookingDateTime)) {
                        $validator->errors()->add('return_time', 'Return date and time must be after the booking date.');
                        \Log::info('Validation error: Return time before booking time');
                    }
                    
                    // Only check 1 hour rule if it's the same day
                    if ($this->booking_date === $this->return_date) {
                        $diffInMinutes = $returnDateTime->diffInMinutes($bookingDateTime);
                        \Log::info('Same day booking - checking 1 hour rule', [
                            'diff_in_minutes' => $diffInMinutes
                        ]);
                        
                        if ($diffInMinutes < 60) {
                            $validator->errors()->add('return_time', 'Return time must be at least 1 hour after booking date when returning same day.');
                            \Log::info('Validation error: Return time less than 1 hour after booking');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('DateTime parsing error', ['error' => $e->getMessage()]);
                    $validator->errors()->add('return_time', 'Invalid date or time format.');
                }
            }

            // Validate custom device when device_type is 'others' AND no device_inventory_id is provided
            if ($this->device_type === 'others' && !$this->device_inventory_id && empty(trim($this->custom_device ?? ''))) {
                $validator->errors()->add('custom_device', 'Please specify the device when "Others" is selected.');
                \Log::info('Validation error: Custom device required for others');
            }
            
            // If device_inventory_id is provided, we don't need custom_device even if device_type is 'others'
            if ($this->device_inventory_id && $this->device_type === 'others') {
                \Log::info('Device inventory provided with others type - this is acceptable', [
                    'device_inventory_id' => $this->device_inventory_id,
                    'device_type' => $this->device_type
                ]);
            }
            
            \Log::info('Custom validation completed', [
                'errors' => $validator->errors()->toArray()
            ]);
        });
    }
    
    /**
     * Validate device type based on whether it's from inventory or predefined types.
     */
    private function validateDeviceType($validator): void
    {
        $deviceTypes = array_keys(BookingService::getDeviceTypes());
        
        // First, validate that device_type is a valid ENUM value
        if (!in_array($this->device_type, $deviceTypes)) {
            $validator->errors()->add('device_type', 'Please select a valid device type.');
            \Log::info('Validation error: Invalid device type', [
                'device_type' => $this->device_type,
                'valid_types' => $deviceTypes
            ]);
            return;
        }
        
        // If device_inventory_id is provided, validate the inventory device
        if ($this->device_inventory_id) {
            $deviceInventory = \App\Models\DeviceInventory::find($this->device_inventory_id);
            if (!$deviceInventory) {
                $validator->errors()->add('device_type', 'Selected device not found in inventory.');
                \Log::info('Validation error: Device inventory not found', ['device_inventory_id' => $this->device_inventory_id]);
                return;
            }
            
            if (!$deviceInventory->is_active) {
                $validator->errors()->add('device_type', 'Selected device is not active.');
                \Log::info('Validation error: Device inventory not active', ['device_inventory_id' => $this->device_inventory_id]);
                return;
            }
            
            if (!$deviceInventory->isAvailable()) {
                $validator->errors()->add('device_type', 'Selected device is not available for borrowing.');
                \Log::info('Validation error: Device inventory not available', ['device_inventory_id' => $this->device_inventory_id]);
                return;
            }
            
            \Log::info('Device inventory validation passed', [
                'device_inventory_id' => $this->device_inventory_id,
                'device_name' => $deviceInventory->device_name,
                'mapped_device_type' => $this->device_type
            ]);
        }
        
        \Log::info('Device type validation passed', ['device_type' => $this->device_type]);
    }
}