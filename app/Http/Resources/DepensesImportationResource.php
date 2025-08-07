<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepensesImportationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'depense_importation_type' => $this->depense_importation_type,
            'depense_importation_type_id' => $this->depense_importation_type_id,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate,
            'amount' => $this->amount,
            'amount_currency' => $this->amount_currency,
            'date' => $this->date,
            'description' => $this->description,
            'user_id' => $this->user_id,
        ];
    }
}
