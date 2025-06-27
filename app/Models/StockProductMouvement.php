<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProductMouvement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function addStockProductMouvement(StockProduct $stockProduct, double $quantite,Sale $sale , string $type,string $description,  string $note = 'Vente Normal'){

        StockProductMouvement::create([
            'agency_id' => auth()->user()->agency_id,
            'stock_id' => $stockProduct->stock_id,
            'stock_product_id' => $stockProduct->id,
            'item_code' => $stockProduct->product->id,
            'item_designation' => $stockProduct->product->name,
            'item_quantity' => $quantite,
            'item_measurement_unit' => $stockProduct->product->unit ?? 'Piece',
            'item_purchase_or_sale_price' => $stockProduct->product->sale_price_ht,
            'item_purchase_or_sale_currency' => $stockProduct->product->sale_price_currency ?? 'BIF',
            'item_movement_type' => $type,
            'item_movement_invoice_ref' => $sale->invoice_number,
            'item_movement_description' => $description,
            'item_movement_date' => now(),
            'item_product_detail_id' => $stockProduct->product->id,
            'is_send_to_obr' => null,
            'is_sent_at' => null,
            'user_id' => auth()->user()->id,
            'item_movement_note' => $note,
        ]);
    }
}
