<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use App\Models\Stock;
use App\Models\User;
use App\Models\ExpenseType;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::with(['stock', 'user', 'expenseType', 'agency']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                  ->orWhere('amount', 'like', "%$search%");
            });
        }
        if ($request->filled('expense_type_id')) {
            $query->where('expense_type_id', $request->input('expense_type_id'));
        }
        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->input('agency_id'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $expenses = $query->orderByDesc('expense_date')->paginate(15)->withQueryString();
        $expenseTypes = ExpenseType::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();
        $users = User::orderBy('first_name')->get();
        
    }

    public function create(): View
    {
        $stocks = Stock::orderBy('name')->get();
        $users = User::orderBy('first_name')->get();
        $expenseTypes = ExpenseType::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();

        return view('expense.create', compact('stocks', 'users', 'expenseTypes', 'agencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);
        $data['created_by'] = auth()->id();
        $data['user_id'] = auth()->id();

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Dépense créée avec succès.');
    }

    public function show(Expense $expense): View
    {
        $expense->load(['stock', 'user', 'expenseType', 'agency']);
        return view('expense.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        $stocks = Stock::orderBy('name')->get();
        $users = User::orderBy('first_name')->get();
        $expenseTypes = ExpenseType::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();

        return view('expense.edit', compact('expense', 'stocks', 'users', 'expenseTypes', 'agencies'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $data = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'user_id' => 'required|exists:users,id',
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);
        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Dépense modifiée avec succès.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Dépense supprimée avec succès.');
    }
}
