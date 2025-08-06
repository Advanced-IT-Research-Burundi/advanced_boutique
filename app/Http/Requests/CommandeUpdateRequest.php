<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommandeUpdateRequest extends FormRequest
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
            'vehicule_id' => ['required', 'integer'],
            'matricule' => ['nullable', 'string'],
            'commentaire' => ['nullable', 'string'],
            'poids' => ['nullable', 'numeric'],
            'date_livraison' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
