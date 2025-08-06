<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'commande_id' => $this->commande_id,
            'produit_id' => $this->produit_id,
            'produit_code' => $this->produit_code,
            'produit_name' => $this->produit_name,
            'company_code' => $this->company_code,
            'quantite' => $this->quantite,
            'poids' => $this->poids,
            'prix_unitaire' => $this->prix_unitaire,
            'remise' => $this->remise,
            'date_livraison' => $this->date_livraison,
            'statut' => $this->statut,
        ];
    }
}
