<?php

namespace App\Http\Controllers\Api;

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
    /**
     * Liste des achats
     */
    public function index(Request $request)
    {
        $query = Purchase::query()->with(['supplier', 'stock']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('total_amount', 'like', "%$search%")
                    ->orWhere('paid_amount', 'like', "%$search%")
                    ->orWhere('due_amount', 'like', "%$search%");
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('stock_id')) {
            $query->where('stock_id', $request->input('stock_id'));
        }

        $purchases = $query->orderByDesc('purchase_date')->paginate(15);

        return sendResponse($purchases, 'Liste des achats récupérée avec succès');
    }

    /**
     * Créer un nouvel achat
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'stock_id' => 'required|exists:stocks,id',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return sendError('Erreur de validation', 422, $validator->errors());
        }

        // Vérifier que paid_amount + due_amount = total_amount
        $data = $validator->validated();
        if (abs(($data['paid_amount'] + $data['due_amount']) - $data['total_amount']) > 0.01) {
            return sendError('Les montants ne correspondent pas au total.', 422);
        }

        $purchase = Purchase::create($data);

        return sendResponse($purchase, 'Achat créé avec succès', 201);
    }

    /**
     * Afficher un achat
     */
    public function show(Purchase $purchase)
    {
        return sendResponse($purchase->load(['supplier', 'stock']), 'Détail de l\'achat récupéré avec succès');
    }

    /**
     * Mettre à jour un achat
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'stock_id' => 'sometimes|required|exists:stocks,id',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'paid_amount' => 'sometimes|required|numeric|min:0',
            'due_amount' => 'sometimes|required|numeric|min:0',
            'purchase_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return sendError('Erreur de validation', 422, $validator->errors());
        }

        $data = $validator->validated();

        // Si des montants sont mis à jour, vérifier la cohérence
        if (isset($data['total_amount']) || isset($data['paid_amount']) || isset($data['due_amount'])) {
            $total = $data['total_amount'] ?? $purchase->total_amount;
            $paid = $data['paid_amount'] ?? $purchase->paid_amount;
            $due = $data['due_amount'] ?? $purchase->due_amount;

            if (abs(($paid + $due) - $total) > 0.01) {
                return sendError('Les montants ne correspondent pas au total.', 422);
            }
        }

        $purchase->update($data);

        return sendResponse($purchase->load(['supplier', 'stock']), 'Achat mis à jour avec succès');
    }

    /**
     * Supprimer un achat
     */
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return sendResponse(null, 'Achat supprimé avec succès');
    }
}
