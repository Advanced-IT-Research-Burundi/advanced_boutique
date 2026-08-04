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
        $category = $this->relationLoaded('category') ? $this->getRelation('category') : null;
        $unit = $this->relationLoaded('unit') ? $this->getRelation('unit') : null;

        return [
            "id" =>  $this->id,
            "code" => $this->code,
            "name" => $this->name,
            "description" => $this->description,
            "category_id" => $this->category_id,
            "category" => $category ? [
                "id" => $category->id,
                "name" => is_numeric($category->name) ? "Autre" : $category->name,
            ] : null,
            "purchase_price" => $this->purchase_price,
            "sale_price_ht" => $this->sale_price_ht,
            "sale_price_ttc" => $this->sale_price_ttc,
            "prix_promotionnel" => $this->prix_promotionnel,
            "unit_id" => $this->unit_id,
            "unit" => $unit ? [
                "id" => $unit->id,
                "name" => $unit->name,
                "abbreviation" => $unit->abbreviation,
            ] : $this->unit,
            "alert_quantity" => $this->alert_quantity,
            "image" => $this->image,
            "agency" => $this->whenLoaded('agency'),
        ];
    }
}
