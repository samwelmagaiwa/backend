<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $departmentId = $this->route('department')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($departmentId)
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departments', 'code')->ignore($departmentId)
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'hod_user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('departments', 'hod_user_id')->ignore($departmentId)
            ],
            'has_divisional_director' => 'boolean',
            'divisional_director_id' => [
                'nullable',
                'exists:users,id',
                'required_if:has_divisional_director,true',
                Rule::unique('departments', 'divisional_director_id')->ignore($departmentId)
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'name.unique' => 'A department with this name already exists.',
            'code.required' => 'Department code is required.',
            'code.unique' => 'A department with this code already exists.',
            'hod_user_id.exists' => 'Selected HOD user does not exist.',
            'hod_user_id.unique' => 'This user is already assigned as HOD to another department.',
            'divisional_director_id.exists' => 'Selected divisional director does not exist.',
            'divisional_director_id.required_if' => 'Divisional director is required when department has divisional director.',
            'divisional_director_id.unique' => 'This user is already assigned as divisional director to another department.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert code to uppercase
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code)
            ]);
        }

        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'has_divisional_director' => $this->boolean('has_divisional_director', false),
        ]);

        // Clear divisional_director_id if has_divisional_director is false
        if (!$this->boolean('has_divisional_director')) {
            $this->merge([
                'divisional_director_id' => null
            ]);
        }
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ensure divisional_director_id is null if has_divisional_director is false
        if (!$validated['has_divisional_director']) {
            $validated['divisional_director_id'] = null;
        }

        return $validated;
    }
}