<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashRegisterStoreRequest extends FormRequest
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
            'opening_balance' => ['required', 'numeric'],
            'closing_balance' => ['required', 'numeric'],
            'status' => ['required', 'string'],
            'opened_at' => ['required'],
            'closed_at' => ['required'],
            'created_by' => ['required'],
        ];
    }
}
