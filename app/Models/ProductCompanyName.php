<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCompanyName extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_code',
        'company_code',
        'item_name',
        'size',
        'packing_details',
        'mfg_location',
        'weight_kg',
        'order_qty',
        'total_weight',
        'pu',
        'total_weight_pu',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'weight_kg' => 'double',
            'order_qty' => 'double',
            'total_weight' => 'double',
            'total_weight_pu' => 'float',
        ];
    }
}
