<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'stock', 'agency', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('purchase.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $stocks = Stock::all();
        $products = Product::with('category')->get();
        $agencies = Agency::all();

        return view('purchase.create', compact('suppliers', 'stocks', 'products', 'agencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'stock_id' => 'required|exists:stocks,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Calculate totals
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['purchase_price'];
            }

            $paidAmount = $request->paid_amount ?? 0;
            $dueAmount = $totalAmount - $paidAmount;

            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'stock_id' => $request->stock_id,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'purchase_date' => $request->purchase_date,
                'agency_id' => $request->agency_id,
                'created_by' => Auth::id(),
            ]);

            // Create purchase items
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['purchase_price'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'subtotal' => $subtotal,
                    'agency_id' => $request->agency_id,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Achat créé avec succès!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'stock', 'agency', 'createdBy', 'purchaseItems.product']);

        return view('purchase.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('purchaseItems.product');
        $suppliers = Supplier::all();
        $stocks = Stock::all();
        $products = Product::with('category')->get();
        $agencies = Agency::all();

        return view('purchase.edit', compact('purchase', 'suppliers', 'stocks', 'products', 'agencies'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'stock_id' => 'required|exists:stocks,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            // Delete existing items
            $purchase->purchaseItems()->delete();

            // Calculate totals
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['purchase_price'];
            }

            $paidAmount = $request->paid_amount ?? 0;
            $dueAmount = $totalAmount - $paidAmount;

            // Update purchase
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'stock_id' => $request->stock_id,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'purchase_date' => $request->purchase_date,
                'agency_id' => $request->agency_id,
            ]);

            // Create new purchase items
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['purchase_price'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'subtotal' => $subtotal,
                    'agency_id' => $request->agency_id,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Achat mis à jour avec succès!');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Achat supprimé avec succès!');
    }
}
