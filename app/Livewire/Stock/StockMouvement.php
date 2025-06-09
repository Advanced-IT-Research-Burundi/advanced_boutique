<?php

namespace App\Livewire\Stock;

use Livewire\Component;
use App\Models\StockProductMouvement;
use Illuminate\Support\Facades\Auth;

class StockMouvement extends Component
{
    public $stock;

    // Form fields
    public $item_movement_type;
    public $item_purchase_or_sale_price = 0;
    public $item_purchase_or_sale_currency = 'BIF';
    public $item_quantity = 1;
    public $item_movement_date;
    public $item_movement_note;

    protected $rules = [
        'item_movement_type' => 'required|in:EN,ER,EI,EAJ,ET,EAU,SN,SP,SV,SD,SC,SAJ,ST,SAU',
        'item_purchase_or_sale_price' => 'required|numeric|min:0',
        'item_purchase_or_sale_currency' => 'required|string|max:3',
        'item_quantity' => 'required|numeric|gt:0',
        'item_movement_date' => 'required|date',
        'item_movement_note' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'item_movement_type.required' => 'The movement type is required.',
        'item_movement_type.in' => 'The selected movement type is invalid.',
        'item_purchase_or_sale_price.required' => 'The price is required.',
        'item_purchase_or_sale_price.numeric' => 'The price must be a number.',
        'item_purchase_or_sale_price.min' => 'The price must be at least 0.',
        'item_quantity.required' => 'The quantity is required.',
        'item_quantity.numeric' => 'The quantity must be a number.',
        'item_quantity.gt' => 'The quantity must be greater than 0.',
        'item_movement_date.required' => 'The movement date is required.',
        'item_movement_date.date' => 'The movement date is not a valid date.',
    ];

    public function mount($stock)
    {
        $this->stock = $stock;
        $this->item_movement_date = now()->format('Y-m-d\TH:i');
    }

    public function saveMovement()
    {

        $validatedData = $this->validate();

        try {
            $movement = StockProductMouvement::create([
                'agency_id' => auth()->user()->agency_id ?? 1,
                'stock_id' => $this->stock->id,
                'stock_product_id' => $this->stock->stock_id, // Adjust if needed
                'item_code' => $this->stock->id ?? 'N/A',
                'item_designation' => $this->stock->product->name ?? 'N/A',
                'item_quantity' => $validatedData['item_quantity'],
                'item_measurement_unit' => $this->stock->measurement_unit ?? 'pcs',
                'item_purchase_or_sale_price' => $validatedData['item_purchase_or_sale_price'],
                'item_purchase_or_sale_currency' => $validatedData['item_purchase_or_sale_currency'],
                'item_movement_type' => $validatedData['item_movement_type'],
                'item_movement_date' => $validatedData['item_movement_date'],
                'item_movement_note' => $validatedData['item_movement_note'],
                'user_id' => auth()->id(),
            ]);
            // Update stock quantity based on movement type
            $this->updateStockQuantity($movement);

            $this->dispatch('movement-saved', message: 'Stock movement recorded successfully!');
            $this->resetForm();
        } catch (\Exception $e) {
            dd($e);
            $this->dispatch('movement-error', message: 'Error saving stock movement: ' . $e->getMessage());
        }

        return redirect()->route('stocks.show', $this->stock->stock_id);
    }

    protected function updateStockQuantity($movement)
    {
        // Determine if this is an entry or exit movement
        $isEntry = in_array($movement->item_movement_type, ['EN', 'ER', 'EI', 'EAJ', 'ET', 'EAU']);

        if ($isEntry) {
            $this->stock->quantity += $movement->item_quantity;
            $this->stock->save();
        } else {
            $this->stock->quantity -= $movement->item_quantity;
            $this->stock->save();
        }
    }

    protected function resetForm()
    {
        $this->reset([
            'item_movement_type',
            'item_purchase_or_sale_price',
            'item_quantity',
            'item_movement_note',
        ]);
        $this->item_purchase_or_sale_currency = 'BIF';
        $this->item_quantity = 1;
        $this->item_movement_date = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('livewire.stock.stock-mouvement');
    }
}
