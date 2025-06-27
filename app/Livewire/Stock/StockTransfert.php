<?php

namespace App\Livewire\Stock;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\StockProductMouvement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
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
            unset($this->selectedProductsItems[$key]);
        }
    }

    public function transfer()
    {
        // Implémentez la logique de transfert ici
        // Parcourir $this->selectedProducts et $this->quantities

        $stockSource = Stock::find($this->stockSource);
        $stockDestination = Stock::find($this->destination_stock_id);

        if (!$stockSource || !$stockDestination) {
            session()->flash('error', 'Stock source ou destination non trouvé');
            return;
        }
        //check if stock source and destination are the same
        if ($stockSource->id == $stockDestination->id) {
            session()->flash('error', 'Stock source et destination sont les mêmes');
            return;
        }

        if (!$stockSource || !$stockDestination) {
            session()->flash('error', 'Stock source ou destination non trouvé');
            return;
        }
        try {
            DB::beginTransaction();
            $codeTransfert = time();
            foreach ($this->selectedProductsItems as $product) {
                $stockProduct = $product->stockProducts->where('stock_id', $this->stockSource)->first();
                if ($stockProduct) {
                    if ($stockProduct->quantity < $this->quantities[$product->id]) {
                        session()->flash('error', 'Quantité insuffisante pour le produit '.$product->name);
                        return;
                    }
                    $stockProduct->quantity -= $this->quantities[$product->id];
                    $stockProduct->save();
                    // Recherche de stock de destination
                    $stockProductDestination = $product->stockProducts->where('stock_id', $this->destination_stock_id)->first();
                    // Si non on le cree
                    if (!$stockProductDestination) {
                        $stockProductDestination = new StockProduct();
                        $stockProductDestination->stock_id = $this->destination_stock_id;
                        $stockProductDestination->product_id = $product->id;
                        $stockProductDestination->product_name = $product->name;
                        //$stockProductDestination->product_code = $product->code;
                        $stockProductDestination->quantity = $this->quantities[$product->id];
                        $stockProductDestination->save();
                    }
                    // Si oui on ajoute la quantité
                    if ($stockProductDestination) {
                        $stockProductDestination->quantity += $this->quantities[$product->id];
                        $stockProductDestination->save();
                    }
                    // Enregistrement du transfert
                    $this->updateTransferTable($product, $codeTransfert);
                    // Enregistrement du Mouvment du produit de stock
                    // stock_product_mouvements
                    $this->updateStockProductMouvement($stockSource, $stockProductDestination, $product, $codeTransfert,$stockProduct);
                }
            }
            // reset selected products
            $this->selectedProducts = [];
            $this->quantities = [];
            $this->updateProductListe($this->selectedCategory);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors du transfert '.
            $th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile());
            return;
        }
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

    public function updateTransferTable($product, $codeTransfert)
    {
        $transfer =  StockTransfer::create([
            'from_stock_id' => $this->stockSource,
            'to_stock_id' => $this->destination_stock_id,
            'product_id' => $product->id,
            'quantity' => $this->quantities[$product->id],
            'price' => 0,
            'user_id' => auth()->user()->id,
            'transfer_date' => now(),
            'note' =>   $codeTransfert,
            'product_code' => $product->code,
            'product_name' => $product->name,
            'agency_id' => $product->agency_id,
            'created_by' => auth()->user()->id,
        ]);
    }

    public function updateStockProductMouvement($stockSource,  $stockProductDestination, $product, $codeTransfert , $stockProductSource)
    {
        // stock source
        StockProductMouvement::create([
            'agency_id' => $product->agency_id,
            'stock_id' => $stockSource->id,
            'stock_product_id' => $stockProductSource->id,
            'item_code' => $product->code,
            'item_designation' => $product->name,
            'item_quantity' => $this->quantities[$product->id],
            'item_measurement_unit' => $product->unit ?? 'Piece',
            'item_purchase_or_sale_price' => $product->sale_price_ht,
            'item_purchase_or_sale_currency' => $product->sale_price_currency ?? 'BIF',
            'item_movement_type' => 'ST',
            'item_movement_invoice_ref' => '',
            'item_movement_description' => $codeTransfert,
            'item_movement_date' => now(),
            'item_product_detail_id' => $product->id,
            'is_send_to_obr' => null,
            'is_sent_at' => null,
            'user_id' => auth()->user()->id,
            'item_movement_note' => 'Transfert de stock',
        ]);
        // stock destination
        StockProductMouvement::create([
            'agency_id' => $product->agency_id,
            'stock_id' => $stockProductDestination->id,
            'stock_product_id' => $stockProductDestination->id,
            'item_code' => $product->code,
            'item_designation' => $product->name,
            'item_quantity' => $this->quantities[$product->id],
            'item_measurement_unit' => $product->unit ?? 'Piece',
            'item_purchase_or_sale_price' => $product->sale_price_ht,
            'item_purchase_or_sale_currency' => $product->sale_price_currency ?? 'BIF',
            'item_movement_type' => 'ET',
            'item_movement_invoice_ref' => $codeTransfert,
            'item_movement_description' => $codeTransfert,
            'item_movement_date' => now(),
            'item_product_detail_id' => $product->id,
            'is_send_to_obr' => null,
            'is_sent_at' => null,
            'user_id' => auth()->user()->id,
            'item_movement_note' => 'Transfert de distination ',
        ]);
    }
}
