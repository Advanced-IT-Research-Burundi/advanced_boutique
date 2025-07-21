<?php

namespace App\Http\Controllers\Api;

use App\Models\Stock;
use App\Models\Agency;
use App\Models\User;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{


    public function index(Request $request)
    {
        try{
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


            $agencies = Agency::whereIn('id', Stock::select('agency_id')->distinct()->pluck('agency_id'))->latest()->get();
            $creators = User::whereIn('id', Stock::select('created_by')->distinct()->pluck('created_by'))->latest()->get();
            $users    = User::whereIn('id', Stock::select('user_id')->distinct()->pluck('user_id'))->get();


            $data = [
                'stocks' => $stocks,
                'agencies' => $agencies,
                'creators' => $creators,
                'users' => $users,
            ];


            return sendResponse($data,'Stocks  récupérées avec succès');

        }catch(\Throwable $e){

            return sendError('Erreur lors de la récupération des stocks: ' ,500,$e->getMessage());

        }


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
        try {
            // $stock->load(['agency', 'user', 'createdBy']);

            $recentProducts = $stock->stockProducts()
                ->with(['product'])
                ->orderBy('created_at', 'desc')
                ->take(1)
                ->get();

            $recentProducts = $stock->stockProducts()
                ->with(['product' => function($query) {
                    $query->select('id', 'name', 'code', 'unit', 'image');
                }])
                ->latest()
                ->limit(5)
                ->get();
                //  $recentProducts = $stock->stockProducts()
                //     ->with(['product'])
                //     ->take(5)
                //     ->get();

            $proformas = $stock->proformas()
                ->select('id', 'client', 'total_amount', 'sale_date', 'invoice_type', 'note')
                ->latest()
                ->limit(10)
                ->get();
            $users = $stock->users;

            $data = [
                'stock' => $stock,
                'recent_products' => $recentProducts,
                'proformas' => $proformas,
                'users' => $users
            ];
            return sendResponse($data, 'Stock retrieved successfully', 200);
        } catch (\Throwable $e) {
            return sendError('Erreur lors de la récupération du stock: ', 500, $e->getMessage());
        }
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
        try {
            $stock = Stock::findOrFail($id);

            // Vérifications de sécurité
            if ($stock->stockProducts()->count() > 0) {
                return sendError('Impossible de supprimer un stock contenant des produits', 400);
            }

            $stock->delete();

            return sendResponse(null, 'Stock supprimé avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors de la suppression', 500, $e->getMessage());
        }
    }

    public function detachUser($stockId, $userId)
    {
        try {
            $stock = Stock::findOrFail($stockId);
            $stock->users()->detach($userId);

            return sendResponse(null, 'Utilisateur désassigné avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors de la désassignation', 500, $e->getMessage());
        }
    }
}
