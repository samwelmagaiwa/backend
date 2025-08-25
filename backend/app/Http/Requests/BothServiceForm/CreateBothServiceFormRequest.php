<?php

namespace App\Http\Requests\BothServiceForm;

use Illuminate\Foundation\Http\FormRequest;

class CreateBothServiceFormRequest extends FormRequest
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
        return [
            'services_requested' => 'required|array|min:1',
            'services_requested.*' => 'required|string|in:wellsoft,jeeva,internet_access',
            'access_type' => 'required|in:permanent,temporary',
            'temporary_until' => 'nullable|date|required_if:access_type,temporary|after:today',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'comments' => 'nullable|string|max:1000',
            'department_id' => 'required|exists:departments,id',
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'services_requested.required' => 'At least one service must be selected.',
            'services_requested.min' => 'At least one service must be selected.',
            'services_requested.*.in' => 'Invalid service selected.',
            'temporary_until.required_if' => 'End date is required for temporary access.',
            'temporary_until.after' => 'End date must be in the future.',
            'department_id.required' => 'Department is required.',
            'department_id.exists' => 'Selected department is invalid.',
            'signature.required' => 'Signature is required.',
            'signature.mimes' => 'Signature must be a PNG, JPG, or JPEG image.',
            'signature.max' => 'Signature file size must not exceed 2MB.',
        ];
    }
}