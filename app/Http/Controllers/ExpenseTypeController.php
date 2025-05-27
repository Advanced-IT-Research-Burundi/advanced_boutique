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
    public function index(Request $request): Response
    {
        $expenseTypes = ExpenseType::all();

        return view('expenseType.index', [
            'expenseTypes' => $expenseTypes,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('expenseType.create');
    }

    public function store(ExpenseTypeStoreRequest $request): Response
    {
        $expenseType = ExpenseType::create($request->validated());

        $request->session()->flash('expenseType.id', $expenseType->id);

        return redirect()->route('expenseTypes.index');
    }

    public function show(Request $request, ExpenseType $expenseType): Response
    {
        return view('expenseType.show', [
            'expenseType' => $expenseType,
        ]);
    }

    public function edit(Request $request, ExpenseType $expenseType): Response
    {
        return view('expenseType.edit', [
            'expenseType' => $expenseType,
        ]);
    }

    public function update(ExpenseTypeUpdateRequest $request, ExpenseType $expenseType): Response
    {
        $expenseType->update($request->validated());

        $request->session()->flash('expenseType.id', $expenseType->id);

        return redirect()->route('expenseTypes.index');
    }

    public function destroy(Request $request, ExpenseType $expenseType): Response
    {
        $expenseType->delete();

        return redirect()->route('expenseTypes.index');
    }
}
