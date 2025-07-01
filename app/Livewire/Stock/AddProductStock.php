<?php

namespace App\Livewire\Stock;

use App\Models\Product;
use App\Models\StockProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;
class AddProductStock extends Component
{
    public $stock;
    public $search;
    public $products = [];
    public $stockProductSearch = '';

    public function mount($stock)
    {
        $this->stock = $stock;
    }
    // Ajoutez cette méthode pour la recherche des produits du stock
    public function searchStockProducts()
    {
        $query = StockProduct::with(['product'])
        ->where('stock_id', $this->stock->id);

        if ($this->stockProductSearch) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->stockProductSearch . '%')
                ->orWhere('description', 'like', '%' . $this->stockProductSearch . '%');
            });
        }

        return $query->paginate(10);
    }

    public function render()
    {
        // Recuperer la liste des produits qui ne sont pa lies avec ce stock en Passant par le model StockProduit
        $stockProducts = $this->searchStockProducts();
        return view('livewire.stock.add-product-stock', compact('stockProducts'));
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

    public function exportToExcel()
    {
        // $stockProducts = $this->searchStockProducts();
        // Stocker un token en session
            $token = Str::random(32);
            session(['excel_export_' . $token => [
            'stock_id' => $this->stock->id,
            'expires_at' => now()->addMinutes(5)
            ]]);
            // Rediriger vers la route d'export
            return redirect()->route('export.excel', ['token' => $token])->with('success', 'Export en cours...');
        }

        public function exportToPdf()
        {
            $stockProducts = $this->searchStockProducts();
            $pdf = PDF::loadView('exports.stock-product-pdf', compact('stockProducts'))
            ->setPaper('a4', 'landscape');
            $filename = 'stock_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', $this->stock->name) . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);

        }

        public function searchProduct()
        {

            $this->products = Product::where('name', 'like', "%{$this->search}%")->take(5)->get();
            $stockProducts = StockProduct::with(['product'])->where('stock_id', $this->stock->id)->get();

            $this->products = $this->products->filter(function ($product) use ($stockProducts) {
                return !$stockProducts->contains('product_id', $product->id);
            });

            // return view('livewire.stock.add-product-stock', compact('stockProducts'));
        }
    }
