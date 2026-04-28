<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutreElementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'libelle' => $this->libelle,
            'emplacement' => $this->emplacement,
            'quantite' => $this->quantite,
            'valeur' => $this->valeur,
            'devise' => $this->devise,
            'type_element' => $this->type_element,
            'reference' => $this->reference,
            'observation' => $this->observation,
            'document' => $this->document,
        ];
    }
}
