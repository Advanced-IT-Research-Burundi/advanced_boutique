<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric'],
            'sale_price' => ['required', 'numeric'],
            'unit' => ['required', 'string'],
            'alert_quantity' => ['required', 'numeric'],
            'created_by' => ['required'],
        ];
    }
}
