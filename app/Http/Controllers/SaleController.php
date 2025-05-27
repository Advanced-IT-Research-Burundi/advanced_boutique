<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleStoreRequest;
use App\Http\Requests\SaleUpdateRequest;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): Response
    {
        $sales = Sale::all();

        return view('sale.index', [
            'sales' => $sales,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('sale.create');
    }

    public function store(SaleStoreRequest $request): Response
    {
        $sale = Sale::create($request->validated());

        $request->session()->flash('sale.id', $sale->id);

        return redirect()->route('sales.index');
    }

    public function show(Request $request, Sale $sale): Response
    {
        return view('sale.show', [
            'sale' => $sale,
        ]);
    }

    public function edit(Request $request, Sale $sale): Response
    {
        return view('sale.edit', [
            'sale' => $sale,
        ]);
    }

    public function update(SaleUpdateRequest $request, Sale $sale): Response
    {
        $sale->update($request->validated());

        $request->session()->flash('sale.id', $sale->id);

        return redirect()->route('sales.index');
    }

    public function destroy(Request $request, Sale $sale): Response
    {
        $sale->delete();

        return redirect()->route('sales.index');
    }
}
