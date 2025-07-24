<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Category;
use App\Models\StockProduct;
use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\Proforma;
use App\Models\StockProductMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Données initiales pour la création de vente
     */
    public function getCreateData()
    {
        try {
            $user = Auth::user();

            // Récupérer les stocks de l'utilisateur
            $stocks = auth()->user()->stocks()
                ->get();
            // $stocks = $user->stocks()
            //     ->withCount('products')
            //     ->select('id', 'name', 'agency_id')
            //     ->get()
            //     ->map(function ($stock) {
            //         return [
            //             'id' => $stock->id,
            //             'name' => $stock->name,
            //             'products_count' => $stock->products_count
            //         ];
            //     });



            $data = [
                'stocks' => $stocks,
                'current_date' => now()->format('Y-m-d\TH:i'),
                'invoice_types' => ['FACTURE', 'PROFORMA', 'BON']
            ];

         return sendResponse($data, 'Produits récupérés avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors du chargement des données', 500, ['error' => $e->getMessage()]);

        }
    }

    /**
     * Récupérer les catégories pour un stock donné
     */
    public function getCategories($stockId)
    {
        try {
            $categories = Category::select('id', 'name')
                ->withCount(['products' => function ($query) use ($stockId) {
                    $query->whereHas('stockProducts', function ($q) use ($stockId) {
                        $q->where('quantity', '>', 0)
                          ->where('stock_id', $stockId);
                    });
                }])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id');


            $data = [
                'categories' => $categories
            ];
            return sendResponse($data, 'Catégories récupérées avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors du chargement des catégories', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Recherche de clients
     */
    public function searchClients(Request $request)
    {
        try {
            $search = $request->get('search', '');

            // if (strlen($search) < 2) {
            //     return response()->json([
            //         'success' => true,
            //         'data' => ['clients' => []]
            //     ]);

            // }

            $clients = Client::select('id', 'name', 'phone', 'email')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get();


            $data = [
                'clients' => $clients
            ];
            return sendResponse($data, 'Clients récupérés avec succès');
        } catch (\Throwable $e) {
            return sendError('Erreur lors de la recherche de clients', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Recherche de produits
     */
    public function searchProducts(Request $request)
    {
        try {
            $stockId = $request->get('stock_id');
            $search = $request->get('search', '');
            $categoryId = $request->get('category_id');
            $perPage = $request->get('per_page', 50);

            if (!$stockId) {

                return sendError('Stock ID requis', 400, ['error' => 'Stock ID requis']);
            }

            $query = Product::with(['stockProducts' => function ($query) use ($stockId) {
                    $query->where('stock_id', $stockId);
                }])
                ->select('id', 'name', 'code', 'description', 'sale_price_ttc', 'unit', 'image', 'category_id')
                ->whereHas('stockProducts', function ($query) use ($stockId) {
                    $query->where('quantity', '>=', 0)
                          ->where('stock_id', $stockId);
                });

            // Recherche par mot-clé
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filtrage par catégorie
            if (!empty($categoryId)) {
                $query->where('category_id', $categoryId);
            }

            $products = $query->orderBy('name')
                            ->limit($perPage)
                            ->get();

            // Enrichir avec les données de stock
            $products->each(function ($product) use ($stockId) {
                $stockProduct = $product->stockProducts->first();
                $product->quantity_disponible = $stockProduct ? $stockProduct->quantity : 0;
                $product->stock_id = $stockId;

                // Nettoyer les relations pour réduire la taille de la réponse
                unset($product->stockProducts);
            });



            $data = [
                'products' => $products
            ];
            return sendResponse($data, 'Produits récupérés avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la recherche de produits', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Créer une nouvelle vente
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'stock_id' => 'required|exists:stocks,id',
            'sale_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'invoice_type' => 'required|in:FACTURE,PROFORMA,BON',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'sale_date.required' => 'La date de vente est obligatoire.',
            'paid_amount.required' => 'Le montant payé est obligatoire.',
            'items.required' => 'Veuillez ajouter au moins un produit.',
            'items.min' => 'Veuillez ajouter au moins un produit.',
        ]);

        if ($validator->fails()) {

            return sendError('Données invalides', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            // Vérifier la caisse
            $caisse = CashRegister::where('user_id', Auth::id())->first();
            if (!$caisse) {

                return sendError('Caisse introuvable', 403, ['error' => 'Vous n\'avez pas le droit de créer une facture.']);
            }

            // Valider les stocks
            $stockErrors = $this->validateStock($request->items, $request->stock_id);
            if (!empty($stockErrors)) {
                return sendError('Stocks insuffisants', 400, ['errors' => $stockErrors]);
            }

            // Calculer les totaux
            $totals = $this->calculateTotals($request->items);

            if ($request->invoice_type === 'FACTURE') {
                $result = $this->createSale($request, $totals, $caisse);
            } else {
                return sendError('Type de facture non supporté', 400, ['error' => 'Type de facture non supporté']);
            }

            DB::commit();


            $data = $result;
            $message = $request->invoice_type === 'PROFORMA'
                ? 'Proforma enregistrée avec succès'
                : 'Vente enregistrée avec succès';
            return sendResponse($data, $message);

        } catch (\Exception $e) {
            DB::rollBack();


             return sendError('Erreur lors de l\'enregistrement', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Valider les stocks
     */
    private function validateStock($items, $stockId)
    {
        $errors = [];
        $productIds = collect($items)->pluck('product_id')->toArray();

        // Récupérer tous les stocks en une requête
        $stockProducts = StockProduct::whereIn('product_id', $productIds)
            ->where('stock_id', $stockId)
            ->with('product:id,name')
            ->get()
            ->keyBy('product_id');

        foreach ($items as $item) {
            $stockProduct = $stockProducts->get($item['product_id']);
            $availableStock = $stockProduct ? $stockProduct->quantity : 0;

            if ($item['quantity'] > $availableStock) {
                $productName = $stockProduct ? $stockProduct->product->name : "Produit #{$item['product_id']}";
                $errors[] = "Stock insuffisant pour {$productName}. Stock disponible: {$availableStock}, Demandé: {$item['quantity']}";
            }
        }

        return $errors;
    }

    /**
     * Calculer les totaux
     */
    private function calculateTotals($items)
    {
        $subtotal = 0;
        $totalDiscount = 0;

        foreach ($items as $item) {
            $quantity = floatval($item['quantity']);
            $price = floatval($item['sale_price']);
            $discount = floatval($item['discount'] ?? 0);

            $itemSubtotal = $quantity * $price;
            $itemDiscountAmount = ($itemSubtotal * $discount) / 100;

            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscountAmount;
        }

        return [
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'total_amount' => $subtotal - $totalDiscount
        ];
    }

    /**
     * Créer une proforma
     */
    private function createProforma($request, $totals, $caisse)
    {
        $dueAmount = $totals['total_amount'] - $request->paid_amount;

        $proforma = Proforma::create([
            'client_id' => $request->client_id,
            'stock_id' => $request->stock_id,
            'user_id' => Auth::id(),
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $request->paid_amount,
            'due_amount' => $dueAmount,
            'sale_date' => Carbon::parse($request->sale_date),
            'note' => $request->note,
            'invoice_type' => $request->invoice_type,
            'agency_id' => Auth::user()->agency_id,
            'created_by' => Auth::id(),
            'proforma_items' => json_encode($request->items),
            'client' => json_encode(Client::find($request->client_id)),
        ]);

        return [
            'type' => 'proforma',
            'id' => $proforma->id,
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $request->paid_amount,
            'due_amount' => $dueAmount
        ];
    }

    /**
     * Créer une vente
     */
    private function createSale($request, $totals, $caisse)
    {
        $dueAmount = $totals['total_amount'] - $request->paid_amount;

        // Créer la vente
        $sale = Sale::create([
            'client_id' => $request->client_id,
            'stock_id' => $request->stock_id,
            'user_id' => Auth::id(),
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $request->paid_amount,
            'due_amount' => $dueAmount,
            'type_facture' => 'F. NORMALE',
            'sale_date' => Carbon::parse($request->sale_date),
            'note' => $request->note,
            'agency_id' => Auth::user()->agency_id,
            'created_by' => Auth::id(),
        ]);

        // Créer les items de vente et mettre à jour les stocks
        foreach ($request->items as $item) {
            // Calculer le sous-total de l'item
            $quantity = floatval($item['quantity']);
            $price = floatval($item['sale_price']);
            $discount = floatval($item['discount'] ?? 0);
            $itemSubtotal = $quantity * $price;
            $itemDiscountAmount = ($itemSubtotal * $discount) / 100;
            $finalSubtotal = $itemSubtotal - $itemDiscountAmount;

            // Créer l'item de vente
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'sale_price' => $price,
                'discount' => $discount,
                'subtotal' => $finalSubtotal,
                'agency_id' => Auth::user()->agency_id,
                'created_by' => Auth::id(),
                'user_id' => Auth::id(),
            ]);

            // Mettre à jour le stock
            $stockProduct = StockProduct::where('product_id', $item['product_id'])
                ->where('stock_id', $request->stock_id)
                ->first();

            if (!$stockProduct || $stockProduct->quantity < $quantity) {
                throw new \Exception("Stock insuffisant pour le produit {$item['product_id']}");
            }

            $stockProduct->update([
                'quantity' => $stockProduct->quantity - $quantity
            ]);

            // Créer le mouvement de stock
            StockProductMouvement::create([
                'agency_id' => Auth::user()->agency_id,
                'stock_id' => $stockProduct->stock_id,
                'stock_product_id' => $stockProduct->id,
                'item_code' => $stockProduct->id,
                'item_designation' => $stockProduct->product->name,
                'item_quantity' => $quantity,
                'item_measurement_unit' => $stockProduct->product->unit ?? 'Piece',
                'item_purchase_or_sale_price' => $price,
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

        // Créer la transaction de caisse
        CashTransaction::create([
            'cash_register_id' => $caisse->id,
            'type' => 'in',
            'reference_id' => 'Ref ' . $sale->id,
            'amount' => $totals['total_amount'],
            'description' => $request->note ?? 'Vente Normale facture no '.$sale->id,
            'agency_id' => $caisse->agency_id,
            'created_by' => Auth::id(),
            'user_id' => Auth::id(),
        ]);

        return [
            'type' => 'sale',
            'id' => $sale->id,
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $request->paid_amount,
            'due_amount' => $dueAmount
        ];
    }

    /**
     * Récupérer les détails d'un produit avec son stock
     */
    public function getProductStock(Request $request, $productId)
    {
        try {
            $stockId = $request->get('stock_id');

            if (!$stockId) {
                return sendError('Stock ID requis', 400, ['error' => 'Stock ID requis']);
            }

            $product = Product::with(['stockProducts' => function ($query) use ($stockId) {
                    $query->where('stock_id', $stockId);
                }])
                ->find($productId);

            if (!$product) {

                return sendError('Produit non trouvé', 404, ['error' => 'Produit non trouvé']);
            }

            $stockProduct = $product->stockProducts->first();
            $availableStock = $stockProduct ? $stockProduct->quantity : 0;


            $data = [
                'product' => $product,
                'available_stock' => $availableStock,
                'stock_id' => $stockId
            ];
            return sendResponse($data, 'Produit récupéré avec succès');
        } catch (\Throwable $e) {
            return sendError('Erreur lors de la récupération du produit: ' . $e->getMessage(), 500);
        }
    }
}
