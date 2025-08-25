<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        $roleId = $this->route('role') ? $this->route('role')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_\s]+$/', // Only alphanumeric, underscore, and spaces
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'permissions' => [
                'nullable',
                'array'
            ],
            'permissions.*' => [
                'string',
                'max:255'
            ],
            'is_system_role' => [
                'boolean'
            ],
            'is_deletable' => [
                'boolean'
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.unique' => 'A role with this name already exists.',
            'name.regex' => 'Role name can only contain letters, numbers, underscores, and spaces.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.string' => 'Each permission must be a string.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize role name
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }

        // Set default values for system roles
        if ($this->isMethod('POST')) {
            $this->merge([
                'is_system_role' => $this->boolean('is_system_role', false),
                'is_deletable' => $this->boolean('is_deletable', true),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Prevent making system roles non-deletable during update
            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $role = $this->route('role');
                if ($role && $role->is_system_role && $this->has('is_deletable') && $this->is_deletable) {
                    $validator->errors()->add('is_deletable', 'System roles cannot be made deletable.');
                }
            }

            // Validate permissions format
            if ($this->has('permissions') && is_array($this->permissions)) {
                foreach ($this->permissions as $permission) {
                    if (!is_string($permission) || empty(trim($permission))) {
                        $validator->errors()->add('permissions', 'All permissions must be non-empty strings.');
                        break;
                    }
                }
            }
        });
    }
}