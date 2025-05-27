<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockStoreRequest;
use App\Http\Requests\StockUpdateRequest;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): Response
    {
        $stocks = Stock::all();

        return view('stock.index', [
            'stocks' => $stocks,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('stock.create');
    }

    public function store(StockStoreRequest $request): Response
    {
        $stock = Stock::create($request->validated());

        $request->session()->flash('stock.id', $stock->id);

        return redirect()->route('stocks.index');
    }

    public function show(Request $request, Stock $stock): Response
    {
        return view('stock.show', [
            'stock' => $stock,
        ]);
    }

    public function edit(Request $request, Stock $stock): Response
    {
        return view('stock.edit', [
            'stock' => $stock,
        ]);
    }

    public function update(StockUpdateRequest $request, Stock $stock): Response
    {
        $stock->update($request->validated());

        $request->session()->flash('stock.id', $stock->id);

        return redirect()->route('stocks.index');
    }

    public function destroy(Request $request, Stock $stock): Response
    {
        $stock->delete();

        return redirect()->route('stocks.index');
    }
}
