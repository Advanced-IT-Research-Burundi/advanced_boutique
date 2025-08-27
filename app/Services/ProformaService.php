<?php

namespace App\Services;

use App\Models\Proforma;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockProduct;
use App\Models\StockProductMouvement;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\Auth;

class ProformaService
{
    public function valider(Proforma $proforma, $caisse, $note = null)
    {
        $proformaItems = json_decode($proforma->proforma_items, true);
        $clientData = json_decode($proforma->client, true);

        if (empty($proformaItems)) {
            throw new \Exception('Aucun article trouvé dans le proforma');
        }

        $sale = Sale::create([
            'client_id' => $clientData['id'],
            'stock_id' => $proforma->stock_id,
            'user_id' => $proforma->user_id,
            'total_amount' => $proforma->total_amount,
            'paid_amount' => 0,
            'due_amount' => $proforma->due_amount,
            'sale_date' => now(),
            'type_facture' => 'F. PROFORMA',
            'agency_id' => $proforma->agency_id,
            'created_by' => auth()->id() ?? $proforma->created_by,
        ]);

        foreach ($proformaItems as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'sale_price' => $item['sale_price'],
                'discount' => $item['discount'] ?? 0,
                'subtotal' => $item['quantity'] * $item['sale_price'],
                'agency_id' => $proforma->agency_id,
                'created_by' => auth()->id() ?? $proforma->created_by,
                'user_id' => $proforma->user_id,
            ]);

            $stockProduct = StockProduct::where('product_id', $item['product_id'])
                ->where('stock_id', $proforma->stock_id)
                ->first();

            if (!$stockProduct || $stockProduct->quantity < $item['quantity']) {
                throw new \Exception("Stock insuffisant pour le produit code :  {$stockProduct->product->code} - {$stockProduct->product->name}");
            }

            $stockProduct->update([
                'quantity' => $stockProduct->quantity - $item['quantity']
            ]);

            StockProductMouvement::create([
                'agency_id' => Auth::user()->agency_id,
                'stock_id' => $stockProduct->stock_id,
                'stock_product_id' => $stockProduct->id,
                'item_code' => $stockProduct->id,
                'item_designation' => $stockProduct->product->name,
                'item_quantity' => $item['quantity'],
                'item_measurement_unit' => $stockProduct->product->unit ?? 'Piece',
                'item_purchase_or_sale_price' => $stockProduct->sale_price_ttc ?? 0,
                'item_purchase_or_sale_currency' => $stockProduct->product->sale_price_currency ?? 'BIF',
                'item_movement_type' => 'SN',
                'item_movement_invoice_ref' => $sale->id,
                'item_movement_description' => 'Vente',
                'item_movement_date' => now(),
                'item_product_detail_id' => $stockProduct->product->id,
                'user_id' => Auth::id(),
                'item_movement_note' => 'Vente Normale',
            ]);
        }

        $proforma->update([
            'invoice_type' => 'F. PROFORMA VALIDÉE',
            'is_valid' => true,
            'sale_date' => now(),
            'updated_at' => now()
        ]);

        CashTransaction::create([
            'cash_register_id' => $caisse->id,
            'type' => 'in',
            'reference_id' => 'Ref ' . $sale->id,
            'amount' => $proforma->total_amount,
            'description' => $note ?? 'Vente Normale facture no '.$sale->id,
            'agency_id' => $caisse->agency_id,
            'created_by' => Auth::id(),
            'user_id' => Auth::id(),
        ]);

        return $sale;
    }
}
