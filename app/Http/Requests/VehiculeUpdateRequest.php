<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehiculeUpdateRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'model' => ['nullable', 'string'],
            'brand' => ['nullable', 'string'],
            'year' => ['nullable', 'integer'],
            'color' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'status' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'created_by' => ['required'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
