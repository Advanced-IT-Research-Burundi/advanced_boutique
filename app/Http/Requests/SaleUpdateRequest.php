<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleUpdateRequest extends FormRequest
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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'total_amount' => ['required', 'numeric'],
            'paid_amount' => ['required', 'numeric'],
            'due_amount' => ['required', 'numeric'],
            'sale_date' => ['required'],
            'created_by' => ['required'],
        ];
    }
}
