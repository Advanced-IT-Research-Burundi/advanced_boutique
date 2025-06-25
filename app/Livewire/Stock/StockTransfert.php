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
    public $product_id;
    public $quantity;
    public $products = [];
    public $categories = [];

    public function mount()
    {
        $this->stock_id = auth()->user()->stock_id;
    }
    public function render()
    {
        $stocks = Stock::all();

        return view('livewire.stock.stock-transfert', compact('stocks'  ));
    }
    public function updateStockSource()
    {

        $this->categories = Category::with('products.stockProducts')
            ->whereHas('products.stockProducts', function ($query) {
                $query->where('stock_id', $this->stockSource);
            })  ->get();

    }

    public function updateProductListe($category_id)
    {
        $stock_source = $this->stockSource;
        $this->products = Product::with('stockProducts')
            ->whereHas('stockProducts', function ($query) use ( $stock_source) {
                $query->where('stock_id', $stock_source);
            })
            ->where('category_id', $category_id)
            ->get();
    }
    public function updatedStockDestination()
    {
        dd($this->destination_stock_id);
    }
}
