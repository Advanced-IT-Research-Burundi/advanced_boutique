<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStockStoreRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'created_by' => ['required'],
        ];
    }
}
