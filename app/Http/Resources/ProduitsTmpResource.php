<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProduitsTmpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'designation' => $this->designation,
            'PVHT' => $this->PVHT,
            'PVTTC' => $this->PVTTC,
        ];
    }
}
