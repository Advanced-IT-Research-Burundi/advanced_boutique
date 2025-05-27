<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashTransactionStoreRequest extends FormRequest
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
            'cash_register_id' => ['required', 'integer', 'exists:cash_registers,id'],
            'type' => ['required', 'string'],
            'reference_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'created_by' => ['required'],
        ];
    }
}
