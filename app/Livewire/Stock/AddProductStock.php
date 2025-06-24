<?php

namespace App\Livewire\Stock;

use App\Models\Product;
use App\Models\StockProduct;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddProductStock extends Component
{
    public $stock;
    public $search;
    public $products = [];

    public function mount($stock)
    {
        $this->stock = $stock;
    }

    public function render()
    {
        // Recuperer la liste des produits qui ne sont pa lies avec ce stock en Passant par le model StockProduit
        $stockProducts = StockProduct::with(['product'])
        ->where('stock_id', $this->stock->id)->paginate();
        return view('livewire.stock.add-product-stock', compact( 'stockProducts'));
    }

    public function addProduct($productId)
    {
        $stockProduct = new StockProduct();
        $stockProduct->stock_id = $this->stock->id;
        $stockProduct->product_id = $productId;
        $stockProduct->quantity = 0;
        $stockProduct->agency_id = $this->stock->agency_id;
        $stockProduct->user_id = Auth::user()->id;
        $stockProduct->product_name = Product::find($productId)->name;
        $stockProduct->save();
        $this->dispatch('stock-product-added', stockId: $this->stock->id);

      $this->searchProduct();

    }

    public function searchProduct(){

        $this->products = Product::where('name', 'like', "%{$this->search}%")->take(5)->get();
        $stockProducts = StockProduct::with(['product'])->where('stock_id', $this->stock->id)->get();

        $this->products = $this->products->filter(function ($product) use ($stockProducts) {
            return !$stockProducts->contains('product_id', $product->id);
        });

        return view('livewire.stock.add-product-stock', compact( 'stockProducts'));
    }
}
