<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCompanyNameStoreRequest extends FormRequest
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
            'product_code' => ['required', 'string'],
            'company_code' => ['required', 'string'],
            'item_name' => ['required', 'string'],
            'size' => ['required', 'string'],
            'packing_details' => ['required', 'string'],
            'mfg_location' => ['required', 'string'],
            'weight_kg' => ['required', 'numeric'],
            'order_qty' => ['required', 'numeric'],
            'total_weight' => ['required', 'numeric'],
            'pu' => ['required', 'string'],
            'total_weight_pu' => ['required', 'numeric'],
        ];
    }
}
