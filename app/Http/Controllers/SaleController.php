<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Client;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['client', 'saleItems.product', 'user'])
                    ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'paid':
                    $query->where('due_amount', 0);
                    break;
                case 'partial':
                    $query->where('due_amount', '>', 0)
                          ->where('paid_amount', '>', 0);
                    break;
                case 'unpaid':
                    $query->where('paid_amount', 0);
                    break;
            }
        }

        $sales = $query->paginate(20);

        // Calculate statistics
        $stats = $this->calculateStats();

        return view('sale.index', compact('sales') + $stats);
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::with('stocks')
                          ->where('alert_quantity', '>', 0)
                          ->orderBy('name')
                          ->get();

        return view('sale.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate totals
            $totalAmount = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = floatval($item['quantity']);
                $salePrice = floatval($item['sale_price']);
                $discount = floatval($item['discount'] ?? 0);

                $subtotal = ($quantity * $salePrice) * (1 - $discount / 100);
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'sale_price' => $salePrice,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ];

                // Check stock availability
                $stock = Stock::where('product_id', $product->id)->first();
                if (!$stock || $stock->quantity < $quantity) {
                    throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
                }
            }

            $paidAmount = floatval($request->paid_amount);
            $dueAmount = max(0, $totalAmount - $paidAmount);

            // Create sale
            $sale = Sale::create([
                'client_id' => $request->client_id,
                'stock_id' => 1, // You might want to handle this differently
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'sale_date' => $request->sale_date,
                'agency_id' => Auth::user()->agency_id,
                'created_by' => Auth::id(),
            ]);

            // Create sale items and update stock
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'sale_price' => $item['sale_price'],
                    'discount' => $item['discount'],
                    'subtotal' => $item['subtotal'],
                    'agency_id' => Auth::user()->agency_id,
                    'created_by' => Auth::id(),
                    'user_id' => Auth::id(),
                ]);

                // Update stock
                $stock = Stock::where('product_id', $item['product_id'])->first();
                $stock->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('sales.show', $sale)
                ->with('success', 'Vente créée avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['client', 'saleItems.product', 'user']);

        return view('sale.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load(['saleItems.product']);
        $clients = Client::orderBy('name')->get();
        $products = Product::with('stocks')->orderBy('name')->get();

        return view('sale.edit', compact('sale', 'clients', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Restore original stock quantities
            foreach ($sale->saleItems as $originalItem) {
                $stock = Stock::where('product_id', $originalItem->product_id)->first();
                if ($stock) {
                    $stock->increment('quantity', $originalItem->quantity);
                }
            }

            // Delete existing sale items
            $sale->saleItems()->delete();

            // Calculate new totals
            $totalAmount = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = floatval($item['quantity']);
                $salePrice = floatval($item['sale_price']);
                $discount = floatval($item['discount'] ?? 0);

                $subtotal = ($quantity * $salePrice) * (1 - $discount / 100);
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'sale_price' => $salePrice,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ];

                // Check stock availability
                $stock = Stock::where('product_id', $product->id)->first();
                if (!$stock || $stock->quantity < $quantity) {
                    throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
                }
            }

            $paidAmount = floatval($request->paid_amount);
            $dueAmount = max(0, $totalAmount - $paidAmount);

            // Update sale
            $sale->update([
                'client_id' => $request->client_id,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'sale_date' => $request->sale_date,
            ]);

            // Create new sale items and update stock
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'sale_price' => $item['sale_price'],
                    'discount' => $item['discount'],
                    'subtotal' => $item['subtotal'],
                    'agency_id' => Auth::user()->agency_id,
                    'created_by' => Auth::id(),
                    'user_id' => Auth::id(),
                ]);

                // Update stock
                $stock = Stock::where('product_id', $item['product_id'])->first();
                $stock->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('sale.show', $sale)
                ->with('success', 'Vente modifiée avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Sale $sale)
    {
        DB::beginTransaction();

        try {
            // Restore stock quantities
            foreach ($sale->saleItems as $item) {
                $stock = Stock::where('product_id', $item->product_id)->first();
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }

            // Delete sale items and sale
            $sale->saleItems()->delete();
            $sale->delete();

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Vente supprimée avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get product details for AJAX requests
     */
    public function getProduct(Product $product)
    {
        $product->load('stocks');

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sale_price' => $product->sale_price,
            'unit' => $product->unit,
            'available_quantity' => $product->stocks->sum('quantity') ?? 0,
        ]);
    }

    /**
     * Calculate dashboard statistics
     */
    private function calculateStats()
    {
        $today = now()->startOfDay();

        return [
            'totalRevenue' => Sale::sum('total_amount'),
            'paidSales' => Sale::where('due_amount', 0)->count(),
            'totalDue' => Sale::sum('due_amount'),
            'todaySales' => Sale::whereDate('sale_date', $today)->count(),
        ];
    }
}
