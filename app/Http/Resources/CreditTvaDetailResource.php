<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditTvaDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'credit_tva_id' => $this->credit_tva_id,
            'type' => $this->type,
            'montant' => $this->montant,
            'sale_id' => $this->sale_id,
            'description' => $this->description,
            'date' => $this->date,
        ];
    }
}
