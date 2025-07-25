<?php

namespace App\Livewire\Sales;

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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class SaleCreate extends Component
{
    use WithPagination;

    // Client properties
    public $client_search = '';
    public $selected_client = null;
    public $client_id = null;
    public $show_client_search = false;
    public $client_search_loading = false;

    // Product search properties
    public $product_search = '';
    public $show_product_search = false;
    public $product_search_loading = false;
    public $selected_products = [];

    // Sale properties
    public $sale_date;
    public $note = '';
    public $paid_amount = 0;
    public $items = [];

    // Totals
    public $subtotal = 0;
    public $total_subtotal = 0;
    public $total_discount = 0;
    public $total_amount = 0;
    public $due_amount = 0;
    public $invoiceTye = "FACTURE";

    // Collections (optimized)
    public $current_stock;
    public $availablestocks = [];
    public $cart_session;

    // Category and pagination
    public $categories = [];
    public $selected_category_id = null;
    public $products_per_page = 200;
    public $categories_loading = false;
    public $products_loading = false;
    public $selectedStock = null;

    // Cache properties
    public $clients_cache = null;
    public $filtered_clients = [];
    public $filtered_products = [];
    public $listeCategories = [];

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'sale_date' => 'required|date',
        'paid_amount' => 'required|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.sale_price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0|max:100',
    ];

    protected $messages = [
        'client_id.required' => 'Veuillez sélectionner un client.',
        'client_id.exists' => 'Le client sélectionné n\'existe pas.',
        'sale_date.required' => 'La date de vente est obligatoire.',
        'paid_amount.required' => 'Le montant payé est obligatoire.',
        'paid_amount.min' => 'Le montant payé doit être positif.',
        'items.required' => 'Veuillez ajouter au moins un produit.',
        'items.min' => 'Veuillez ajouter au moins un produit.',
        'items.*.product_id.required' => 'Veuillez sélectionner un produit.',
        'items.*.quantity.required' => 'La quantité est obligatoire.',
        'items.*.quantity.min' => 'La quantité doit être supérieure à 0.',
        'items.*.sale_price.required' => 'Le prix de vente est obligatoire.',
        'items.*.sale_price.min' => 'Le prix de vente doit être positif.',
        'items.*.discount.max' => 'La remise ne peut pas dépasser 100%.',
    ];

    public function currentSelectStock()
    {
        $this->listeCategories = StockProduct::where('stock_id', $this->selectedStock)
        ->with('product.category')
        ->get()
        ->pluck('product.category.name', 'product.category.id');

        //dd($this->listeCategories);
    }

    public function mount()
    {
        $this->sale_date = now()->format('Y-m-d\TH:i');
        $this->cart_session = 'sale_create_' . Auth::id() . '_' . session()->getId();
        $stock = auth()->user()->stocks()->first();
        if ($stock) {
            $this->selectedStock = auth()->user()->stocks()->first()->id;

            // select current Category
            $this->currentSelectStock();
        }

        if (!session()->has('cart_sessions')) {
            session()->put('cart_sessions', []);
        }

        $this->loadCurrentStock();
        $this->loadCartItems();
    }

    /**
    * Chargement optimisé du stock actuel
    */
    public function loadCurrentStock()
    {
        $this->current_stock = Stock::where('agency_id', Auth::user()->agency_id ?? null)
        ->latest()
        ->select('id', 'agency_id', 'created_at')
        ->first();

        $this->availablestocks = Auth::user()
        ->stocks()
        ->withCount('products')
        ->get();

    }

    /**
    * Chargement lazy des catégories
    */
    public function loadCategories()
    {
        if ($this->categories_loading) return;

        $this->categories_loading = true;

        try {
            $selectedStock = $this->selectedStock;
            $this->categories = Category::select('id', 'name')
            ->withCount(['products' => function ($query) use ($selectedStock) {
                $query->whereHas('stockProducts', function ($q) use ($selectedStock) {
                    $q->where('quantity', '>', 0)
                    ->where('stock_id', $selectedStock);
                });
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();
        } finally {
            $this->categories_loading = false;
        }
    }

    /**
    * Recherche de clients optimisée
    */
    public function searchClients()
    {
        $this->show_client_search = true;
        $this->client_search_loading = true;

        try {
            if (strlen($this->client_search) >= 1) {
                $this->filtered_clients = Client::select('id', 'name', 'phone', 'email')
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->client_search . '%')
                    ->orWhere('phone', 'like', '%' . $this->client_search . '%')
                    ->orWhere('email', 'like', '%' . $this->client_search . '%');
                })
                ->orderBy('name')
                ->limit(10)
                ->get();
            } else {
                $this->filtered_clients = [];
            }
        } finally {
            $this->client_search_loading = false;
        }
    }


    /**
    * Sélection de catégorie optimisée
    */
    public function selectCategory($categoryId = null)
    {
        $this->selected_category_id = $categoryId;
        $this->product_search = '';
        $this->products_loading = true;
        $this->resetPage();

        try {
            if ($categoryId) {
                $this->loadProductsByCategory($categoryId);
            } else {
                $this->filtered_products = [];
            }
        } finally {
            $this->products_loading = false;
        }
    }



    public function loadProductList( $categoryId = null, $loadMore = false, $searchKey = null){
        $currentPage = $loadMore ? $this->getPage() + 1 : 1;
        $selectedStock = $this->selectedStock;
        $products = Product::with(['stockProducts' => function ($query) use ($selectedStock) {
                        $query->where('stock_id', $selectedStock);
                    }])
                    ->where(function ($query) use ($searchKey){
                        if($searchKey){
                            $query->where('name', 'like', '%' . $searchKey . '%')
                            ->orWhere('code', 'like', '%' . $searchKey . '%')
                            ->orWhere('description', 'like', '%' . $searchKey . '%');
                        }
                    })
                    ->where(function ($query) use ($categoryId){
                        if($categoryId){
                            $query->where('category_id', $categoryId);
                        }
                    })
                    ->whereHas('stockProducts', function ($query) use ($selectedStock) {
                        $query->where('quantity', '>', 0)
                        ->where('stock_id', $selectedStock);
                    })
                    ->whereNotIn('id', $this->selected_products)
                    ->orderBy('name')
                    ->paginate($this->products_per_page, ['*'], 'page', $currentPage);

       // dd($selectedStock,$products->getCollection()->toArray());// Ajouter les informations de stock

        $products->getCollection()->each(function ($product) use ($selectedStock) {
            $product->quantity_disponible =  $product->stockProducts->first()->quantity ?? 0;
            $product->stock_id = $selectedStock;
            $product->product_id = $product->id;
        });

        //dd($selectedStock, $products->getCollection());

        if ($loadMore && $currentPage > 1) {
            $this->filtered_products =   array_merge($this->filtered_products, $products->getCollection()->toArray());
        } else {
            $this->filtered_products =  $products->getCollection()->toArray();
        }

        return $products->getCollection();

    }

    /**
    * Chargement des produits par catégorie avec pagination
    */



    public function loadProductsByCategory($categoryId, $loadMore = false)
    {
        $this->loadProductList($categoryId, $loadMore);
    }

    /**
    * Charger plus de produits (pagination infinie)
    */
    public function loadMoreProducts()
    {
        if ($this->selected_category_id) {
            $this->loadProductsByCategory($this->selected_category_id, true);
        }
    }

    /**
    * Recherche de produits optimisée
    */
    public function searchProducts()
    {
        $this->product_search_loading = true;
        try {
            if (strlen($this->product_search) >= 1) {
              $this->loadProductList(null, false, $this->product_search);
            }
        } finally {
            $this->product_search_loading = false;
        }
    }

    /**
    * Mise à jour de la recherche produits
    */
    public function updatedProductSearch()
    {
        if (strlen($this->product_search) >= 1) {
            $this->selected_category_id = null;
            $this->searchProducts();
        } else {
            $this->filtered_products = [];
        }
    }

    /**
    * Chargement des items du panier optimisé
    */
    public function loadCartItems()
    {
        try {
            $cartContent = Cart::session($this->cart_session)->getContent();
            $this->items = [];
            $this->selected_products = [];

            if ($cartContent->count() > 0) {
                $productIds = $cartContent->pluck('id')->toArray();

                // Charger tous les produits en une seule requête
                $products = Product::whereIn('id', $productIds)
                ->select('id','code', 'name', 'unit')
                ->get()
                ->keyBy('id');

                // Charger les stocks en une seule requête
                $stockProducts = StockProduct::whereIn('product_id', $productIds)
                ->where('stock_id', $this->selectedStock)
                ->select('product_id', 'quantity')
                ->get()
                ->keyBy('product_id');

                foreach ($cartContent->sortBy('id') as $cartItem) {
                    $product = $products->get($cartItem->id);
                    $stockProduct = $stockProducts->get($cartItem->id);
                    // dd($cartItem);
                    if ($product) {
                        $discount = floatval($cartItem->attributes->get('discount', 0));
                        $code = $cartItem->attributes->get('code') ?? '';
                        $quantity = floatval($cartItem->quantity);
                        $price = floatval($cartItem->price);
                        $availableStock = $stockProduct ? $stockProduct->quantity : 0;

                        $subtotal_before_discount = $quantity * $price;
                        $discount_amount = ($subtotal_before_discount * $discount) / 100;
                        $subtotal_after_discount = $subtotal_before_discount - $discount_amount;

                        $this->items[] = [
                            'product_id' => $cartItem->id,
                            'quantity' => $quantity,
                            'name' => $product->name,
                            'code' => $code,
                            'sale_price' => $price,
                            'discount' => $discount,
                            'subtotal' => $subtotal_after_discount,
                            'unit' => $product->unit,
                            'available_stock' => $availableStock,
                        ];

                        $this->selected_products[] = $cartItem->id;
                    }
                }
            }

            $this->calculateTotals();
        } catch (\Exception $e) {
            $this->items = [];
            $this->selected_products = [];
        }
    }

    /**
    * Sélection de client
    */
    public function selectClient($clientId)
    {
        $client = Client::find($clientId);
        if ($client) {
            $this->selected_client = $client;
            $this->client_id = $clientId;
            $this->client_search = $client->name;
            $this->filtered_clients = [];
            $this->show_client_search = false;
        }
    }

    /**
    * Effacer la sélection client
    */
    public function clearClient()
    {
        $this->selected_client = null;
        $this->client_id = null;
        $this->client_search = '';
        $this->filtered_clients = [];
        $this->show_client_search = false;
    }

    /**
    * Ajouter un produit au panier (optimisé)
    */
    public function addProductToSale($productId)
    {

        try {
            // Vérifier le stock disponible
            $stockProduct = StockProduct::where('product_id', $productId)
                ->where('stock_id', $this->selectedStock)
                ->first();

            if (!$stockProduct || $stockProduct->quantity <= 0) {
                $this->dispatch('error', ['message' => 'Stock insuffisant pour ce produit']);
                return;
            }

            $existingItem = Cart::session($this->cart_session)->get($productId);

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + 1;
                if ($newQuantity > $stockProduct->quantity) {
                    $this->dispatch('error', ['message' => 'Stock insuffisant pour cette quantité']);
                    return;
                }

                Cart::session($this->cart_session)->update($productId, [
                    'quantity' => ['relative' => false, 'value' => $newQuantity]
                ]);
            } else {
                $product = Product::select('id', 'code','name', 'sale_price_ttc', 'unit', 'image')
                ->find($productId);

                if (!$product) return;

                Cart::session($this->cart_session)->add([
                    'id' => $productId,
                    'name' => $product->name,
                    'code' => $product->code,
                    'price' => $product->sale_price_ttc ?? 0,
                    'quantity' => 1,
                    'attributes' => [
                        'unit' => $product->unit,
                        'available_stock' => $stockProduct->quantity,
                        'code' => $product->code,
                        'discount' => 0,
                        'image' => $product->image,
                        ]
                    ]);
                }

                $this->loadCartItems();

                // Supprimer le produit de la liste affichée
                $this->filtered_products = array_filter($this->filtered_products, function ($product) use ($productId) {
                    return $product['id'] != $productId;
                });

                // Réindexer le tableau
                $this->filtered_products = array_values($this->filtered_products);

                $this->dispatch('productAdded', ['message' => 'Produit ajouté avec succès!']);

            } catch (\Exception $e) {

                $this->dispatch('error', ['message' => 'Erreur lors de l\'ajout du produit: ' . $e->getMessage()]);
            }
        }

        /**
        * Supprimer un item du panier
        */
        public function removeItem($productId)
        {
            try {
                Cart::session($this->cart_session)->remove($productId);
                $this->loadCartItems();

                // Recharger les produits si une catégorie est sélectionnée
                if ($this->selected_category_id) {
                    $this->loadProductsByCategory($this->selected_category_id);
                }

                $this->dispatch('productRemoved', ['message' => 'Produit retiré du panier']);
            } catch (\Exception $e) {
                // Gestion silencieuse
            }
        }

        /**
        * Mettre à jour la quantité d'un item
        */
        public function updateItemQuantity($productId, $quantityD)
        {
            $quantity = doubleval($quantityD);
            if ($quantity <= 0 && $quantityD != "" ) {

                $this->removeItem($productId);
                return;
            }


            // Vérifier le stock disponible
            $stockProduct = StockProduct::where('product_id', $productId)
            ->where('stock_id', $this->selectedStock)
            ->first();

            if ($stockProduct && $quantity > $stockProduct->quantity) {
              //  dd($quantity, $stockProduct);
                $this->dispatch('error', ['message' => 'Quantité supérieure au stock disponible']);
                return;
            }



            try {
                Cart::session($this->cart_session)->update($productId, [
                    'quantity' => ['relative' => false, 'value' => $quantity]
                ]);
                $this->loadCartItems();
            } catch (\Exception $e) {
                // Gestion silencieuse
                dd($e);
                $this->dispatch('error', ['message' => 'Erreur lors de la mise à jour de la quantité: ' . $e->getMessage()]);
            }
        }

        /**
        * Mettre à jour le prix d'un item
        */
        public function updateItemPrice($productId, $price)
        {
            $price = floatval($price);
            if ($price < 0) return;

            try {
                Cart::session($this->cart_session)->update($productId, ['price' => $price]);
                $this->loadCartItems();
            } catch (\Exception $e) {
                // Gestion silencieuse
            }
        }

        /**
        * Mettre à jour la remise d'un item
        */
        public function updateItemDiscount($productId, $discount)
        {
            $discount = max(0, min(100, floatval($discount ?? 0)));

            try {
                $cartItem = Cart::session($this->cart_session)->get($productId);
                if ($cartItem) {
                    $attributes = $cartItem->attributes->toArray();
                    $attributes['discount'] = $discount;

                    Cart::session($this->cart_session)->update($productId, [
                        'attributes' => $attributes
                    ]);
                    $this->loadCartItems();
                }
            } catch (\Exception $e) {
                // Gestion silencieuse
            }
        }

        /**
        * Calculer les totaux
        */
        public function calculateTotals()
        {
            $this->subtotal = 0;
            $this->total_discount = 0;
            $this->total_amount = 0;



            foreach ($this->items as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity']) && isset($item['sale_price'])) {
                    $quantity = floatval($item['quantity']);
                    $price = floatval($item['sale_price']);
                    $discount = floatval($item['discount'] ?? 0);

                    $item_subtotal = $quantity * $price;
                    $item_discount_amount = ($item_subtotal * $discount) / 100;

                    $this->subtotal += $item_subtotal;
                    $this->total_discount += $item_discount_amount;
                }
            }

            $this->total_amount = $this->subtotal - $this->total_discount;
            $this->total_subtotal = $this->subtotal;
            $this->due_amount = $this->total_amount - floatval($this->paid_amount);
        }

        /**
        * Mise à jour du montant payé
        */
        public function updatedPaidAmount()
        {
            $this->calculateTotals();
        }

        /**
        * Statut du paiement
        */
        public function getPaymentStatusProperty()
        {
            if ($this->due_amount < 0) {
                return [
                    'type' => 'info',
                    'message' => 'Monnaie à rendre : ' . number_format(abs($this->due_amount), 0, ',', ' ') . ' Fbu'
                ];
            } elseif ($this->due_amount == 0) {
                return [
                    'type' => 'success',
                    'message' => 'Paiement complet'
                ];
            } else {
                return [
                    'type' => 'warning',
                    'message' => 'Paiement partiel - Reste : ' . number_format($this->due_amount, 0, ',', ' ') . ' Fbu'
                ];
            }
        }

        /**
        * Valider le stock
        */
        protected function validateStock()
        {
            $errors = [];
            $productIds = collect($this->items)->pluck('product_id')->toArray();

            // Charger tous les stocks en une seule requête
            $stockProducts = StockProduct::whereIn('product_id', $productIds)
            ->where('stock_id', $this->selectedStock)
            ->select('product_id', 'quantity')
            ->get()
            ->keyBy('product_id');

            foreach ($this->items as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity'])) {
                    $stockProduct = $stockProducts->get($item['product_id']);
                    $availableStock = $stockProduct ? $stockProduct->quantity : 0;

                    if ($item['quantity'] > $availableStock) {
                        $product = Product::find($item['product_id']);
                        $errors[] = "Stock insuffisant pour {$product->name}. Stock disponible: {$availableStock}, Demandé: {$item['quantity']}";
                    }
                }
            }

            if (!empty($errors)) {
                $this->addError('stock_error', $errors);
                return false;
            }

            return true;
        }

        /**
        * Vider le panier
        */
        public function clearCart()
        {
            try {
                Cart::session($this->cart_session)->clear();
                $this->loadCartItems();

                // Recharger les produits si nécessaire
                if ($this->selected_category_id) {
                    $this->loadProductsByCategory($this->selected_category_id);
                }

                $this->dispatch('cartCleared', ['message' => 'Panier vidé avec succès']);
            } catch (\Exception $e) {
                // Gestion silencieuse
            }
        }

        /**
        * Valider la vente
        */
        public function validateSale()
        {
            $this->calculateTotals();
        }

        /**
        * Sauvegarder la vente
        */
        public function save()
        {


            $this->loadCartItems();

            if (empty($this->items)) {
                $this->addError('items', 'Veuillez ajouter au moins un produit à la vente.');
                $this->dispatch('error', [
                    'message' => 'Veuillez ajouter au moins un produit à la vente.'
                ]);
                return;
            }

            $this->validate();

            // Vérifier le stock avant de procéder
            if (!$this->validateStock()) {
                $this->addError('stock_validation', 'Impossible de procéder à la vente. Certains produits ont une quantité supérieure au stock disponible.');
                $this->dispatch('error', [
                    'message' => 'Impossible de procéder à la vente. Certains produits ont une quantité supérieure au stock disponible.'
                ]);
                return;
            }

            // Vérification supplémentaire des quantités en temps réel
            $hasStockErrors = false;
            foreach ($this->items as $item) {
                $quantity = floatval($item['quantity']);
                $availableStock = floatval($item['available_stock'] ?? 0);

                if ($quantity > $availableStock) {
                    $hasStockErrors = true;
                    break;
                }
            }

            if ($hasStockErrors) {
                $this->addError('stock_validation', 'Impossible de procéder à la vente. Certains produits ont une quantité supérieure au stock disponible.');
                $this->dispatch('error', [
                    'message' => 'Veuillez corriger les quantités avant de sauvegarder la vente.'
                ]);
                return;
            }

            $caisse = CashRegister::where('user_id', auth()->user()->id)->first();
            if (!$caisse) {
                $this->addError('error', "Vous n'avez pas le droit de créer une facture. Caisse introuvable.");
                $this->dispatch('error', [
                    'message' => "Vous n'avez pas le droit de créer une facture. Caisse introuvable."
                ]);
                return;
            }

            try {
                DB::beginTransaction();
                //   dd($this->invoiceTye);
                if($this->invoiceTye == "PROFORMA"){
                    // create Proforma
                    $this->createProforma(  $caisse );
                }else{
                    // create Sale
                    $this->createSale(  $caisse );
                }


                DB::commit();

                Cart::session($this->cart_session)->clear();
                session()->flash('success', 'Vente enregistrée avec succès!');

                return redirect()->route('sales.index');

            } catch (\Exception $e) {
                DB::rollBack();
                $this->addError('error', 'Erreur lors de l\'enregistrement de la vente: ' . $e->getMessage());
            }
        }


        public function createProforma($caisse){

             Proforma::create([
                'client_id' => $this->client_id,
                'stock_id' => $this->selectedStock ?? null,
                'user_id' => Auth::id(),
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'due_amount' => $this->due_amount,
                'sale_date' => Carbon::parse($this->sale_date),
                'note' => $this->note,
                'invoice_type' => $this->invoiceTye,
                'agency_id' => Auth::user()->agency_id,
                'created_by' => Auth::id(),
                'proforma_items' => json_encode($this->items),
                'client' => json_encode(Client::find($this->client_id)),
            ]);
            $this->dispatch('success', [
                'message' => 'Proforma enregistrée avec succès!'
            ]);
        }
        /**
        * Définir le montant exact
        */
        public function setExactAmount()
        {
            $this->paid_amount = $this->total_amount;
            $this->calculateTotals();
        }

        public function createSale($caisse){

            $sale = Sale::create([
                'client_id' => $this->client_id,
                'stock_id' => $this->selectedStock ?? null,
                'user_id' => Auth::id(),
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'due_amount' => $this->due_amount,
                'type_facture' =>'F. NORMALE',
                'sale_date' => Carbon::parse($this->sale_date),
                'note' => $this->note,
                'agency_id' => Auth::user()->agency_id,
                'created_by' => Auth::id(),
            ]);

            foreach ($this->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'sale_price' => $item['sale_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $item['subtotal'],
                    'agency_id' => Auth::user()->agency_id,
                    'created_by' => Auth::id(),
                    'user_id' => Auth::id(),
                ]);

                $stockProduct = StockProduct::where('product_id', $item['product_id'])
                ->where('stock_id', $this->selectedStock)
                ->first();
                if(!$stockProduct){
                    $this->addError('stock_error', 'Stock insuffisant pour ' . $item['product_id']);
                    $this->dispatch('error', [
                        'message' => 'Stock insuffisant pour ' . $item['product_id']
                    ]);
                    return;
                }
                if ($stockProduct) {
                    $stockProduct->update([
                        'quantity' => $stockProduct->quantity - $item['quantity']
                    ]);

                    // Update Product Stock Movement
                  $s =  StockProductMouvement::create([
                        'agency_id' => auth()->user()->agency_id,
                        'stock_id' => $stockProduct->stock_id,
                        'stock_product_id' => $stockProduct->id,
                        'item_code' => $stockProduct->id,
                        'item_designation' => $stockProduct->product->name,
                        'item_quantity' => $item['quantity'],
                        'item_measurement_unit' => $stockProduct->product->unit ?? 'Piece',
                        'item_purchase_or_sale_price' => $item['sale_price'],
                        'item_purchase_or_sale_currency' => $stockProduct->product->sale_price_currency ?? 'BIF',
                        'item_movement_type' => 'SN',
                        'item_movement_invoice_ref' => $sale->id,
                        'item_movement_description' => 'Vente',
                        'item_movement_date' => now(),
                        'item_product_detail_id' => $stockProduct->product->id,
                        'is_send_to_obr' => null,
                        'is_sent_at' => null,
                        'user_id' => auth()->user()->id,
                        'item_movement_note' => 'Vente Normal',
                    ]);

                    //dd($s);

                }
            }

            CashTransaction::create([
                'cash_register_id' => $caisse->id,
                'type' => 'in',
                'reference_id' => 'Ref ' . $sale->id,
                'amount' => $this->total_amount,
                'description' => $this->note,
                'agency_id' => $caisse->agency_id,
                'created_by' => auth()->user()->id,
                'user_id' => auth()->user()->id,
            ]);

        }

        /**
        * Définir un montant rapide
        */
        public function setQuickAmount($amount)
        {
            $this->paid_amount = floatval($amount);
            $this->calculateTotals();
        }

        /**
        * Afficher tous les produits
        */
        public function showAllProducts()
        {
            $this->selected_category_id = null;
            $this->product_search = '';
            $this->filtered_products = [];
            $this->resetPage();
        }

        /**
        * Toggles
        */
        public function toggleProductSearch()
        {
            $this->show_product_search = !$this->show_product_search;

            if ($this->show_product_search && empty($this->categories)) {
                $this->loadCategories();
            }
        }

        public function toggleClientSearch()
        {
            $this->show_client_search = !$this->show_client_search;
        }

        /**
        * Render
        */
        public function render()
        {
            return view('livewire.sales.sale-create');
        }
    }
