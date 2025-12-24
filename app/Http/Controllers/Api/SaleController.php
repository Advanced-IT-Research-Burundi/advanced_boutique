<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\Company;
use App\Models\Client;
use App\Models\Category;
use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\StockProductMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Liste des ventes avec filtres
     */
    public function index(Request $request)
    {
        try {
            $query = Sale::with(['client', 'saleItems.product', 'user'])
                        ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('client', function($clientQuery) use ($search) {
                          $clientQuery->where('name', 'like', "%{$search}%")
                                     ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            }

            if ($request->filled('status')) {
                switch ($request->status) {
                    case 'paid':
                        $query->where('due_amount', 0);
                        break;
                    case 'partial':
                        $query->where('due_amount', '>', 0)
                              ->where('paid_amount', '>', 0);
                        break;
                    case 'unpaid':
                        $query->where('paid_amount', 0);
                        break;
                }
            }

            // Restriction par agence pour les non-admins
            if (!Auth::user()->isAdmin()) {
                $stocks = Auth::user()->stocks->pluck('id')->toArray();
                $query->whereIn('stock_id', $stocks)
                      ->where('agency_id', Auth::user()->agency_id);
            }

            $sales = $query->paginate(10);
            $stats = $this->calculateStats();

            return sendResponse([
                'sales' => $sales,
                'stats' => $stats,
                'filters' => $request->only(['search', 'date_from', 'date_to', 'status'])
            ], 'Ventes récupérées avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération des ventes', 500, $e->getMessage());
        }
    }

    /**
     * Détails d'une vente
     */
    public function show(Sale $sale)
    {
        try {
            $sale->load(['client', 'saleItems.product', 'user']);
            $company = Company::where('is_actif', true)->first();

            return sendResponse([
                'sale' => $sale,
                'company' => $company
            ], 'Vente récupérée avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération de la vente', 500, $e->getMessage());
        }
    }

    /**
     * Données initiales pour la création de vente
     */
    public function getCreateData()
    {
        try {
            $stocks = Auth::user()->stocks()->get();

            return sendResponse([
                'stocks' => $stocks,
                'current_date' => now()->format('Y-m-d\TH:i'),
                'invoice_types' => ['FACTURE', 'PROFORMA', 'BON']
            ], 'Données récupérées avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors du chargement des données', 500, $e->getMessage());
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

            return sendResponse(['categories' => $categories], 'Catégories récupérées avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors du chargement des catégories', 500, $e->getMessage());
        }
    }

    /**
     * Recherche de clients
     */
    public function searchClients(Request $request)
    {
        try {
            $search = $request->get('search', '');

            $clients = Client::select('id', 'name', 'phone', 'email')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->limit(10)
                ->get();

            return sendResponse(['clients' => $clients], 'Clients récupérés avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la recherche de clients', 500, $e->getMessage());
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

            if (!$stockId) {
                return sendError('Stock ID requis', 400);
            }

            $products = StockProduct::with('product')
                ->where('stock_id', $stockId)
                ->where('quantity', '>', 0)
                ->when($search, function ($query, $search) {
                    $query->whereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%");
                    });
                })
                ->when($categoryId, function ($query, $categoryId) {
                    $query->whereHas('product', function ($q) use ($categoryId) {
                        $q->where('category_id', $categoryId);
                    });
                })
                ->get()
                ->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->product?->name,
                        'code' => $p->product?->code,
                        'tva' => $p->product?->tva ?? 0,
                        'description' => $p->product?->description,
                        'sale_price_ttc' => $p->product?->sale_price_ttc,
                        'unit' => $p->product?->unit ?? 'Piece',
                        'category_id' => $p->product?->category_id,
                        'quantity_disponible' => $p->quantity,
                        'stock_id' => $p->stock_id,
                        'sale_price' => $p->product?->sale_price_ttc,
                    ];
                });

            return sendResponse(['products' => $products], 'Produits récupérés avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la recherche de produits', 500, $e->getMessage());
        }
    }

    /**
     * Récupérer les détails d'un produit avec son stock
     */
    public function getProductStock(Request $request, $productId)
    {
        try {
            $stockId = $request->get('stock_id');

            if (!$stockId) {
                return sendError('Stock ID requis', 400);
            }

            $product = Product::with(['stockProducts' => function ($query) use ($stockId) {
                    $query->where('stock_id', $stockId);
                }])
                ->find($productId);

            if (!$product) {
                return sendError('Produit non trouvé', 404);
            }

            $stockProduct = $product->stockProducts->first();
            $availableStock = $stockProduct ? $stockProduct->quantity : 0;

            return sendResponse([
                'product' => $product,
                'available_stock' => $availableStock,
                'stock_id' => $stockId
            ], 'Produit récupéré avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération du produit', 500, $e->getMessage());
        }
    }

    /**
     * Créer une nouvelle vente
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'stock_id' => 'required|exists:stocks,id',
            'sale_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'invoice_type' => 'required|in:FACTURE,PROFORMA,BON',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:stock_products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ], [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'sale_date.required' => 'La date de vente est obligatoire.',
            'items.required' => 'Veuillez ajouter au moins un produit.',
            'items.min' => 'Veuillez ajouter au moins un produit.',
        ]);

        if ($validator->fails()) {
            return sendError('Données invalides', 422, $validator->errors());
        }

        DB::beginTransaction();

        try {
            // Vérifier la caisse
            $caisse = CashRegister::where('user_id', Auth::id())->first();
            if (!$caisse) {
                return sendError('Caisse introuvable', 403);
            }

            // Valider les stocks
            $stockErrors = $this->validateStock($request->items, $request->stock_id);
            if (!empty($stockErrors)) {
                return sendError('Stocks insuffisants', 400, $stockErrors);
            }

            // Créer la vente
            $result = $this->createSale($request, $caisse);

            DB::commit();

            return sendResponse($result, 'Vente enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return sendError('Erreur lors de l\'enregistrement', 500, $e->getMessage());
        }
    }

    /**
     * Annuler une vente
     */
    public function cancel($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($id);

            if ($sale->status === 'cancelled') {
                return sendError('Cette vente est déjà annulée.', 400);
            }

            // Restaurer les stocks
            foreach ($sale->saleItems as $item) {
                $stock = StockProduct::where('product_id', $item->product_id)
                    ->where('stock_id', $sale->stock_id)
                    ->first();

                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                } else {
                    return sendError('Stock not found for product ID: ' . $item->product_id, 404);
                }
            }

            $sale->status = 'cancelled';
            $sale->description = $request->input('description', 'Vente annulée');
            $sale->save();

            DB::commit();

            return sendResponse($sale, 'Vente annulée avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Erreur lors de l\'annulation', 500, $e->getMessage());
        }
    }

    /**
     * Enregistrer un paiement pour une vente
     */
    public function payment($id, Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
        ]);

        try {
            $sale = Sale::findOrFail($id);

            if ($sale->status === 'cancelled') {
                return sendError('Impossible d\'ajouter un paiement à une vente annulée.', 400);
            }

            if ($sale->due_amount <= 0) {
                return sendError('Cette vente est déjà entièrement payée.', 400);
            }

            $paymentAmount = $request->amount;

            if ($paymentAmount > $sale->due_amount) {
                return sendError('Le montant du paiement ne peut pas dépasser le montant dû (' . number_format($sale->due_amount, 0, ',', ' ') . ' F).', 400);
            }

            $sale->paid_amount += $paymentAmount;
            $sale->due_amount -= $paymentAmount;

            if ($sale->due_amount <= 0) {
                $sale->status = 'paid';
            } elseif ($sale->paid_amount > 0) {
                $sale->status = 'partial';
            }

            $sale->save();

            return sendResponse($sale, 'Paiement enregistré avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de l\'enregistrement du paiement', 500, $e->getMessage());
        }
    }

    /**
     * Télécharger le PDF d'une vente
     */
    public function downloadPDF(Sale $sale)
    {
        try {
            $sale->load(['client', 'saleItems.product']);
            $company = Company::where('is_actif', true)->first();

            $data = [
                'sale' => $sale,
                'company' => $company,
                'title' => 'Facture #' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)
            ];

            $pdf = Pdf::loadView('sale.invoice-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            $filename = 'facture_' . str_pad($sale->id, 6, '0', STR_PAD_LEFT) . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return sendError('Erreur lors de la génération du PDF', 500, $e->getMessage());
        }
    }

    /**
     * Valider les stocks disponibles
     */
    private function validateStock($items, $stockId)
    {
        $errors = [];

        foreach ($items as $item) {
            $stockProduct = StockProduct::where('id', $item['product_id'])
                ->where('stock_id', $stockId)
                ->first();

            $availableStock = $stockProduct ? $stockProduct->quantity : 0;

            if ($item['quantity'] > $availableStock) {
                $productName = $stockProduct ? $stockProduct->product->name : "Produit #{$item['product_id']}";
                $errors[] = "Stock insuffisant pour {$productName}. Disponible: {$availableStock}, Demandé: {$item['quantity']}";
            }
        }

        return $errors;
    }

    /**
     * Créer une vente
     */
    private function createSale($request, $caisse)
    {
        $dueAmount = $request->total_amount - $request->paid_amount;

        $sale = Sale::create([
            'client_id' => $request->client_id,
            'stock_id' => $request->stock_id,
            'user_id' => Auth::id(),
            'total_amount' => $request->total_amount,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $dueAmount,
            'type_facture' => 'F. NORMALE',
            'sale_date' => Carbon::parse($request->sale_date),
            'note' => $request->note,
            'agency_id' => Auth::user()->agency_id,
            'created_by' => Auth::id(),
        ]);

        foreach ($request->items as $item) {
            $quantity = floatval($item['quantity']);
            $price = floatval($item['sale_price']);
            $discount = floatval($item['discount'] ?? 0);
            $itemSubtotal = $quantity * $price;
            $itemDiscountAmount = ($itemSubtotal * $discount) / 100;
            $finalSubtotal = $itemSubtotal - $itemDiscountAmount;

            $stockProduct = StockProduct::where('id', $item['product_id'])
                ->where('stock_id', $request->stock_id)
                ->first();

            if (!$stockProduct || $stockProduct->quantity < $quantity) {
                throw new \Exception("Stock insuffisant pour le produit {$item['product_id']}");
            }

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
            $stockProduct->decrement('quantity', $quantity);

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
            'amount' => $request->paid_amount,
            'description' => $request->note ?? 'Vente Normale facture no ' . $sale->id,
            'agency_id' => $caisse->agency_id,
            'created_by' => Auth::id(),
            'user_id' => Auth::id(),
        ]);

        return [
            'type' => 'sale',
            'id' => $sale->id,
            'total_amount' => $request->total_amount,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $dueAmount
        ];
    }

    /**
     * Calculer les statistiques
     */
    private function calculateStats()
    {
        $today = now()->startOfDay();

        return [
            'totalRevenue' => Sale::sum('total_amount'),
            'paidSales' => Sale::where('due_amount', 0)->count(),
            'totalDue' => Sale::sum('due_amount'),
            'todaySales' => Sale::whereDate('sale_date', $today)->count(),
        ];
    }
}