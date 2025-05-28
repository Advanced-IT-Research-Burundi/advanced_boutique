<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StockStoreRequest;
use App\Http\Requests\StockUpdateRequest;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $stocks = Stock::with(['agency', 'user', 'creator'])
            ->when($search, function($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                          ->orWhere('location', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('stock.index', compact('stocks', 'search'));
    }

    public function create(): View
    {
        $agencies = Agency::all();

        return view('stock.create', compact('agencies'));
    }

    public function store(StockStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $stock = Stock::create($data);

        return redirect()
            ->route('stocks.show', $stock->id)
            ->with('success', 'Le stock a été créé avec succès.');
    }

    public function show(Stock $stock): View
    {
        $stock->load(['agency', 'user', 'creator']);
        return view('stock.show', compact('stock'));
    }

    public function edit(Stock $stock): View
    {
        $agencies = Agency::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view('stock.edit', compact('stock', 'agencies', 'users'));
    }

    public function update(StockUpdateRequest $request, Stock $stock): RedirectResponse
    {
        $stock->update($request->validated());

        return redirect()
            ->route('stocks.show', $stock->id)
            ->with('success', 'Le stock a été mis à jour avec succès.');
    }

    public function destroy(Stock $stock): RedirectResponse
    {
        // Vérifier s'il y a des articles liés avant de supprimer
        if ($stock->articles()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce stock car il contient des articles.');
        }

        $stock->delete();

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Le stock a été supprimé avec succès.');
    }
}
