<?php

namespace App\Http\Controllers\Api;

use App\Models\ExpenseType;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseTypeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ExpenseType::with('agency');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%$search%");
            }
            if ($request->filled('agency_id')) {
                $query->where('agency_id', $request->input('agency_id'));
            }

            $agencies = Agency::whereIn('id', ExpenseType::select('agency_id')->distinct()->pluck('agency_id'))->get();

            $data = [
                'expense_types' => $query->paginate(15)->withQueryString(),
                'agencies' => $agencies
            ];

            return sendResponse($data, 'Types de depenses  récupérés avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors de la récupération des types de dépenses', 500, $e->getMessage());
        }

    }



    public function store(Request $request)
    {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $data['created_by'] = auth()->id();
            $data['user_id'] = auth()->id();
            $data['agency_id'] = auth()->user()->agency_id;


            ExpenseType::create($data);
            return sendResponse($data, 'Type de dépense créé avec succès', 201);

    }

    public function show(ExpenseType $expense_type)
    {
        return sendResponse($expense_type, 'Type de dépense récupéré avec succès');
    }



    public function update(Request $request, $id)
    {
            $expensetype = ExpenseType::findOrFail($id);

            $validator = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
            ]);

            $expensetype->update([
                'name' => $validator['name'],
                'description' => $validator['description'],
                'user_id' => auth()->id(),
                'created_by' => auth()->id(),
                'agency_id' => auth()->user()->agency_id,
            ]);

            return sendResponse($expensetype, 'Type de depense mis à jour avec succès');

    }

    public function destroy(ExpenseType $expense_type)
    {
        try {
            $expense_type->delete();
            return sendResponse(null, 'Type de depense supprimé avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors de la validation de la suppression', 500, $e->getMessage());
        }
    }
}
