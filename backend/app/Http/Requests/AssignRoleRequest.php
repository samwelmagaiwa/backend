<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;

class AssignRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'role_ids' => [
                'required',
                'array',
                'min:1'
            ],
            'role_ids.*' => [
                'integer',
                'exists:roles,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'role_ids.required' => 'At least one role must be selected.',
            'role_ids.array' => 'Role IDs must be provided as an array.',
            'role_ids.min' => 'At least one role must be selected.',
            'role_ids.*.integer' => 'Each role ID must be a valid integer.',
            'role_ids.*.exists' => 'One or more selected roles do not exist.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->route('user');
            $roleIds = $this->input('role_ids', []);

            // Prevent privilege escalation
            if (!$this->user()->isSuperAdmin()) {
                $adminRoles = Role::whereIn('name', ['admin', 'super_admin'])->pluck('id')->toArray();
                $hasAdminRole = !empty(array_intersect($roleIds, $adminRoles));
                
                if ($hasAdminRole) {
                    $validator->errors()->add('role_ids', 'You cannot assign admin roles.');
                }
            }

            // Check if trying to assign roles to self
            if ($user && $user->id === $this->user()->id) {
                $validator->errors()->add('user', 'You cannot modify your own roles.');
            }

            // Validate that roles exist and are assignable
            if (!empty($roleIds)) {
                $validRoles = Role::whereIn('id', $roleIds)->get();
                
                if ($validRoles->count() !== count($roleIds)) {
                    $validator->errors()->add('role_ids', 'One or more selected roles are invalid.');
                }

                // Check for system roles that shouldn't be assigned
                $systemRoles = $validRoles->where('is_system_role', true);
                if ($systemRoles->isNotEmpty() && !$this->user()->isSuperAdmin()) {
                    $validator->errors()->add('role_ids', 'You cannot assign system roles.');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
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
    }
}