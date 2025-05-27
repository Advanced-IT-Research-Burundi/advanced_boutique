<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseUpdateRequest extends FormRequest
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
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'total_amount' => ['required', 'numeric'],
            'paid_amount' => ['required', 'numeric'],
            'due_amount' => ['required', 'numeric'],
            'purchase_date' => ['required'],
            'created_by' => ['required'],
        ];
    }
}
