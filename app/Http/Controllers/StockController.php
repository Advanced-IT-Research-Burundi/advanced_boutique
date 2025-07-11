<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Agency;
use App\Models\StockProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function transfer()
     {
        return view('stock.transfer');
     }

     public function entreMultiple($stock){

        return view('stock.entre_multiple', compact('stock'));
     }

    public function mouvement($stock)
    {
        $stock = StockProduct::with(['stock', 'product', 'agency', 'stockProductMouvements'])->findOrFail($stock);

      //  dd($stock->stockProductMouvements);

        return view('stock.mouvement', compact('stock'));
    }

    public function list(Stock $stock)
    {
        return view('stock.list', compact('stock'));
    }
    public function index(Request $request)
    {
        $query = Stock::where('agency_id',auth()->user()->agency_id)->with(['agency', 'createdBy', 'user']);

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
        $stocks = Stock::where('agency_id', Auth::user()->agency_id)->latest()->get();

        return view('stock.create', compact(['agencies', 'users','stocks']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'agency_id' => 'required|exists:agencies,id',
            'user_id' => 'required|exists:users,id',
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
        $stock->load(['agency', 'user', 'createdBy']);

        $recentProducts = $stock->stockProducts()
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();


        $stockProducts = $stock->stockProducts()
            ->with(['product'])
            ->get();

        return view('stock.show', compact('stock', 'recentProducts', 'stockProducts'));
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
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'agency_id' => 'required|exists:agencies,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();

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
