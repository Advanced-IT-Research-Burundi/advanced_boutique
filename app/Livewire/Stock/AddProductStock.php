<?php

namespace App\Livewire\Stock;

use App\Models\Product;
use App\Models\StockProduct;
use Livewire\Component;

class AddProductStock extends Component
{
    public $stock;

    public function mount($stock)
    {
        $this->stock = $stock;
    }

    public function render()
    {
        // Recuperer la liste des produits qui ne sont pa lies avec ce stock en Passant par le model StockProduit

        $products = Product::all();

        return view('livewire.stock.add-product-stock', compact('products'));
    }

    public function addProduct($productId)
    {
        $stockProduct = new StockProduct();
        $stockProduct->stock_id = $this->stock->id;
        $stockProduct->product_id = $productId;
        $stockProduct->save();

        $this->dispatch('stock-product-added', stockId: $this->stock->id);

        return redirect()->route('stocks.list', $this->stock->id);

    }
}
