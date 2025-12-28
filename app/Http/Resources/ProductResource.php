<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             "id" =>  $this->id,
            "code" => $this->code, 
            "name" => $this->name,
            "description" => $this->description,
            "category_id" => $this->category_id,
            "purchase_price" => $this->purchase_price,
            "sale_price_ht" => $this->sale_price_ht,
            "sale_price_ttc" => $this->sale_price_ttc,
            "prix_promotionnel" => $this->prix_promotionnel,
            "unit" => $this->unit
        ];
    }
}
