<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferUpdateRequest extends FormRequest
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
            'from_stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'to_stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'transfer_date' => ['required'],
            'note' => ['required', 'string'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'created_by' => ['required'],
            'stock_id' => ['required', 'integer', 'exists:stocks,id'],
        ];
    }
}
