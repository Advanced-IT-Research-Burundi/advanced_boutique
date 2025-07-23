<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\StockProductMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EntreMultipleController extends Controller
{    /**
     * Get stock categories for filtering
     */
    public function getStockCategories($stockId)
    {
        try {
            $categories = StockProduct::with('product.category')
                ->where('stock_id', $stockId)
                ->get()
                ->pluck('product.category')
                ->unique('id')
                ->filter()
                ->values()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name
                    ];
                });

            $data = [
                'categories' => $categories
            ];
            return sendResponse($data, 'Catégories récupérées avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors du chargement des catégories', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get stock products for entry
     */
    public function getProductsForEntry(Request $request)
    {
        try {
            $stockId = $request->get('stock_id');
            $search = $request->get('search', '');
            $categoryId = $request->get('category_id', '');
            $limit = $request->get('limit', 200);

            $query = StockProduct::with(['product', 'product.category'])
                ->where('stock_id', $stockId);

            // Search filter
            if ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($categoryId) {
                $query->whereHas('product', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            }

            $products = $query->join('products', 'stock_products.product_id', '=', 'products.id')
                ->orderBy('products.code')
                ->select('stock_products.*')
                // ->limit($limit)
                ->get();


            $data = [
                'products' => $products,
            ];
            return sendResponse($data, 'Produits récupérés avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors du chargement des produits', 500);
        }
    }

    /**
     * Process bulk entry
     */
    public function processBulkEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required|exists:stocks,id',
            'entries' => 'required|array|min:1',
            'entries.*.product_id' => 'required|exists:stock_products,id',
            'entries.*.quantity' => 'required|numeric|min:1',
            'entries.*.price' => 'required|numeric|min:0',
        ], [
            'entries.required' => 'Aucune entrée fournie',
            'entries.min' => 'Au moins une entrée est requise',
            'entries.*.product_id.required' => 'ID produit manquant',
            'entries.*.product_id.exists' => 'Produit non trouvé',
            'entries.*.quantity.required' => 'Quantité manquante',
            'entries.*.quantity.min' => 'La quantité doit être supérieure à 0',
            'entries.*.price.required' => 'Prix manquant',
            'entries.*.price.min' => 'Le prix doit être positif',
        ]);

        if ($validator->fails()) {
            return $this->sendError(
                'Données invalides',
                422,
                $validator->errors()
            );
        }

        try {
            DB::beginTransaction();

            $stockId = $request->stock_id;
            $entries = $request->entries;
            $entriesCount = 0;
            $totalQuantity = 0;
            $totalValue = 0;

            $stock = Stock::findOrFail($stockId);

            foreach ($entries as $entry) {
                $stockProduct = StockProduct::where('id', $entry['product_id'])
                    ->where('stock_id', $stockId)
                    ->first();

                if (!$stockProduct) {
                    continue;
                }

                $quantity = (float) $entry['quantity'];
                $price = (float) $entry['price'];

                // Update stock product
                $stockProduct->quantity += $quantity;
                $stockProduct->user_id = Auth::id();
                $stockProduct->agency_id = Auth::user()->agency_id ?? $stock->agency_id;
                $stockProduct->purchase_price = $price;
                $stockProduct->sale_price_ht = $price;
                $stockProduct->sale_price_ttc = $price;
                $stockProduct->save();

                // Create stock movement
                $this->createStockMovement($stockProduct, $quantity, $price, $stock);

                $entriesCount++;
                $totalQuantity += $quantity;
                $totalValue += ($quantity * $price);
            }

            DB::commit();

            if ($entriesCount > 0) {

                $data = [
                    'entries_count' => $entriesCount,
                    'total_quantity' => $totalQuantity,
                    'total_value' => $totalValue
                ];
                return sendResponse($data, "Entrée réussie pour {$entriesCount} produit(s)");
            } elseif ($entriesCount === 0) {
                return sendError('Aucune entrée valide à traiter', 422);
            } else {
                return sendError('Aucune entrée valide à traiter', 422);
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            return sendError(
                'Erreur lors de la mise à jour: ' ,
                500,
                ['error' => $e->getMessage()]
            );

        }
    }

    /**
     * Create stock movement record
     */
    private function createStockMovement($stockProduct, $quantity, $price, $stock)
    {
        try {
            StockProductMouvement::create([
                'agency_id' => Auth::user()->agency_id ?? $stock->agency_id,
                'stock_id' => $stock->id,
                'stock_product_id' => $stockProduct->id,
                'item_code' => $stockProduct->product->code ?? 'N/A',
                'item_designation' => $stockProduct->product->name ?? 'N/A',
                'item_quantity' => $quantity,
                'item_measurement_unit' => $stockProduct->product->unit ?? 'pcs',
                'item_purchase_or_sale_price' => $price,
                'item_purchase_or_sale_currency' => 'FBU',
                'item_movement_type' => 'EN',
                'item_movement_date' => now(),
                'item_movement_note' => 'Entrée multiple',
                'user_id' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            // Log error but don't fail the main transaction
            \Log::error('Failed to create stock movement: ' . $e->getMessage());
            return sendError('Erreur lors de la création du mouvement de stock', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get entry summary/statistics
     */
    public function getEntrySummary($stockId)
    {
        try {
            $stock = Stock::findOrFail($stockId);

            $totalProducts = StockProduct::where('stock_id', $stockId)->count();
            $totalQuantity = StockProduct::where('stock_id', $stockId)->sum('quantity');
            $totalValue = StockProduct::where('stock_id', $stockId)
                ->get()
                ->sum(function($item) {
                    return $item->quantity * $item->sale_price_ttc;
                });

            $recentEntries = StockProductMouvement::where('stock_id', $stockId)
                ->where('item_movement_type', 'EN')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
                $data = [
                    'summary' => [
                        'stock_name' => $stock->name,
                        'total_products' => $totalProducts,
                        'total_quantity' => $totalQuantity,
                        'total_value' => $totalValue,
                        'recent_entries' => $recentEntries
                    ]
                ];

            return sendResponse($data, 'Résumé récupéré avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors du chargement du résumé', 500);
        }
    }
}
