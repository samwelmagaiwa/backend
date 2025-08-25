<?php

namespace App\Http\Requests\BothServiceForm;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
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
        $rules = [
            'action' => 'required|in:approve,reject',
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ];

        // HOD comments are required, others are optional
        if ($this->route('role') === 'hod') {
            $rules['comments'] = 'required|string|min:10|max:1000';
        } else {
            $rules['comments'] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Action (approve/reject) is required.',
            'action.in' => 'Action must be either approve or reject.',
            'comments.required' => 'Comments are required for HOD approval.',
            'comments.min' => 'Comments must be at least 10 characters long.',
            'comments.max' => 'Comments must not exceed 1000 characters.',
            'signature.required' => 'Signature is required for approval.',
            'signature.mimes' => 'Signature must be a PNG, JPG, or JPEG image.',
            'signature.max' => 'Signature file size must not exceed 2MB.',
        ];
    }
}