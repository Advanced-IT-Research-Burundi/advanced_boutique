<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Agency;
use Illuminate\Support\Facades\Validator;

class SupplierController extends \App\Http\Controllers\Controller
{
    /**
     * Liste des fournisseurs (API)
     */
    public function index(Request $request)
    {
        $query = Supplier::query()->with('agency');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->input('agency_id'));
        }

        $suppliers = $query->orderBy('name')->paginate(15);
        $agencies = Agency::whereIn('id', Supplier::select('agency_id')->distinct()->pluck('agency_id'))->latest()->get();

        $data = [
            'suppliers' => $suppliers,
            'agencies' => $agencies
        ];
        return sendResponse($data, 'Liste des fournisseurs récupérée avec succès');
    }

    /**
     * Créer un fournisseur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);



        if ($validator->fails()) {
            return sendError('Erreur de validation', 422, $validator->errors());
        }


        $data = $validator->validated();
        $data['created_by'] = auth()->id();
        $data['agency_id'] = $request->input('agency_id', auth()->user()->agency_id);

        $supplier = Supplier::create($data);

        return sendResponse($supplier, 'Fournisseur créé avec succès', 201);
    }

    /**
     * Afficher un fournisseur
     */
    public function show(Supplier $supplier)
    {
        return sendResponse($supplier, 'Détail du fournisseur récupéré avec succès');
    }

    /**
     * Mettre à jour un fournisseur
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return sendError('Erreur de validation', 422, $validator->errors());
        }
        $data = $validator->validated();
        $data['agency_id'] = $request->input('agency_id', auth()->user()->agency_id);
        $data['user_id'] = auth()->id();

        $supplier->update($data);

        return sendResponse($supplier, 'Fournisseur mis à jour avec succès');
    }

    /**
     * Supprimer un fournisseur
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return sendResponse(null, 'Fournisseur supprimé avec succès');
    }
}
