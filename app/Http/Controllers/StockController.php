<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function list(Stock $stock)
    {

        return view('stock.list', compact('stock'));
    }
    public function index(Request $request)
    {
        $query = Stock::with(['agency', 'createdBy', 'user']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Tri par défaut
        $query->orderBy('created_at', 'desc');

        $stocks = $query->paginate(15)->withQueryString();

        // Données pour les filtres
        $agencies = Agency::latest()->get();
        $creators = User::latest()->get();
        $users = User::latest()->get();

        return view('stock.index', compact('stocks', 'agencies', 'creators', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agencies = Agency::all();
        $users = User::latest()->get();

        return view('stock.create', compact(['agencies', 'users']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'agency_id' => 'nullable|exists:agencies,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();
        // $validated['agency_id'] = $request->agency_id ?: null;
        // dd($validated);
        Stock::create($validated);

        return redirect()->route('stocks.index')
                        ->with('success', 'Stock créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        $stock->load(['agency', 'createdBy', 'user']);

        return view('stock.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        $agencies = Agency::latest()->get();
        $users = User::latest()->get();

        return view('stock.edit', compact('stock', 'agencies', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'agency_id' => 'nullable|exists:agencies,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $stock->update($validated);

        return redirect()->route('stocks.index')
                        ->with('success', 'Stock mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')
                        ->with('success', 'Stock supprimé avec succès.');
    }
}
