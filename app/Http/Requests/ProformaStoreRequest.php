<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProformaStoreRequest extends FormRequest
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
            'total_amount' => ['required', 'numeric'],
            'due_amount' => ['required', 'numeric'],
            'sale_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'invoice_type' => ['nullable', 'string'],
            'agency_id' => ['required', 'integer', 'exists:agencies,id'],
            'created_by' => ['required'],
            'proforma_items' => ['nullable', 'string'],
            'client' => ['nullable', 'string'],
        ];
    }
}
