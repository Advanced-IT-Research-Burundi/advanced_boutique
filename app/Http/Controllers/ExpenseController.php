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
    public function index(Request $request): Response
    {
        $expenses = Expense::all();

        return view('expense.index', [
            'expenses' => $expenses,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('expense.create');
    }

    public function store(ExpenseStoreRequest $request): Response
    {
        $expense = Expense::create($request->validated());

        $request->session()->flash('expense.id', $expense->id);

        return redirect()->route('expenses.index');
    }

    public function show(Request $request, Expense $expense): Response
    {
        return view('expense.show', [
            'expense' => $expense,
        ]);
    }

    public function edit(Request $request, Expense $expense): Response
    {
        return view('expense.edit', [
            'expense' => $expense,
        ]);
    }

    public function update(ExpenseUpdateRequest $request, Expense $expense): Response
    {
        $expense->update($request->validated());

        $request->session()->flash('expense.id', $expense->id);

        return redirect()->route('expenses.index');
    }

    public function destroy(Request $request, Expense $expense): Response
    {
        $expense->delete();

        return redirect()->route('expenses.index');
    }
}
