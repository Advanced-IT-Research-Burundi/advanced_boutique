<?php

namespace App\Livewire\Sales;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class SaleCreate extends Component
{

    public $client_search = '';
    public $selected_client = null;
    public $client_id = null;
    public $filtered_clients = [];

    public $product_search = '';
    public $show_product_search = false;
    public $filtered_products = [];


    public $sale_date;
    public $note = '';
    public $paid_amount = 0;


    public $items = [];


    public $subtotal = 0;
    public $total_discount = 0;
    public $total_amount = 0;
    public $due_amount = 0;

    // Collections
    public $products;
    public $clients;
    public $current_stock;

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
        $this->loadData();
        $this->addEmptyItem();
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
            ->get()
            ->map(function ($product) {
                $product->available_stock = $this->getProductAvailableStock($product->id);
                return $product;
            });
            // dump($this->);

        // Obtenir le stock actuel (le plus récent)
        $this->current_stock = Stock::where('agency_id', Auth::user()->agency_id ?? null)
            ->latest()
            ->first();
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
            $this->filtered_products = $this->products->filter(function ($product) {
                return stripos($product->name, $this->product_search) !== false;
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

    public function addEmptyItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => '',
            'sale_price' => '',
            'discount' => 0,
            'subtotal' => 0,
            'unit' => '',
            'available_stock' => 0,
        ];
    }

    public function addProductToSale($productId)
    {
        $product = $this->products->find($productId);

        if (!$product) {
            return;
        }

        // Vérifier si le produit est déjà dans la liste
        $existingIndex = collect($this->items)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existingIndex !== false) {
            // Augmenter la quantité si le produit existe déjà
            $this->items[$existingIndex]['quantity'] = ($this->items[$existingIndex]['quantity'] ?: 0) + 1;
        } else {
            // Ajouter un nouveau produit
            $this->items[] = [
                'product_id' => $productId,
                'quantity' => 1,
                'sale_price' => $product->sale_price,
                'discount' => 0,
                'subtotal' => $product->sale_price,
                'unit' => $product->unit,
                'available_stock' => $product->available_stock,
            ];
        }

        $this->show_product_search = false;
        $this->product_search = '';
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if (empty($this->items)) {
            $this->addEmptyItem();
        }

        $this->calculateTotals();
    }

    public function updatedItems()
    {
        // Mettre à jour les informations du produit quand un produit est sélectionné
        foreach ($this->items as $index => $item) {
            if (!empty($item['product_id']) && empty($item['sale_price'])) {
                $product = $this->products->find($item['product_id']);
                if ($product) {
                    $this->items[$index]['sale_price'] = $product->sale_price;
                    $this->items[$index]['unit'] = $product->unit;
                    $this->items[$index]['available_stock'] = $product->available_stock;
                }
            }
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        $this->total_discount = 0;

        foreach ($this->items as $index => $item) {
            if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['sale_price'])) {
                $quantity = floatval($item['quantity']);
                $price = floatval($item['sale_price']);
                $discount = floatval($item['discount'] ?? 0);

                $item_subtotal = $quantity * $price;
                $item_discount = ($item_subtotal * $discount) / 100;
                $final_subtotal = $item_subtotal - $item_discount;

                $this->items[$index]['subtotal'] = $final_subtotal;
                $this->subtotal += $item_subtotal;
                $this->total_discount += $item_discount;
            }
        }

        $this->total_amount = $this->subtotal - $this->total_discount;
        $this->due_amount = $this->total_amount - floatval($this->paid_amount);
    }

    public function updatedPaidAmount()
    {
        $this->calculateTotals();
    }

    public function getProductAvailableStock($productId)
    {
        $quantity = StockProduct::where('product_id', $productId)
            ->sum('quantity');

        return $quantity ?? 0;
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
                $availableStock = $this->getProductAvailableStock($item['product_id']);

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

    public function save()
    {
        // Nettoyer les items vides
        $this->items = array_filter($this->items, function ($item) {
            return !empty($item['product_id']) && !empty($item['quantity']);
        });

        $this->validate();

        // Vérifier le stock
        if (!$this->validateStock()) {
            return;
        }

        // Vérifier qu'il y a au moins un item
        if (empty($this->items)) {
            $this->addError('items', 'Veuillez ajouter au moins un produit à la vente.');
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
                $stockProduct = StockProduct::where('stock_id', $this->current_stock->id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($stockProduct) {
                    $stockProduct->update([
                        'quantity' => $stockProduct->quantity - $item['quantity']
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Vente enregistrée avec succès!');

            return redirect()->route('sales.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'enregistrement de la vente: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sales.sale-create');
    }
}
