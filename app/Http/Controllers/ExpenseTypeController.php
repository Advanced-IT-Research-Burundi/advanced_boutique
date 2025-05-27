<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseTypeStoreRequest;
use App\Http\Requests\ExpenseTypeUpdateRequest;
use App\Models\ExpenseType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseTypeController extends Controller
{
    public function index(Request $request): View
    {
        $expenseTypes = ExpenseType::all();

        return view('expenseType.index', [
            'expenseTypes' => $expenseTypes,
        ]);
    }

    public function create(Request $request): View
    {
        return view('expenseType.create');
    }

    public function store(ExpenseTypeStoreRequest $request): RedirectResponse
    {
        $expenseType = ExpenseType::create($request->validated());

        $request->session()->flash('expenseType.id', $expenseType->id);

        return redirect()->route('expenseTypes.index');
    }

    public function show(Request $request, ExpenseType $expenseType): View
    {
        return view('expenseType.show', [
            'expenseType' => $expenseType,
        ]);
    }

    public function edit(Request $request, ExpenseType $expenseType): View
    {
        return view('expenseType.edit', [
            'expenseType' => $expenseType,
        ]);
    }

    public function update(ExpenseTypeUpdateRequest $request, ExpenseType $expenseType): RedirectResponse
    {
        $expenseType->update($request->validated());

        $request->session()->flash('expenseType.id', $expenseType->id);

        return redirect()->route('expenseTypes.index');
    }

    public function destroy(Request $request, ExpenseType $expenseType): RedirectResponse
    {
        $expenseType->delete();

        return redirect()->route('expenseTypes.index');
    }
}
