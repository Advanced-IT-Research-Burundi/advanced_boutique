<?php

namespace App\Livewire\Sales;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\CashRegister;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class SaleCreate extends Component
{
    public $client_search = '';
    public $selected_client = null;
    public $client_id = null;
    public $filtered_clients = [];

    public $product_search = '';
    public $show_product_search = false;
    public $filtered_products = [];
    public $selected_products = []; // Produits actuellement dans le panier

    public $sale_date;
    public $note = '';
    public $paid_amount = 0;

    public $items = [];

    public $subtotal = 0;
    public $total_subtotal = 0;
    public $total_discount = 0;
    public $total_amount = 0;
    public $due_amount = 0;

    // Collections
    public $products;
    public $clients;
    public $current_stock;

    // Cart session identifier
    public $cart_session;

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

    public function mount()
    {
        $this->sale_date = now()->format('Y-m-d\TH:i');
        $this->cart_session = 'sale_' . Auth::id() . '_' . time();
        $this->loadData();
        $this->loadCartItems();
    }

    public function loadData()
    {
        // Charger les clients
        $this->clients = Client::select('id', 'name', 'phone', 'email')
            ->orderBy('name')
            ->get();

        // Charger les produits avec leur stock disponible
        $this->products = Product::with(['stockProducts.stock'])
            ->select('id', 'name', 'sale_price', 'unit', 'alert_quantity', 'image', 'agency_id')
            ->orderBy('name')
            ->get();

        // Obtenir le stock actuel (le plus récent)
        $this->current_stock = Stock::where('agency_id', Auth::user()->agency_id ?? null)
            ->latest()
            ->first();
    }

    public function loadCartItems()
    {
        $cartItems = Cart::session($this->cart_session)->getContent();
        $this->items = [];
        $this->selected_products = [];

        $sortedCartItems = $cartItems->sortBy('id');

        foreach ($sortedCartItems as $cartItem) {
            $product = $this->products->find($cartItem->id);
            if ($product) {
                $discount = floatval($cartItem->attributes->discount ?? 0);
                $quantity = floatval($cartItem->quantity);
                $price = floatval($cartItem->price);

                // Calculer le sous-total avec remise
                $subtotal_before_discount = $quantity * $price;
                $discount_amount = ($subtotal_before_discount * $discount) / 100;
                $subtotal_after_discount = $subtotal_before_discount - $discount_amount;

                $this->items[] = [
                    'product_id' => $cartItem->id,
                    'quantity' => $quantity,
                    'sale_price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal_after_discount,
                    'unit' => $product->unit,
                    'available_stock' => $product->available_stock,
                ];
                $this->selected_products[] = $cartItem->id;
            }
        }

        $this->calculateTotals();
    }

    public function updatedClientSearch()
    {
        if (strlen($this->client_search) >= 2) {
            $this->filtered_clients = $this->clients->filter(function ($client) {
                return stripos($client->name, $this->client_search) !== false ||
                       stripos($client->phone, $this->client_search) !== false;
            })->take(10)->values();
        } else {
            $this->filtered_clients = [];
        }
    }

    public function updatedProductSearch()
    {
        if (strlen($this->product_search) >= 2) {
            // Filtrer les produits en excluant ceux déjà sélectionnés
            $this->filtered_products = $this->products->filter(function ($product) {
                $nameMatch = stripos($product->name, $this->product_search) !== false;
                $notSelected = !in_array($product->id, $this->selected_products);
                return $nameMatch && $notSelected;
            })->take(12)->values();
        } else {
            $this->filtered_products = [];
        }
    }

    public function selectClient($clientId)
    {
        $this->selected_client = $this->clients->find($clientId);
        $this->client_id = $clientId;
        $this->client_search = $this->selected_client->name;
        $this->filtered_clients = [];
    }

    public function clearClient()
    {
        $this->selected_client = null;
        $this->client_id = null;
        $this->client_search = '';
        $this->filtered_clients = [];
    }

    public function addProductToSale($productId)
    {
        $product = $this->products->find($productId);

        if (!$product) {
            return;
        }

        // Vérifier si le produit est déjà dans le panier
        if (Cart::session($this->cart_session)->get($productId)) {
            // Augmenter la quantité
            Cart::session($this->cart_session)->update($productId, [
                'quantity' => 1, // Cette valeur sera ajoutée à la quantité existante
            ]);
        } else {
            // Ajouter un nouveau produit au panier
            Cart::session($this->cart_session)->add([
                'id' => $productId,
                'name' => $product->name,
                'price' => $product->sale_price,
                'quantity' => 1,
                'attributes' => [
                    'unit' => $product->unit,
                    'available_stock' => $product->available_stock,
                    'discount' => 0,
                    'image' => $product->image,
                ]
            ]);
        }

        // $this->show_product_search = false;
        $this->product_search = '';
        $this->loadCartItems();
        $this->dispatch('productAdded', ['message' => "Produit '{$product->name}' ajouté avec succès!"]);
    }

    public function removeItem($productId)
    {
        Cart::session($this->cart_session)->remove($productId);
        $this->loadCartItems();
        $this->dispatch('productRemoved', ['message' => 'Produit retiré du panier']);
    }

    public function updateItemQuantity($productId, $quantity)
    {
        if ($quantity > 0) {
            Cart::session($this->cart_session)->update($productId, [
                'quantity' => [
                    'relative' => false,
                    'value' => $quantity
                ]
            ]);

            $this->loadCartItems();

            \Log::info("Quantity updated for product {$productId}: {$quantity}");
        }
    }

    public function updateItemPrice($productId, $price)
    {
        if ($price >= 0) {
            Cart::session($this->cart_session)->update($productId, [
                'price' => $price
            ]);
            $this->loadCartItems();

            \Log::info("Price updated for product {$productId}: {$price}");
        }
    }

    public function updateItemDiscount($productId, $discount)
    {
        $cartItem = Cart::session($this->cart_session)->get($productId);

        if ($cartItem) {
            $attributes = $cartItem->attributes->toArray();
            $attributes['discount'] = max(0, min(100, floatval($discount ?? 0)));

            Cart::session($this->cart_session)->update($productId, [
                'attributes' => $attributes
            ]);

            $this->loadCartItems();

            \Log::info("Discount updated for product {$productId}: {$discount}");
        }
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;           // Total AVANT remises
        $this->total_discount = 0;     // Total des remises
        $this->total_amount = 0;       // Total APRÈS remises (montant final)

        foreach ($this->items as $item) {
            if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['sale_price'])) {
                $quantity = floatval($item['quantity']);
                $price = floatval($item['sale_price']);
                $discount = floatval($item['discount'] ?? 0);

                // Calcul pour cet item
                $item_subtotal = $quantity * $price;                    // Sous-total de l'item
                $item_discount_amount = ($item_subtotal * $discount) / 100;  // Montant de remise de l'item

                // Accumulation des totaux
                $this->subtotal +=  $item_subtotal;                     // Additionner les sous-totaux
                $this->total_discount += $item_discount_amount;        // Additionner les remises
            }
        }

        // Le total final = sous-total - remises totales
        $this->total_amount = $this->subtotal - $this->total_discount;

        $this->total_subtotal = $this->subtotal;
        // dump($this->subtotal);
        // Montant restant à payer
        $this->due_amount = $this->total_amount - floatval($this->paid_amount);
    }

    public function updatedPaidAmount()
    {
        $this->calculateTotals();
    }


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

    protected function validateStock()
    {
        $errors = [];

        foreach ($this->items as $index => $item) {
            if (!empty($item['product_id']) && !empty($item['quantity'])) {
                $product = $this->products->find($item['product_id']);
                $availableStock = $product->available_stock ?? 0;

                if ($item['quantity'] > $availableStock) {
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


    public function clearCart()
    {
        Cart::session($this->cart_session)->clear();
        $this->loadCartItems();
        $this->dispatch('cartCleared', ['message' => 'Panier vidé avec succès']);
    }

    public function save()
    {
        // Synchroniser les items du panier
        $this->loadCartItems();

        if (empty($this->items)) {
            $this->addError('items', 'Veuillez ajouter au moins un produit à la vente.');
            return;
        }

        $this->validate();

        // Vérifier le stock
        if (!$this->validateStock()) {
            return;
        }


        $caisse = CashRegister::where('user_id', auth()->user()->id)->first();
        //  dd(auth()->user()->id);
        if(!$caisse){
            dd('Caisse introuvable pour l\'utilisateur actuel');
                 $this->addError('error', "Veuillez Nous excuse vous n'avez droit de créer une facture");
           return;
        }

        try {
            DB::beginTransaction();

            // Créer la vente
            $sale = Sale::create([
                'client_id' => $this->client_id,
                'stock_id' => $this->current_stock->id ?? null,
                'user_id' => Auth::id(),
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'due_amount' => $this->due_amount,
                'sale_date' => Carbon::parse($this->sale_date),
                'note' => $this->note,
                'agency_id' => Auth::user()->agency_id,
                'created_by' => Auth::id(),
            ]);

            // Créer les items de vente et mettre à jour le stock
            foreach ($this->items as $item) {
                // Créer l'item de vente
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'sale_price' => $item['sale_price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $item['subtotal'],
                    'agency_id' => Auth::user()->agency_id,
                    'created_by' => Auth::id(),
                    'user_id' => Auth::id(),
                ]);

                // Mettre à jour le stock
                $stockProduct = StockProduct::where('product_id', $item['product_id'])
                    ->first();

                if ($stockProduct) {
                    $stockProduct->update([
                        'quantity' => $stockProduct->quantity - $item['quantity']
                    ]);
                }
            }
             // Enregistre montant sur la caisse de l'utilisateur


            CashTransaction::create([
                'cash_register_id' =>$caisse->id,
                'type' =>'in',
                'reference_id' =>'Ref '.$sale->id,
                'amount' => $this->total_amount,
                'description' => $this->note,
                'agency_id' => $caisse->agency_id,
                'created_by' =>auth()->user()->id,
                'user_id'=>auth()->user()->id,
            ]);

            DB::commit();

            // Vider le panier après une vente réussie
            Cart::session($this->cart_session)->clear();

            session()->flash('success', 'Vente enregistrée avec succès!');
            return redirect()->route('sales.index');

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            $this->addError('error', 'Erreur lors de l\'enregistrement de la vente: ' . $e->getMessage());
        }
    }

    public function setExactAmount()
    {
        // dd($this->total_amount);
        $this->paid_amount = $this->total_amount;
        $this->calculateTotals();
    }

    public function setQuickAmount($amount)
    {
        // dd('Quick amount set');

        $this->paid_amount = floatval($amount);
        $this->calculateTotals();
    }
    public function render()
    {
        return view('livewire.sales.sale-create');
    }
}
