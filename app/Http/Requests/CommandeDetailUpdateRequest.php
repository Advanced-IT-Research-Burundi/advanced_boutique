<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommandeDetailUpdateRequest extends FormRequest
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
            'commande_id' => ['required', 'integer'],
            'produit_id' => ['nullable', 'string'],
            'produit_code' => ['nullable', 'string'],
            'produit_name' => ['nullable', 'string'],
            'company_code' => ['required', 'integer'],
            'quantite' => ['nullable', 'numeric'],
            'poids' => ['nullable', 'numeric'],
            'prix_unitaire' => ['required', 'numeric'],
            'remise' => ['nullable', 'numeric'],
            'date_livraison' => ['nullable', 'date'],
            'statut' => ['nullable', 'string'],
        ];
    }
}
