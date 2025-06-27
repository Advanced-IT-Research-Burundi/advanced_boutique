<?php

namespace App\Livewire\Stock;

use App\Models\StockProduct;
use App\Models\StockProductMouvement;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock;

class EntreMultiple extends Component
{
    public $stockID;
    public $quantities = [];
    public $prices = [];
    public $stock;
    public $search = '';
    public $selectedCategory = '';
    public $availableCategories = [];

    public function mount($stock)
    {
        $this->stockID = $stock;
        $this->stock = Stock::findOrFail($stock);
        $this->loadCategories();
        // Initialiser le tableau des quantités
        $this->initializeQuantities();
    }

    public function loadCategories()
    {
        $this->availableCategories = StockProduct::with('product.category')
            ->where('stock_id', $this->stockID)
            ->get()
            ->pluck('product.category')
            ->unique('id')
            ->filter()
            ->pluck('name', 'id')
            ->prepend('Toutes les catégories', '');
    }

    public function initializeQuantities()
    {
        $stockProducts = StockProduct::
        with(['product', 'product.category'])
        ->where('stock_id', $this->stockID)
        ->orderBy('product_name')
        ->get();

        foreach ($stockProducts as $product) {
            $this->quantities[$product->id] = 0;
            $this->prices[$product->id] = $product->product->sale_price;
        }
      $this->products = [];
      $this->resetForm();

    }

    public function clearQuantity($productId)
    {
        $this->quantities[$productId] = 0;
    }

    public function entreMultiple()
    {
        // Validation des données
        $this->validate([
            'quantities.*' => 'nullable|numeric|min:0',
        ], [
            'quantities.*.numeric' => 'La quantité doit être un nombre.',
            'quantities.*.min' => 'La quantité doit être positive.',
        ]);

        try {
            DB::beginTransaction();

            $entriesCount = 0;

            foreach ($this->quantities as $productId => $quantity) {
                // Ignorer les quantités nulles ou égales à 0
                if (!$quantity || $quantity <= 0) {
                    continue;
                }
                // Trouver le produit
                $stockProduct = StockProduct::find($productId);

                if ($stockProduct && $stockProduct->stock_id == $this->stockID) {
                    // Mettre à jour la quantité
                    $stockProduct->quantity += $quantity;
                    $stockProduct->user_id = Auth::id(); // Enregistrer qui a fait l'entrée
                    $stockProduct->save();

                    $entriesCount++;

                    // Optionnel : Créer un historique des mouvements de stock
                    $this->createStockMovement($stockProduct, $quantity, 'EN' , $this->prices[$productId]);
                }
            }

            DB::commit();

            if ($entriesCount > 0) {
                session()->flash('message', "Entrée réussie pour {$entriesCount} produit(s).");
                // Réinitialiser les quantités après succès
                $this->initializeQuantities();
            } else {
                session()->flash('error', 'Aucune quantité valide à traiter.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Optionnel : Créer un historique des mouvements de stock
     */
    private function createStockMovement($stockProduct, $quantity, $type, $price)
    {
        // Si vous avez une table stock_movements pour l'historique


        $movement = StockProductMouvement::create([
            'agency_id' => auth()->user()->agency_id ?? 1,
            'stock_id' => $this->stock->id,
            'stock_product_id' => $stockProduct->id, // Adjust if needed
            'item_code' => $stockProduct->product->id ?? 'N/A',
            'item_designation' => $stockProduct->product->name ?? 'N/A',
            'item_quantity' => $quantity,
            'item_measurement_unit' => $stockProduct->product->unit ?? 'pcs',
            'item_purchase_or_sale_price' => $price,
            'item_purchase_or_sale_currency' => 'FBU',
            'item_movement_type' => $type,
            'item_movement_date' => now(),
            'item_movement_note' => 'Entrée multiple',
            'user_id' => auth()->id(),
        ]);
    }

    public function render()
    {
        $query = StockProduct::with(['product', 'product.category'])
            ->where('stock_id', $this->stockID)
            ->when($this->search, function($query) {
                $query->whereHas('product', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function($query) {
                $query->whereHas('product', function($q) {
                    $q->where('category_id', $this->selectedCategory);
                });
            })
            ->orderBy('product_name');

        $products = $query->get();

        return view('livewire.stock.entre-multiple', [
            'products' => $products,
            'categories' => $this->availableCategories
        ]);
    }
}
