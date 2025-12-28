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
            'quantite' => ['required', 'numeric', 'min:0'],
            'valeur' => ['required', 'numeric', 'min:0'],
            'devise' => ['required', 'string', 'max:10'],
            'type_element' => ['required', 'in:caisse,banque,avance,credit,investissement,immobilisation,autre'],
            'reference' => ['nullable', 'string', 'max:100'],
            'observation' => ['nullable', 'string'],
            'exchange_rate' => ['required', 'numeric', 'min:1'],
            // valide le fichier est ce type document et de taille max 5MB
            'document' => ['sometimes', 'nullable'],
        ];
    }
}
