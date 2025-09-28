<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduitsTmpStoreRequest extends FormRequest
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
            'code' => ['nullable', 'string'],
            'designation' => ['nullable', 'string'],
            'PVHT' => ['nullable', 'numeric'],
            'PVTTC' => ['nullable', 'numeric'],
        ];
    }
}
