<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseStoreRequest;
use App\Http\Requests\PurchaseUpdateRequest;
use App\Models\Purchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): Response
    {
        $purchases = Purchase::all();

        return view('purchase.index', [
            'purchases' => $purchases,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('purchase.create');
    }

    public function store(PurchaseStoreRequest $request): Response
    {
        $purchase = Purchase::create($request->validated());

        $request->session()->flash('purchase.id', $purchase->id);

        return redirect()->route('purchases.index');
    }

    public function show(Request $request, Purchase $purchase): Response
    {
        return view('purchase.show', [
            'purchase' => $purchase,
        ]);
    }

    public function edit(Request $request, Purchase $purchase): Response
    {
        return view('purchase.edit', [
            'purchase' => $purchase,
        ]);
    }

    public function update(PurchaseUpdateRequest $request, Purchase $purchase): Response
    {
        $purchase->update($request->validated());

        $request->session()->flash('purchase.id', $purchase->id);

        return redirect()->route('purchases.index');
    }

    public function destroy(Request $request, Purchase $purchase): Response
    {
        $purchase->delete();

        return redirect()->route('purchases.index');
    }
}
