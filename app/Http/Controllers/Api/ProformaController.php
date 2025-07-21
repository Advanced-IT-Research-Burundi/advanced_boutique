<?php

namespace App\Http\Controllers\Api;

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


         $stats = [
            'totalRevenue' => $totalRevenue,
            'paidProformas'=> $paidProformas,
            'totalDue' => $totalDue,
            'todayProformas' => $todayProformas
         ];

        $data = [
            'proformas' =>$proformas,
            'stats'=> $stats
        ];

        return  sendResponse($data,'Proformas retrieved successfully');
    }

    public function show(Proforma $proforma)
    {
        $proforma->load(['stock', 'user', 'agency', 'createdBy']);

        // Decode proforma items
        $items = json_decode($proforma->proforma_items, true) ?? [];

        // Decode client data
        $client = json_decode($proforma->client, true) ?? [];

        return sendResponse([
            'proforma' => $proforma,
            'items' => $items,
            'client' => $client
        ], 'Proforma retrieved successfully', 200);
    }

    public function create(Request $request)
    {
        return sendResponse([
            'proforma' => new Proforma(),
        ], 'Proforma created successfully', 200);
    }

    public function store(ProformaStoreRequest $request)
    {
        $proforma = Proforma::create($request->validated());

        return sendResponse([
            'proforma' => $proforma,
        ], 'Proforma created successfully', 200);
    }

    public function edit(Request $request, Proforma $proforma)
    {
        return sendResponse([
            'proforma' => $proforma,
        ], 'Proforma edited successfully', 200);
    }

    public function update(ProformaUpdateRequest $request, Proforma $proforma)
    {
        $proforma->update($request->validated());

        return sendResponse([
            'proforma' => $proforma,
        ], 'Proforma updated successfully', 200);
    }



    public function destroy(Request $request, Proforma $proforma)
    {
        try{
            $proforma->delete();
            return sendResponse(null, 'Proforma deleted successfully', 200);

        } catch (\Throwable $e) {
            return sendError('Erreur lors de la suppression', 500, $e->getMessage());
        }

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
                'invoice_type' => 'F. PROFORMA VALIDÉE',
                'updated_at' => now()
            ]);

            \DB::commit();

            return sendResponse([
                'sale' => $sale,
            ], 'Proforma validée et convertie en vente avec succès.', 200);

        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la validation du proforma: ' . $e->getMessage());

            return sendError('Erreur lors de la validation du proforma: ' . $e->getMessage());
        }
    }
}
