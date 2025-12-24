<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditTvaDetailStoreRequest extends FormRequest
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
            'credit_tva_id' => ['nullable', 'integer'],
            'montant' => ['nullable', 'numeric'],
            'sale_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'type' => ['required', 'in:ADD,SUB'  ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be either ADD or SUB.',
        ];
    }
}