<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutreElementStoreRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'libelle' => ['required', 'string'],
            'emplacement' => ['nullable', 'string'],
            'quantite' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'valeur' => ['required', 'numeric', 'between:-9999999999999.99,9999999999999.99'],
            'devise' => ['required', 'string', 'max:10'],
            'type_element' => ['required', 'in:caisse,banque,avance,credit,investissement,immobilisation,autre'],
            'reference' => ['nullable', 'string', 'max:100'],
            'observation' => ['nullable', 'string'],
            'document' => ['nullable', 'string'],
        ];
    }
}
