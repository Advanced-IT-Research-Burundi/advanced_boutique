<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseUpdateRequest extends FormRequest
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
            'stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'expense_type_id' => ['required', 'integer', 'exists:expense_types,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'expense_date' => ['required'],
            'created_by' => ['required'],
        ];
    }
}
