<?php

namespace App\Livewire\Stock;

use App\Models\Stock;
use Livewire\Component;

class StockTransfert extends Component
{
    public $stock_id;
    public $destination_stock_id;
    public $product_id;
    public $quantity;

    public function mount()
    {
        $this->stock_id = auth()->user()->stock_id;
    }
    public function render()
    {
        $stocks = Stock::all();
        return view('livewire.stock.stock-transfert', compact('stocks'));
    }
}
