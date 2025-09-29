<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index(Request $request)
    {
      
        try {
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

            // Check if user is not admin, then restrict to their agency
            if (!Auth::user()->isAdmin()) {

                // get autorized stock 
                $stocks = Auth::user()->stocks->pluck('id')->toArray();
                $query->whereIn('stock_id', $stocks);
                $query->where('agency_id', Auth::user()->agency_id)
                ;
            }

            $sales = $query->paginate(10);
            $stats = $this->calculateStats();

            $data = [
                'sales' => $sales,
                'stats' => $stats,
                'filters' => $request->only(['search', 'date_from', 'date_to', 'status'])
            ];

            return sendResponse($data,'Ventes récupérées avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération des ventes: ' ,500, $e->getMessage());
        }
    }



       public function show(Sale $sale)
    {
        try {
            $sale->load(['client', 'saleItems.product', 'user']);
            $company = Company::where('is_actif', true)->first();

            $data = [
                'sale' => $sale,
                'company' => $company
            ];

            return sendResponse( $data ,'Vente récupérée avec succès', 200);

        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération de la vente: ' . $e->getMessage());
        }
    }



    public function store(Request $request)
    {

      
        
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'type_facture' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:stock_products,id',
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
                    return sendError("Stock insuffisant pour le produit: {$product->name}", 400);
                }
            }

            $paidAmount = floatval($request->paid_amount);
            $dueAmount = max(0, $totalAmount - $paidAmount);

            $sale = Sale::create([
                'client_id' => $request->client_id,
                'stock_id' => 1,
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'sale_date' => $request->sale_date,
                'type_facture' => $request->type_facture,
                'agency_id' => Auth::user()->agency_id,
                'created_by' => Auth::id(),
            ]);

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

            $sale->load(['client', 'saleItems.product', 'user']);

            return sendResponse( $sale,'Vente créée avec succès', 201);

        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Erreur lors de la création de la vente: ' ,500, $e->getMessage());
        }
    }

  

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'type_facture' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:stock_products,id',
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
                'type_facture' => $request->type_facture,
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

            $sale->load(['client', 'saleItems.product', 'user']);

            return sendResponse( $sale,'Vente modifiée avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Erreur lors de la modification de la vente: ' ,500, $e->getMessage());
        }
    }

    public function destroy(Sale $sale)
    {
        // return sendResponse(null,'Vente supprimée avec succès');
        DB::beginTransaction();

        try {
            if ($sale->paid_amount > 0) {
                return sendError('Impossible de supprimer une vente qui a été payée.', 400);
            }
            // Restore stock quantities
            foreach ($sale->saleItems as $item) {
                $stock = StockProduct::where('product_id', $item->product_id)
                ->where('stock_id', $sale->stock_id)->first();
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }

            // Delete sale items and sale
            $sale->saleItems()->delete();
            $sale->delete();

            DB::commit();



            return sendResponse(null,'Vente supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Erreur lors de la suppression de la vente: '.$e->getMessage() ,500,$e->getMessage());
        }
    }

    public function getProduct(Product $product)
    {
        try {
            $product->load('stocks');

            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'sale_price' => $product->sale_price,
                'unit' => $product->unit,
                'available_quantity' => $product->stocks->sum('quantity') ?? 0,
            ];

            return sendResponse( $data,'Produit récupéré avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de la récupération du produit: ' ,500,$e->getMessage());
        }
    }

    public function downloadPDF(Sale $sale)
    {
        try {
            $sale->load(['client', 'saleItems.product']);
            $company = Company::where('is_actif', true)->first();

            $data = [
                'sale' => $sale,
                'company' => $company,
                'title' => 'Facture #' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)
            ];

            $pdf = Pdf::loadView('sale.invoice-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            $filename = 'facture_' . str_pad($sale->id, 6, '0', STR_PAD_LEFT) . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return sendError('Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

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
    public function cancel($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($id);

            foreach ($sale->saleItems as $item) {
                $stock = StockProduct::where('product_id', $item->product_id)
                ->where('stock_id', $sale->stock_id)->first();
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }else {
                    return sendError('Stock not found for product ID: ' . $item->product_id, 404);
                }

            }

            if ($sale->status === 'cancelled') {
                return sendError('Cette vente est déjà annulée.', 400);
            }

            // if ($sale->paid_amount > 0) {
            //     return sendError('Impossible d\'annuler une vente qui a été payée.', 400);
            // }

            $sale->status = 'cancelled';
            $sale->description = $request->input('description', 'Vente annulée');
            $sale->save();
            DB::commit();
            return sendResponse($sale, 'Vente annulée avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Erreur lors de l\'annulation: ' . $e->getMessage(), 500, $e->getMessage());
        }
    }

    /**
     * Enregistrer un paiement pour une vente
     */
    public function payment($id, Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
        ]);

        try {
            $sale = Sale::findOrFail($id);

            if ($sale->status === 'cancelled') {
                return sendError('Impossible d\'ajouter un paiement à une vente annulée.', 400);
            }


            if ($sale->due_amount <= 0) {
                return sendError('Cette vente est déjà entièrement payée.', 400);
            }

            $paymentAmount = $request->amount;

            // Vérifier que le montant ne dépasse pas le montant dû
            if ($paymentAmount > $sale->due_amount) {
                return sendError('Le montant du paiement ne peut pas dépasser le montant dû (' . number_format($sale->due_amount, 0, ',', ' ') . ' F).', 400);
            }

            $sale->paid_amount += $paymentAmount;
            $sale->due_amount -= $paymentAmount;

            if ($sale->due_amount <= 0) {
                $sale->status = 'paid';
            } elseif ($sale->paid_amount > 0) {
                $sale->status = 'partial';
            }

            $sale->save();

            return sendResponse($sale, 'Paiement enregistré avec succès');

        } catch (\Exception $e) {
            return sendError('Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage(), 500, $e->getMessage());
        }
    }
}

