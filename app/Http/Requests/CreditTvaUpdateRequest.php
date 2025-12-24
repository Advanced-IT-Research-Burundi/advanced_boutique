<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditTvaUpdateRequest extends FormRequest
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
            'date' => ['nullable', 'date'],
            'montant' => ['nullable', 'string'],
            'description' => ['nullable', 'numeric'],
            'is_actif' => ['required'],
        ];
    }
}
