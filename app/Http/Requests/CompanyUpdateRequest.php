<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
            'tp_name' => ['required', 'string'],
            'tp_type' => ['required', 'string'],
            'tp_TIN' => ['required', 'string'],
            'tp_trade_number' => ['nullable', 'string'],
            'tp_postal_number' => ['nullable', 'string'],
            'tp_phone_number' => ['nullable', 'string'],
            'tp_address_province' => ['nullable', 'string'],
            'tp_address_commune' => ['nullable', 'string'],
            'tp_address_quartier' => ['nullable', 'string'],
            'tp_address_avenue' => ['nullable', 'string'],
            'tp_address_rue' => ['nullable', 'string'],
            'tp_address_number' => ['nullable', 'string'],
            'vat_taxpayer' => ['nullable', 'string'],
            'ct_taxpayer' => ['nullable', 'string'],
            'tl_taxpayer' => ['nullable', 'string'],
            'tp_fiscal_center' => ['nullable', 'string'],
            'tp_activity_sector' => ['nullable', 'string'],
            'tp_legal_form' => ['nullable', 'string'],
            'payment_type' => ['nullable', 'string'],
            'is_actif' => ['required'],
            'created_by' => ['required'],
        ];
    }
}
