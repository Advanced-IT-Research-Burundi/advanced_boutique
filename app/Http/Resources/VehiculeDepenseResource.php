<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehiculeDepenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicule_id' => $this->vehicule_id,
            'amount' => $this->amount,
            'date' => $this->date,
            'description' => $this->description,
            'user_id' => $this->user_id,
        ];
    }
}
