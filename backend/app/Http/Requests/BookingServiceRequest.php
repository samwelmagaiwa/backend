<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\BookingService;
use App\Models\Department;
use App\Models\Signature;

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
            'device_inventory_ids' => [
                'nullable',
                'array',
            ],
            'device_inventory_ids.*' => [
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
                'after_or_equal:booking_date'
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
            // No file-based signature rules; digital signature is validated in withValidator()
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
            'return_date.after_or_equal' => 'Return date cannot be before the booking date.',
            
            'return_time.required' => 'Return time is required.',
            'return_time.regex' => 'Please provide a valid time in HH:MM format (24-hour format).',
            
            'reason.required' => 'Reason for borrowing is required.',
            'reason.min' => 'Reason must be at least 10 characters.',
            'reason.max' => 'Reason cannot exceed 1000 characters.',

            'device_inventory_ids.array' => 'Selected devices must be a valid list.',
            'device_inventory_ids.*.exists' => 'One or more selected devices were not found in inventory.',

            // Digital signature messages
            'digital_signature.required' => 'Digital signature is required. Please click “Sign Document” before submitting.'
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

        // Normalize multi-device selection to a clean array (keep original single id for BC)
        if ($this->has('device_inventory_ids')) {
            $ids = $this->input('device_inventory_ids');
            if (is_string($ids)) {
                $ids = array_filter(explode(',', $ids));
            }
            if (is_array($ids)) {
                $normalized = [];
                foreach ($ids as $id) {
                    if ($id === null || $id === '' || $id === 'others') {
                        continue;
                    }
                    $normalized[] = (int) $id;
                }
                $normalized = array_values(array_unique(array_filter($normalized)));

                $this->merge([
                    'device_inventory_ids' => $normalized,
                ]);

                // Preserve first element as primary device_inventory_id when not explicitly set
                if (!$this->has('device_inventory_id') && !empty($normalized)) {
                    $this->merge([
                        'device_inventory_id' => $normalized[0],
                    ]);
                }
            }
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

            // Enforce presence of a digital signature pre-signed for booking_service token
            try {
                $userId = optional($this->user())->id;
                if ($userId) {
                    $syntheticDocId = 910000000 + (int) $userId; // booking_service synthetic document id
                    $hasSignature = Signature::where('document_id', $syntheticDocId)
                        ->where('user_id', $userId)
                        ->exists();
                    if (!$hasSignature) {
                        $validator->errors()->add('digital_signature', 'Digital signature is required. Please click “Sign Document” before submitting.');
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error checking digital signature for booking_service token', [
                    'error' => $e->getMessage()
                ]);
                // Fail closed if we cannot verify
                $validator->errors()->add('digital_signature', 'Digital signature verification failed. Please try signing again.');
            }
            
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
                    
                    // NOTE: We do not enforce any minimum-hours rule for same-day returns.
                    // The system only captures booking_date (no booking_time), so enforcing a 1-hour
                    // delta from midnight is incorrect and blocks valid same-day returns.
                } catch (\Exception $e) {
                    \Log::error('DateTime parsing error', ['error' => $e->getMessage()]);
                    $validator->errors()->add('return_time', 'Invalid date or time format.');
                }
            }

            // Validate custom device when device_type is 'others' AND no inventory devices are provided
            $hasInventoryList = is_array($this->device_inventory_ids) && !empty($this->device_inventory_ids);
            if (
                $this->device_type === 'others' &&
                !$this->device_inventory_id &&
                !$hasInventoryList &&
                empty(trim($this->custom_device ?? ''))
            ) {
                $validator->errors()->add('custom_device', 'Please specify the device when "Others" is selected.');
                \Log::info('Validation error: Custom device required for others');
            }
            
            // If device inventory (single or list) is provided, we don't need custom_device even if device_type is 'others'
            if (($this->device_inventory_id || $hasInventoryList) && $this->device_type === 'others') {
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
        
        // If device_inventory_id is provided, validate the primary inventory device
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

        // If additional inventory devices are provided, ensure they exist and are active
        if (is_array($this->device_inventory_ids) && !empty($this->device_inventory_ids)) {
            foreach ($this->device_inventory_ids as $extraId) {
                if ($this->device_inventory_id && (int) $extraId === (int) $this->device_inventory_id) {
                    continue; // already validated as primary
                }
                $extraDevice = \App\Models\DeviceInventory::find($extraId);
                if (!$extraDevice) {
                    $validator->errors()->add('device_type', 'One of the selected devices was not found in inventory.');
                    \Log::info('Validation error: Extra device inventory not found', ['device_inventory_id' => $extraId]);
                    return;
                }
                if (!$extraDevice->is_active) {
                    $validator->errors()->add('device_type', 'One of the selected devices is not active.');
                    \Log::info('Validation error: Extra device inventory not active', ['device_inventory_id' => $extraId]);
                    return;
                }
            }

            \Log::info('All extra device inventory items validated', [
                'device_inventory_ids' => $this->device_inventory_ids
            ]);
        }
        
        \Log::info('Device type validation passed', ['device_type' => $this->device_type]);
    }
}