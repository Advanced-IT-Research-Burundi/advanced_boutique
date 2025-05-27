<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $expenses = Expense::all();

        return view('expense.index', [
            'expenses' => $expenses,
        ]);
    }

    public function create(Request $request): View
    {
        return view('expense.create');
    }

    public function store(ExpenseStoreRequest $request): RedirectResponse
    {
        $expense = Expense::create($request->validated());

        $request->session()->flash('expense.id', $expense->id);

        return redirect()->route('expenses.index');
    }

    public function show(Request $request, Expense $expense): View
    {
        return view('expense.show', [
            'expense' => $expense,
        ]);
    }

    public function edit(Request $request, Expense $expense): View
    {
        return view('expense.edit', [
            'expense' => $expense,
        ]);
    }

    public function update(ExpenseUpdateRequest $request, Expense $expense): RedirectResponse
    {
        $expense->update($request->validated());

        $request->session()->flash('expense.id', $expense->id);

        return redirect()->route('expenses.index');
    }

    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('expenses.index');
    }
}
