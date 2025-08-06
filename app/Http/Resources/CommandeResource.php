<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicule_id' => $this->vehicule_id,
            'matricule' => $this->matricule,
            'commentaire' => $this->commentaire,
            'poids' => $this->poids,
            'date_livraison' => $this->date_livraison,
            'description' => $this->description,
        ];
    }
}
