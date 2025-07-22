<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockProductsExport;

class StockProductController extends Controller
{

    /**
     * Get available products for stock
     */
    public function getAvailableProducts(Request $request)
{
    try {
        $stockId = $request->get('stock_id');
        $search = $request->get('search', '');

        $query = Product::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if (!empty($stockId)) {
            $query->whereNotIn('id', function ($subQuery) use ($stockId) {
                $subQuery->select('product_id')
                         ->from('stock_products')
                         ->where('stock_id', $stockId);
            });
        }

        $products = $query->take(50)->get();

       
        $data = [
            'products' => $products
        ];
        return sendResponse($data, 'Produits disponibles récupérés avec succès');

    } catch (\Exception $e) {

        return sendError('Erreur lors du chargement des produits', 500, $e->getMessage());
    }
}


    /**
     * Get stock products with pagination and search
     */
    public function getStockProducts(Request $request)
    {
        try {
            $stockId = $request->get('stock_id');
            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 10);

            $query = StockProduct::with(['product', 'category'])
                ->where('stock_id', $stockId);

            if ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $stockProducts = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);


            $data = [
                'stock_products' => $stockProducts
            ];
            return sendResponse($data, 'Produits du stock récupérés avec succès');
        } catch (\Exception $e) {

            return sendError('Erreur lors du chargement des produits du stock', 500, $e->getMessage());
        }
    }

    /**
     * Add product to stock
     */
    public function addProduct(Request $request)
    {
        try {
            $request->validate([
                'stock_id' => 'required|exists:stocks,id',
                'product_id' => 'required|exists:products,id'
            ]);

            $stock = Stock::findOrFail($request->stock_id);
            $product = Product::findOrFail($request->product_id);

            // Check if product already exists in stock
            $existingStockProduct = StockProduct::where('stock_id', $request->stock_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingStockProduct) {

                return sendError('Ce produit est déjà dans le stock', 422);
            }

            $stockProduct = new StockProduct();
            $stockProduct->stock_id = $request->stock_id;
            $stockProduct->product_id = $request->product_id;
            $stockProduct->quantity = 0;
            $stockProduct->agency_id = $stock->agency_id;
            $stockProduct->user_id = Auth::id();
            $stockProduct->product_name = $product->name;
            $stockProduct->sale_price_ttc = $product->sale_price ?? 0;
            $stockProduct->save();


            $data = [
                'stock_product' => $stockProduct->load(['product', 'category'])
            ];
            return sendResponse($data, 'Produit ajouté au stock avec succès', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return sendError('Données invalides', 422, $e->errors());
        } catch (\Exception $e) {

            return sendError('Erreur lors de l\'ajout du produit au stock', 500, $e->getMessage());
        }
    }

    /**
     * Remove product from stock
     */
    public function removeProduct($id)
    {
        try {
            $stockProduct = StockProduct::findOrFail($id);

            // Check if product has quantity > 0
            if ($stockProduct->quantity > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un produit avec une quantité en stock'
                ], 422);
            }

            $stockProduct->delete();


            return sendResponse(null, 'Produit retiré du stock avec succès', 200);
        } catch (\Exception $e) {

            return sendError('Erreur lors de la suppression du produit: ', 500, $e->getMessage());
        }
    }

    /**
     * Export stock products to Excel
     */
    public function exportToExcel($stockId)
    {
        try {
            $stock = Stock::findOrFail($stockId);

            $stockProducts = StockProduct::with(['product', 'category'])
                ->where('stock_id', $stockId)
                ->get();

            $filename = 'stock_' . Str::slug($stock->name) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new StockProductsExport($stockProducts, $stock), $filename);

        } catch (\Exception $e) {

            return sendError('Erreur lors de l\'export Excel: ', 500, $e->getMessage());
        }
    }

    /**
     * Export stock products to PDF
     */
    public function exportToPdf($stockId)
    {
        try {
            $stock = Stock::findOrFail($stockId);

            $stockProducts = StockProduct::with(['product', 'category'])
                ->where('stock_id', $stockId)
                ->get();

            $pdf = PDF::loadView('exports.stock-products-pdf', compact('stockProducts', 'stock'))
                ->setPaper('a4', 'landscape');

            $filename = 'stock_' . Str::slug($stock->name) . '_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {

            return sendError('Erreur lors de l\'export PDF: ', 500, $e->getMessage());
        }
    }

    /**
     * Get stock statistics
     */
    public function getStockStats($stockId)
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
            $lowStockCount = StockProduct::where('stock_id', $stockId)
                ->where('quantity', '<=', 5)
                ->count();


            $data = [
                'stats' => [
                    'total_products' => $totalProducts,
                    'total_quantity' => $totalQuantity,
                    'total_value' => $totalValue,
                    'low_stock_count' => $lowStockCount
                ]
            ];
            return sendResponse($data, 'Statistiques du stock récupérées avec succès', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques'
            ], 500);
        }
    }

    /**
     * Update stock product quantity
     */
    public function updateQuantity(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|numeric|min:0'
            ]);

            $stockProduct = StockProduct::findOrFail($id);
            $stockProduct->quantity = $request->quantity;
            $stockProduct->save();


            $data = [
                'stock_product' => $stockProduct->load(['product', 'category'])
            ];
            return sendResponse($data, 'Quantité mise à jour avec succès', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return sendError('Données invalides', 422, $e->errors());
        } catch (\Exception $e) {

            return sendError('Erreur lors de la mise à jour de la quantité', 500, $e->getMessage());
        }
    }
    public function addBulkProducts(Request $request)
    {
        try {
            $request->validate([
                'stock_id' => 'required|exists:stocks,id',
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'exists:products,id'
            ]);

            $stock = Stock::findOrFail($request->stock_id);
            $addedCount = 0;

            foreach ($request->product_ids as $productId) {
                // Vérifier si le produit n'est pas déjà dans le stock
                $exists = StockProduct::where('stock_id', $request->stock_id)
                    ->where('product_id', $productId)
                    ->exists();

                if (!$exists) {
                    $product = Product::find($productId);
                    $stockProduct = new StockProduct();
                    $stockProduct->stock_id = $request->stock_id;
                    $stockProduct->product_id = $productId;
                    $stockProduct->quantity = 0;
                    $stockProduct->agency_id = $stock->agency_id;
                    $stockProduct->user_id = Auth::id();
                    $stockProduct->product_name = $product->name;
                    $stockProduct->sale_price_ttc = $product->sale_price ?? 0;
                    $stockProduct->save();
                    $addedCount++;
                }
            }


            return sendResponse($data, "{$addedCount} produit(s) ajouté(s) au stock avec succès");
        } catch (\Exception $e) {



            return sendError('Erreur lors de l\'ajout des produits: ' , 500, $e->getMessage());
        }
    }
}
