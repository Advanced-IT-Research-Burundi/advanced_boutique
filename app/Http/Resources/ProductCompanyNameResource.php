<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCompanyNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_code' => $this->product_code,
            'company_code' => $this->company_code,
            'item_name' => $this->item_name,
            'size' => $this->size,
            'packing_details' => $this->packing_details,
            'mfg_location' => $this->mfg_location,
            'weight_kg' => $this->weight_kg,
            'order_qty' => $this->order_qty,
            'total_weight' => $this->total_weight,
            'pu' => $this->pu,
            'total_weight_pu' => $this->total_weight_pu,
        ];
    }
}
