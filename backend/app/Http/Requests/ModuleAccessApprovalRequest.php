<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleAccessApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Approval status and comments
            'approval_status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string|max:1000',
            'approver_name' => 'nullable|string|max:255',
            'approver_signature' => 'nullable|string|max:500',
            'approval_date' => 'nullable|date',
            
            // Module form data
            'module_requested_for' => 'required|in:use,revoke',
            'wellsoft_modules' => 'nullable|array',
            'wellsoft_modules.*' => 'string|max:255',
            'jeeva_modules' => 'nullable|array', 
            'jeeva_modules.*' => 'string|max:255',
            'internet_purposes' => 'nullable|array',
            'internet_purposes.*' => 'string|max:500',
            'access_type' => 'required|in:permanent,temporary',
            'temporary_until' => 'nullable|required_if:access_type,temporary|date|after:today',
            
            // Stage-specific approval data
            'stage' => 'required|in:hod,divisional_director,ict_director,head_it,ict_officer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'module_requested_for.required' => 'Please specify if modules are requested for use or revocation.',
            'module_requested_for.in' => 'Module request type must be either "use" or "revoke".',
            'wellsoft_modules.*.string' => 'Each Wellsoft module selection must be a valid string.',
            'jeeva_modules.*.string' => 'Each Jeeva module selection must be a valid string.',
            'internet_purposes.*.string' => 'Each internet purpose must be a valid string.',
            'access_type.required' => 'Please specify whether access is permanent or temporary.',
            'access_type.in' => 'Access type must be either "permanent" or "temporary".',
            'temporary_until.required_if' => 'Please specify end date for temporary access.',
            'temporary_until.after' => 'Temporary access end date must be in the future.',
            'approval_status.required' => 'Please specify approval status.',
            'approval_status.in' => 'Approval status must be either "approved" or "rejected".',
            'stage.required' => 'Approval stage must be specified.',
            'stage.in' => 'Invalid approval stage specified.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that at least one module type is selected if access is requested for use
            if ($this->input('module_requested_for') === 'use') {
                $wellsoftModules = $this->input('wellsoft_modules', []);
                $jeevaModules = $this->input('jeeva_modules', []);
                $internetPurposes = $this->input('internet_purposes', []);
                
                // Filter out empty values
                $wellsoftModules = array_filter($wellsoftModules, function($item) {
                    return !empty(trim($item));
                });
                $jeevaModules = array_filter($jeevaModules, function($item) {
                    return !empty(trim($item));
                });
                $internetPurposes = array_filter($internetPurposes, function($item) {
                    return !empty(trim($item));
                });
                
                if (empty($wellsoftModules) && empty($jeevaModules) && empty($internetPurposes)) {
                    $validator->errors()->add('modules', 'At least one module or service must be selected for access requests.');
                }
            }
        });
    }

    /**
     * Get cleaned and processed data
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();
        
        // Clean and filter module arrays
        if (isset($data['wellsoft_modules'])) {
            $data['wellsoft_modules'] = array_values(array_filter($data['wellsoft_modules'], function($item) {
                return !empty(trim($item));
            }));
        }
        
        if (isset($data['jeeva_modules'])) {
            $data['jeeva_modules'] = array_values(array_filter($data['jeeva_modules'], function($item) {
                return !empty(trim($item));
            }));
        }
        
        if (isset($data['internet_purposes'])) {
            $data['internet_purposes'] = array_values(array_filter($data['internet_purposes'], function($item) {
                return !empty(trim($item));
            }));
        }
        
        return $data;
    }
}
