<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepensesImportationStoreRequest extends FormRequest
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
            'depense_importation_type' => ['nullable', 'string'],
            'depense_importation_type_id' => ['required', 'integer', 'exists:depense_importation_types,id'],
            'currency' => ['required', 'string'],
            'exchange_rate' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'amount_currency' => ['required', 'numeric'],
            'date' => ['required'],
            'description' => ['nullable', 'string'],
            'commande_id' => ['nullable', 'integer', 'exists:commandes,id'],
        ];
    }
}
