<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProformaStoreRequest;
use App\Http\Requests\ProformaUpdateRequest;
use App\Models\Proforma;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProformaController extends Controller
{
    public function index(Request $request)
    {
        $query = Proforma::with(['stock', 'user', 'agency', 'createdBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
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
                          ->whereRaw('due_amount < total_amount');
                    break;
                case 'unpaid':
                    $query->whereRaw('due_amount = total_amount');
                    break;
            }
        }

        $proformas = $query->paginate(15);

        $totalRevenue = Proforma::sum('total_amount');
        $paidProformas = Proforma::where('due_amount', 0)->count();
        $totalDue = Proforma::sum('due_amount');
        $todayProformas = Proforma::whereDate('created_at', today())->count();

        return view('proforma.index', compact(
            'proformas',
            'totalRevenue',
            'paidProformas',
            'totalDue',
            'todayProformas'
        ));
    }

    public function show(Proforma $proforma)
    {
        $proforma->load(['stock', 'user', 'agency', 'createdBy']);

        // Decode proforma items
        $items = json_decode($proforma->proforma_items, true) ?? [];

        // Decode client data
        $client = json_decode($proforma->client, true) ?? [];

        return view('proforma.show', compact('proforma', 'items', 'client'));
    }

    public function create(Request $request): Response
    {
        return view('proforma.create');
    }

    public function store(ProformaStoreRequest $request): Response
    {
        $proforma = Proforma::create($request->validated());

        $request->session()->flash('proforma.id', $proforma->id);

        return redirect()->route('proformas.index');
    }

    public function edit(Request $request, Proforma $proforma): Response
    {
        return view('proforma.edit', [
            'proforma' => $proforma,
        ]);
    }

    public function update(ProformaUpdateRequest $request, Proforma $proforma): Response
    {
        $proforma->update($request->validated());

        $request->session()->flash('proforma.id', $proforma->id);

        return redirect()->route('proformas.index');
    }

    public function destroy(Request $request, Proforma $proforma): Response
    {
        $proforma->delete();

        return redirect()->route('proformas.index');
    }

    public function validateProforma(Proforma $proforma)
    {
         try {
            \DB::beginTransaction();


            $proformaItems = json_decode($proforma->proforma_items, true);
            $clientData = json_decode($proforma->client, true);

            if (empty($proformaItems)) {
                throw new \Exception('Aucun article trouvé dans le proforma');
            }

            $sale = Sale::create([
                'client_id' => $clientData['id'],
                'stock_id' => $proforma->stock_id,
                'user_id' => $proforma->user_id,
                'total_amount' => $proforma->total_amount,
                'paid_amount' => 0,
                'due_amount' => $proforma->due_amount,
                'sale_date' => now(),
                'type_facture' => 'F. PROFORMA',
                'agency_id' => $proforma->agency_id,
                'created_by' => auth()->id() ?? $proforma->created_by,
            ]);

            foreach ($proformaItems as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'sale_price' => $item['sale_price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $item['subtotal'],
                    'agency_id' => $proforma->agency_id,
                    'created_by' => auth()->id() ?? $proforma->created_by,
                    'user_id' => $proforma->user_id,
                ]);
            }

            $proforma->update([
                'invoice_type' => 'VALIDATED',
                'updated_at' => now()
            ]);

            \DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Proforma validée et convertie en vente avec succès.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la validation du proforma: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation du proforma: ' . $e->getMessage()
            ], 500);
        }
    }
}
