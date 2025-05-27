<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
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
            'payment_type' => ['required', 'string'],
            'reference_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric'],
            'payment_method' => ['required', 'string'],
            'payment_date' => ['required'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'created_by' => ['required'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
