<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proforma;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockProduct;
use App\Models\StockProductMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function getStocks()
    {
        try {
            $stocks = Stock::select('id', 'name')->get();
            return sendResponse(['stocks' => $stocks], 'Stocks récupérés avec succès');
        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération des stocks', 500, $e->getMessage());
        }
    }

    public function getStockProformas($id)
    {
        try {
            $proformas = Stock::find($id)->proformaNonValides()->get();

            return sendResponse(['proformas' => $proformas], 'Proformas récupérées avec succès');
        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération des proformas', 500, $e->getMessage());
        }
    }
    public function getStockCategories($stockId)
    {
        try {
            $categories = Category::with('products.stockProducts')
                ->whereHas('products.stockProducts', function ($query) use ($stockId) {
                    $query->where('stock_id', $stockId);
                })->get();

            return sendResponse(['categories' => $categories], 'Catégories récupérées avec succès');
        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération des catégories', 500, $e->getMessage());
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $stockId = $request->stock_id;
            $categoryId = $request->category_id;
            $search = $request->search;
            $excludeProducts = $request->exclude_products ?? [];

            // Convertir exclude_products en tableau si c'est une chaîne
            if (is_string($excludeProducts)) {
                $excludeProducts = array_filter(explode(',', $excludeProducts));
            }

            $query = Product::with(['stockProducts', 'category'])
                ->whereHas('stockProducts', function ($query) use ($stockId) {
                    $query->where('stock_id', $stockId);
                });

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            if (!empty($excludeProducts)) {
                $query->whereNotIn('id', $excludeProducts);
            }

            $products = $query->get()->map(function ($product) use ($stockId) {
                $stockProduct = $product->stockProducts->where('stock_id', $stockId)->first();
                $product->stock_quantity = $stockProduct ? $stockProduct->quantity : 0;
                return $product;
            });

            return sendResponse(['products' => $products], 'Produits récupérés avec succès');
        } catch (\Throwable $e) {
            return sendError('Erreur lors de la récupération des produits', 500, $e->getMessage());
        }
    }

    public function getProformaProducts(Request $request)
    {
        try {
            $stockId = $request->stock_id;
            $productIds = $request->product_ids;


            if (!$stockId || !$productIds) {
                return sendError('Stock ID ou Product IDs sont requis', 400);
            }

            if (is_string($productIds)) {
                $productIds = array_filter(explode(',', $productIds));
            }

            $products = StockProduct::with('product', 'product.category')
                ->where('stock_id', $stockId)
                ->whereIn('id', $productIds)
                ->get()
            ;

            $products = $products->map(function ($stockProduct) {
                return [
                    "id" => $stockProduct->id,
                    "name" => $stockProduct?->product?->name,
                    "code" => $stockProduct?->product?->code,
                    "description" => $stockProduct?->product?->name,
                    "category_id" => $stockProduct?->product?->category_id,
                    "stock_quantity" => $stockProduct->quantity,
                    "sale_price" =>  $stockProduct?->product?->name,
                    "category" => [
                        "id" => $stockProduct?->product?->category?->id,
                        "name" => $stockProduct?->product?->category?->name,
                    ]
                ];
            });

            return sendResponse(['products' => $products], 'Produits du proforma récupérés avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors de la récupération des produits du proforma', 500, $e->getMessage());
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_stock_id' => 'required|exists:stocks,id',
            'to_stock_id' => 'required|exists:stocks,id|different:from_stock_id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:stock_products,id',
            'products.*.quantity' => 'required|integer|min:1',
            "proforma_id" => "nullable|exists:proformas,id",
        ]);
        DB::beginTransaction();
        try {
            $fromStock = Stock::find($request->from_stock_id);
            $toStock = Stock::find($request->to_stock_id);
            $transferCode = time();
            $proforma = null;
             
            if($request->proforma_id){
                
                    $proforma = Proforma::findOrFail($request->proforma_id);
                    if($proforma->transfer_code != null){
                        throw new \Exception("Le proforma a déjà été utilisé pour un transfert.");
                    }
                    $proforma->transfer_code = $transferCode;
                    $proforma->stock_recevant_id = $request->to_stock_id;
                    $proforma->status = 'ACCEPTED';

                    $listeValidate = [];
                    $errors = [];
                    foreach( $request->products as $item){
                        $stockProduct = StockProduct::where('stock_id', $request->from_stock_id)
                        ->where('id', $item['product_id'])
                        ->where('quantity', '>=', $item['quantity'])
                        ->first();
                       
                        if(!$stockProduct){
                            $product = Product::find($item['product_id']);
                            $errors[] = "Quantité insuffisante pour le produit {$product->name} # {$product->code}. Stock disponible: {$stockProduct['quantity']}, Demandé: {$item['quantity']}";
                        }else{
                            $listeValidate[] = $stockProduct;
                        }
                    }
                    if(count($errors) > 0){
                        return sendError('Erreur de validation', 500, $errors);
                    }
                    $proforma->is_valid = true;
                    $proforma->status = 'ACCEPTED';
                    $proforma->save();
            }

            
           
            foreach ($request->products as $productData) {
                $product =StockProduct::find($productData['product_id'])->product;
                $quantity = $productData['quantity'];
                // Vérifier le stock source
                $sourceStockProduct = $product->stockProducts()
                    ->where('stock_id', $request->from_stock_id)
                    ->first();
                if (!$sourceStockProduct || $sourceStockProduct->quantity < $quantity) {
                    throw new \Exception("Quantité insuffisante pour le produit {$product->name}");
                }
                // Mettre à jour le stock source
                $sourceStockProduct->quantity -= $quantity;
                $sourceStockProduct->save();
                // Mettre à jour ou créer le stock destination
                $destStockProduct = $product->stockProducts()
                    ->where('stock_id', $request->to_stock_id)
                    ->first();

                if (!$destStockProduct) {
                    $destStockProduct = StockProduct::create([
                        'stock_id' => $request->to_stock_id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                    ]);
                } else {
                    $destStockProduct->quantity += $quantity;
                    $destStockProduct->save();
                }

                // Enregistrer le transfert
                StockTransfer::create([
                    'from_stock_id' => $request->from_stock_id,
                    'to_stock_id' => $request->to_stock_id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => 0,
                    'user_id' => auth()->id(),
                    'transfer_date' => now(),
                    'note' => $transferCode,
                    'transfer_code' => $transferCode,
                    'product_code' => $product->code,
                    'product_name' => $product->name,
                    'agency_id' => auth()->user()->agency_id,
                    'created_by' => auth()->id(),
                ]);
                $this->createStockMovements($fromStock, $toStock, $product, $quantity, $transferCode, $sourceStockProduct, $destStockProduct);
            }


            DB::commit();         
            $data =[
                'transfer_code' => $transferCode,
            ];
            return sendResponse($data, 'Transfert effectué avec succès');

        } catch (\Throwable $e) {
            DB::rollBack();
            return sendError('Erreur lors du transfert: ' . $e->getMessage(), 500, $e);
        }
    }

    private function createStockMovements($fromStock, $toStock, $product, $quantity, $transferCode, $sourceStockProduct, $destStockProduct)
    {
        // Mouvement de sortie (stock source)
        StockProductMouvement::create([
            'agency_id' => auth()->user()->agency_id,
            'stock_id' => $fromStock->id,
            'stock_product_id' => $sourceStockProduct->id,
            'item_code' => $product->code,
            'item_designation' => $product->name,
            'item_quantity' => $quantity,
            'item_measurement_unit' => $product->unit ?? 'Piece',
            'item_purchase_or_sale_price' => $product->sale_price_ht,
            'item_purchase_or_sale_currency' => $product->sale_price_currency ?? 'BIF',
            'item_movement_type' => 'ST',
            'item_movement_invoice_ref' => '',
            'item_movement_description' => $transferCode . ' - Transfert vers ' . $toStock->name,
            'item_movement_date' => now(),
            'item_product_detail_id' => $product->id,
            'user_id' => auth()->id(),
            'item_movement_note' => 'Transfert de stock',
        ]);

        // Mouvement d'entrée (stock destination)
        StockProductMouvement::create([
            'agency_id' => auth()->user()->agency_id,
            'stock_id' => $toStock->id,
            'stock_product_id' => $destStockProduct->id,
            'item_code' => $product->code,
            'item_designation' => $product->name,
            'item_quantity' => $quantity,
            'item_measurement_unit' => $product->unit ?? 'Piece',
            'item_purchase_or_sale_price' => $product->sale_price_ht,
            'item_purchase_or_sale_currency' => $product->sale_price_currency ?? 'BIF',
            'item_movement_type' => 'ET',
            'item_movement_invoice_ref' => $transferCode,
            'item_movement_description' => $transferCode . ' - Entrée depuis ' . $fromStock->name,
            'item_movement_date' => now(),
            'item_product_detail_id' => $product->id,
            'user_id' => auth()->id(),
            'item_movement_note' => 'Transfert de destination',
        ]);
    }
}
