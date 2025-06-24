<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Update to use proper authorization logic in production
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user ? $user->id : null;
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($userId)],
            'phone' => 'nullable|string|max:20',
            'cnic' => ['nullable', 'string', 'max:15', \Illuminate\Validation\Rule::unique('users', 'cnic')->ignore($userId)],
            'nfc_uid' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('users', 'nfc_uid')->ignore($userId)],
            'designation' => 'nullable|string|max:100',
            'role' => 'sometimes|required|in:admin,supervisor,guard',
            'client_id' => 'nullable|exists:clients,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'sometimes|required|in:active,inactive',
            'password' => 'nullable|string|min:8',
        ];
    }
    
    public function messages()
    {
        return [
            'email.unique' => 'This email is already in use.',
            'cnic.unique' => 'This CNIC is already registered.',
            'nfc_uid.unique' => 'This NFC UID is already in use.',
            'client_id.exists' => 'The selected company is invalid.',
            'role.in' => 'The selected role is invalid.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
