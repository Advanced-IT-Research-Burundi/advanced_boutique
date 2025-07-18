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
            'expenseTypes' => $query->paginate(15)->withQueryString(),
            'agencies' => $agencies
        ];

        return sendResponse($data, 'Types de depenses  récupérés avec succès');
    }

    public function create(): View
    {
        $agencies = Agency::orderBy('name')->get();
        return view('expenseType.create', compact('agencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'agency_id' => 'required|exists:agencies,id',
        ]);
        $data['created_by'] = auth()->id();

        ExpenseType::create($data);

        return redirect()->route('expense-types.index')->with('success', 'Type de dépense créé avec succès.');
    }

    public function show(ExpenseType $expense_type): View
    {
        return view('expenseType.show', compact('expense_type'));
    }

    public function edit(ExpenseType $expense_type): View
    {
        $agencies = Agency::orderBy('name')->get();
        return view('expenseType.edit', compact('expense_type', 'agencies'));
    }

    public function update(Request $request, ExpenseType $expensetype): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'agency_id' => 'required|exists:agencies,id',
        ]);
        $expensetype->update($data);

        return redirect()->route('expense-types.index')->with('success', 'Type de dépense modifié avec succès.');
    }

    public function destroy(ExpenseType $expense_type): RedirectResponse
    {
        $expense_type->delete();
        return redirect()->route('expense-types.index')->with('success', 'Type de dépense supprimé avec succès.');
    }
}
