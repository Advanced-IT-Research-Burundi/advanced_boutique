<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProformaStoreRequest;
use App\Http\Requests\ProformaUpdateRequest;
use App\Models\Company;
use App\Models\Proforma;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Client;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;
use App\Models\StockProduct;
use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\StockProductMouvement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProformaController extends Controller
{
    public function index(Request $request)
    {
        $query = Proforma::with(['stock', 'user', 'agency', 'createdBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
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
                          ->whereRaw('due_amount < total_amount');
                    break;
                case 'unpaid':
                    $query->whereRaw('due_amount = total_amount');
                    break;
            }
        }

        $proformas = $query->paginate(15);

        $totalRevenue = Proforma::sum('total_amount');
        $paidProformas = Proforma::where('due_amount', 0)->count();
        $totalDue = Proforma::sum('due_amount');
        $todayProformas = Proforma::whereDate('created_at', today())->count();


         $stats = [
            'totalRevenue' => $totalRevenue,
            'paidProformas'=> $paidProformas,
            'totalDue' => $totalDue,
            'todayProformas' => $todayProformas
         ];

        $data = [
            'proformas' =>$proformas,
            'stats'=> $stats
        ];

        return  sendResponse($data,'Proformas retrieved successfully');
    }

    public function show(Proforma $proforma)
    {
        $proforma->load(['stock', 'user', 'agency', 'createdBy']);

        // Decode proforma items
        $items = json_decode($proforma->proforma_items, true) ?? [];

        // Decode client data
        $client = json_decode($proforma->client, true) ?? [];

        return sendResponse([
            'proforma' => $proforma,
            'items' => $items,
            'client' => $client,
            'company' =>   Company::where('is_actif', true)->first()
        ], 'Proforma retrieved successfully', 200);
    }



    public function destroy(Request $request, Proforma $proforma)
    {
        try{
            $proforma->delete();
            return sendResponse(null, 'Proforma deleted successfully', 200);

        } catch (\Throwable $e) {
            return sendError('Erreur lors de la suppression', 500, $e->getMessage());
        }

    }

    public function valide(Proforma $proforma, $caisse){

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

                // Mettre à jour le stock
            $stockProduct = StockProduct::where('product_id', $item['product_id'])
                ->where('stock_id', $proforma->stock_id)
                ->first();

            if (!$stockProduct || $stockProduct->quantity < $item['quantity']) {
                throw new \Exception("Stock insuffisant pour le produit code :  {$stockProduct->product->code} - {$stockProduct->product->name}");
            }

            $stockProduct->update([
                'quantity' => $stockProduct->quantity - $item['quantity']
            ]);

            // Créer le mouvement de stock
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




            // Créer la transaction de caisse
            CashTransaction::create([
                'cash_register_id' => $caisse->id,
                'type' => 'in',
                'reference_id' => 'Ref ' . $sale->id,
                'amount' => $proforma->total_amount,
                'description' => $request->note ?? 'Vente Normale facture no '.$sale->id,
                'agency_id' => $caisse->agency_id,
                'created_by' => Auth::id(),
                'user_id' => Auth::id(),
            ]);


    }

    public function validateProforma(Proforma $proforma)
    {
         try {
            \DB::beginTransaction();

             // Vérifier la caisse
            $caisse = CashRegister::where('user_id', Auth::id())->first();
            if (!$caisse) {

                return sendError('Caisse introuvable', 403, ['error' => 'Vous n\'avez pas le droit de créer une facture.']);
            }


            // Valider le proforma
            $this->valide($proforma, $caisse);

            \DB::commit();

            return sendResponse($sale, 'Proforma validée et convertie en vente avec succès.', 200);

        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la validation du proforma: ' . $e->getMessage());

            return sendError('Erreur lors de la validation du proforma: ' . $e->getMessage(), 500, ['error' => $e->getMessage()]);
        }
    }

    public function validateBulkProformas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proforma_ids' => 'required|array|min:1',
            'proforma_ids.*' => 'exists:proformas,id',
        ]);

        if ($validator->fails()) {
            return sendError('Données invalides', 422, $validator->errors());
        }

        try {
            \DB::beginTransaction();

            $proformas = Proforma::whereIn('id', $request->proforma_ids)->get();
            $caisse = CashRegister::where('user_id', Auth::id())->first();
            if (!$caisse) {
                return sendError('Caisse introuvable', 403, ['error' => 'Vous n\'avez pas le droit de créer une facture.']);
            }

            foreach ($proformas as $proforma) {
                $this->valide($proforma, $caisse);
            }

            \DB::commit();
            return sendResponse(null, 'Proformas validées avec succès.', 200);

        } catch (\Throwable $e) {
            \DB::rollBack();
            return sendError('Erreur lors de la validation des proformas: ' . $e->getMessage(), 500);
        }
    }


    /// Les methodes pour sur le vues de vente avec proforma
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
                        // $q->where('quantity', '>', 0)
                          $q->where('stock_id', $stockId);
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
                     $query->where('quantity', '>', 0)
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

            // Calculer les totaux
            $totals = $this->calculateTotals($request->items);

            if ($request->invoice_type === 'PROFORMA') {
                $result = $this->createProforma($request, $totals);
            } else {
                return sendError('Type de facture non supporté ', 400, ['error' => 'Type de facture non supporté']);
            }

            DB::commit();


            $data = $result;
            $message = 'Proforma enregistrée avec succès';
            return sendResponse($data, $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return sendError('Erreur lors de l\'enregistrement', 500, ['error' => $e->getMessage()]);
        }
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
    private function createProforma($request, $totals)
    {
        $dueAmount = $totals['total_amount'] - $request->paid_amount;

        $items = array_map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'sale_price' => $item['sale_price'],
                'discount' => $item['discount'] ?? 0,
                'subtotal' => $item['quantity'] * $item['sale_price'],
            ];
        }, $request->items);

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
            'proforma_items' => json_encode($items),
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
