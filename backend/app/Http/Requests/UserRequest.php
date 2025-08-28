<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAdminPrivileges();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user') : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'email' => [
                $isUpdate ? 'sometimes' : 'required',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'phone' => 'nullable|string|max:20',
            'pf_number' => [
                $isUpdate ? 'sometimes' : 'required',
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'pf_number')->ignore($userId)
            ],
            'staff_name' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ];

        // Password rules for creation and optional update
        if (!$isUpdate) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['role_ids'] = 'required|array|min:1';
            $rules['role_ids.*'] = 'exists:roles,id';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'pf_number.required' => 'PF number is required.',
            'pf_number.unique' => 'This PF number is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role_ids.required' => 'At least one role must be assigned.',
            'role_ids.array' => 'Roles must be provided as an array.',
            'role_ids.min' => 'At least one role must be assigned.',
            'role_ids.*.exists' => 'One or more selected roles do not exist.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation for role assignments during creation
            if (!$this->isMethod('PUT') && !$this->isMethod('PATCH')) {
                $roleIds = $this->input('role_ids', []);

                // Prevent privilege escalation - only admins can assign admin roles
                if (!$this->user()->isAdmin()) {
                    $adminRoles = \App\Models\Role::whereIn('name', ['admin'])->pluck('id')->toArray();
                    $hasAdminRole = !empty(array_intersect($roleIds, $adminRoles));
                    
                    if ($hasAdminRole) {
                        $validator->errors()->add('role_ids', 'You cannot assign admin roles.');
                    }
                }

                // Validate that roles exist and are assignable
                if (!empty($roleIds)) {
                    $validRoles = \App\Models\Role::whereIn('id', $roleIds)->get();
                    
                    if ($validRoles->count() !== count($roleIds)) {
                        $validator->errors()->add('role_ids', 'One or more selected roles are invalid.');
                    }

                    // Check for system roles that shouldn't be assigned by regular users
                    $systemRoles = $validRoles->where('is_system_role', true)->where('name', 'admin');
                    if ($systemRoles->isNotEmpty() && !$this->user()->isAdmin()) {
                        $validator->errors()->add('role_ids', 'You cannot assign admin role.');
                    }
                }
            }

            // Validate email format more strictly
            if ($this->has('email')) {
                $email = $this->input('email');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add('email', 'Please provide a valid email address.');
                }
            }

            // Validate PF number format (customize as needed)
            if ($this->has('pf_number')) {
                $pfNumber = $this->input('pf_number');
                if (!preg_match('/^[A-Z0-9\/\-]+$/i', $pfNumber)) {
                    $validator->errors()->add('pf_number', 'PF number format is invalid.');
                }
            }

            // Validate phone number format
            if ($this->has('phone') && $this->input('phone')) {
                $phone = $this->input('phone');
                if (!preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $phone)) {
                    $validator->errors()->add('phone', 'Phone number format is invalid.');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and normalize input data
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }

        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }

        if ($this->has('pf_number')) {
            $this->merge([
                'pf_number' => strtoupper(trim($this->pf_number)),
            ]);
        }

        if ($this->has('staff_name')) {
            $this->merge([
                'staff_name' => trim($this->staff_name),
            ]);
        }

        if ($this->has('phone')) {
            $this->merge([
                'phone' => trim($this->phone),
            ]);
        }

        // Ensure role_ids is an array and remove duplicates
        if ($this->has('role_ids')) {
            $roleIds = $this->input('role_ids');
            if (!is_array($roleIds)) {
                $roleIds = [$roleIds];
            }
            
            $this->merge([
                'role_ids' => array_unique(array_filter($roleIds))
            ]);
        }

        // Set default active status
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}