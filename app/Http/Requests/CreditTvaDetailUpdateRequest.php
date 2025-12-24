<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditTvaDetailUpdateRequest extends FormRequest
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
            'type' => ['nullable', 'string'],
            'montant' => ['nullable', 'numeric'],
            'sale_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
        ];
    }
}
