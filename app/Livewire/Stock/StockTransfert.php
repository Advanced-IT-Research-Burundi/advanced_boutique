<?php

namespace App\Livewire\Stock;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use Livewire\Component;

class StockTransfert extends Component
{
    public $stock_id;
    public $stockSource;
    public $destination_stock_id;
    public $search = '';
    public $products = [];
    public $categories = [];
    public $selectedCategory;
    public $quantities = [];
    public $selectedProducts = [];
    public $selectedProductsItems = [];

    public $firstName = 'Jean';
    public $lastName = 'Lionel';



    public function mount()
    {
        $this->stock_id = auth()->user()->stock_id;
    }

    public function render()
    {
        $stocks = Stock::all();
        return view('livewire.stock.stock-transfert', compact('stocks'));
    }

    public function updateStockSource()
    {
        $this->categories = Category::with('products.stockProducts')
            ->whereHas('products.stockProducts', function ($query) {
                $query->where('stock_id', $this->stockSource);
            })->get();

        if ($this->categories->isNotEmpty()) {
            $this->updateProductListe($this->categories->first()->id);
        } else {
            $this->products = [];
        }
    }

    public function updateProductListe($category_id = null)
    {
        $this->selectedCategory = $category_id;

        $query = Product::with(['stockProducts', 'category'])
            ->whereHas('stockProducts', function ($query) {
                $query->where('stock_id', $this->stockSource);
            });

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->selectedProducts) {
            $query->whereNotIn('id', $this->selectedProducts);
        }

        $this->products = $query->get();
    }

    public function updatedSearch()
    {
        $this->updateProductListe($this->selectedCategory);
    }

    public function addToTransfer($productId)
    {
        if (!in_array($productId, $this->selectedProducts)) {
            $this->selectedProducts[] = $productId;
            $this->selectedProductsItems[] = $this->products->whereIn('id', $productId)->first();
            $this->quantities[$productId] = 1;
        }
    }

    public function getSelectedProductsItemsProperty()
    {
        return $this->products->whereIn('id', $this->selectedProducts);
    }

    public function removeFromTransfer($productId)
    {
        if (($key = array_search($productId, $this->selectedProducts)) !== false) {
            unset($this->selectedProducts[$key]);
            unset($this->quantities[$productId]);
        }
    }

    public function transfer()
    {
        // Implémentez la logique de transfert ici
        // Parcourir $this->selectedProducts et $this->quantities

        // Réinitialiser après le transfert
        $this->selectedProducts = [];
        $this->quantities = [];
        $this->updateProductListe($this->selectedCategory);

        session()->flash('message', 'Transfert effectué avec succès!');
    }

    public function addProduct($productId)
    {
        $this->addToTransfer($productId);
    }

    public function updatedDestinationStockId()
    {
        // Logique spécifique lors du changement de stock de destination si nécessaire
    }
}
