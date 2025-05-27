<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStockStoreRequest;
use App\Http\Requests\UserStockUpdateRequest;
use App\Models\UserStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserStockController extends Controller
{
    public function index(Request $request): View
    {
        $userStocks = UserStock::all();

        return view('userStock.index', [
            'userStocks' => $userStocks,
        ]);
    }

    public function create(Request $request): View
    {
        return view('userStock.create');
    }

    public function store(UserStockStoreRequest $request): RedirectResponse
    {
        $userStock = UserStock::create($request->validated());

        $request->session()->flash('userStock.id', $userStock->id);

        return redirect()->route('userStocks.index');
    }

    public function show(Request $request, UserStock $userStock): View
    {
        return view('userStock.show', [
            'userStock' => $userStock,
        ]);
    }

    public function edit(Request $request, UserStock $userStock): View
    {
        return view('userStock.edit', [
            'userStock' => $userStock,
        ]);
    }

    public function update(UserStockUpdateRequest $request, UserStock $userStock): RedirectResponse
    {
        $userStock->update($request->validated());

        $request->session()->flash('userStock.id', $userStock->id);

        return redirect()->route('userStocks.index');
    }

    public function destroy(Request $request, UserStock $userStock): RedirectResponse
    {
        $userStock->delete();

        return redirect()->route('userStocks.index');
    }
}
