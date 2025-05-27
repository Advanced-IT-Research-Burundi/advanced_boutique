<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgencyStoreRequest extends FormRequest
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
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'code' => ['required', 'string', 'unique:agencies,code'],
            'name' => ['required', 'string'],
            'adresse' => ['required', 'string'],
            'manager_id' => ['nullable', 'integer', 'exists:users,id'],
            'parent_agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'is_main_office' => ['required'],
            'created_by' => ['required'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'agency_id' => ['required', 'integer', 'exists:agencies,id'],
        ];
    }
}
