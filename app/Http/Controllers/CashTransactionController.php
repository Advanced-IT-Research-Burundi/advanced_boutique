<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashTransactionStoreRequest;
use App\Http\Requests\CashTransactionUpdateRequest;
use App\Models\CashTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $cashTransactions = CashTransaction::all();

        return view('cashTransaction.index', [
            'cashTransactions' => $cashTransactions,
        ]);
    }

    public function create(Request $request): View
    {
        return view('cashTransaction.create');
    }

    public function store(CashTransactionStoreRequest $request): RedirectResponse
    {
        $cashTransaction = CashTransaction::create($request->validated());

        $request->session()->flash('cashTransaction.id', $cashTransaction->id);

        return redirect()->route('cashTransactions.index');
    }

    public function show(Request $request, CashTransaction $cashTransaction): View
    {
        return view('cashTransaction.show', [
            'cashTransaction' => $cashTransaction,
        ]);
    }

    public function edit(Request $request, CashTransaction $cashTransaction): View
    {
        return view('cashTransaction.edit', [
            'cashTransaction' => $cashTransaction,
        ]);
    }

    public function update(CashTransactionUpdateRequest $request, CashTransaction $cashTransaction): RedirectResponse
    {
        $cashTransaction->update($request->validated());

        $request->session()->flash('cashTransaction.id', $cashTransaction->id);

        return redirect()->route('cashTransactions.index');
    }

    public function destroy(Request $request, CashTransaction $cashTransaction): RedirectResponse
    {
        $cashTransaction->delete();

        return redirect()->route('cashTransactions.index');
    }
}
