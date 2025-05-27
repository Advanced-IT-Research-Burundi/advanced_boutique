<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'email_verified_at' => ['nullable'],
            'password' => ['required', 'password'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'string'],
            'status' => ['required', 'string'],
            'role' => ['required', 'string'],
            'permissions' => ['nullable', 'json'],
            'last_login_at' => ['nullable'],
            'must_change_password' => ['required'],
            'two_factor_enabled' => ['required'],
            'two_factor_secret' => ['nullable', 'string'],
            'recovery_codes' => ['nullable', 'json'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'created_by' => ['nullable'],
            'remember_token' => ['nullable', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
