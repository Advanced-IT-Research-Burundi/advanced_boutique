<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stock;
use App\Models\Agency;
use App\Models\UserStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserStockController extends Controller
{
    /**
     * Afficher la page de gestion des associations utilisateur-stock
     */
    public function index(Request $request)
    {
        $query = UserStock::with(['user', 'stock', 'agency', 'createdBy']);

        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('stock_id')) {
            $query->where('stock_id', $request->stock_id);
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        $userStocks = $query->latest()->paginate(15);

        // Données pour les filtres
        $users = User::select('id', 'first_name', 'last_name')->get();
        $stocks = Stock::select('id', 'name', 'code')->get();
        $agencies = Agency::select('id', 'name')->get();

        return view('userStock.index', compact('userStocks', 'users', 'stocks', 'agencies'));
    }

    /**
     * Afficher le formulaire de création d'association
     */
    public function create()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        $stocks = Stock::select('id', 'name', 'code', 'description')->get();
        $agencies = Agency::select('id', 'name')->get();

        return view('userStock.create', compact('users', 'stocks', 'agencies'));
    }

    /**
     * Enregistrer une nouvelle association
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'stock_id' => 'required|exists:stocks,id',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);

        // Vérifier si l'association existe déjà
        $existingAssociation = UserStock::where('user_id', $request->user_id)
            ->where('stock_id', $request->stock_id)
            ->first();

        if ($existingAssociation) {
            return back()->withErrors(['error' => 'Cette association existe déjà.']);
        }

        UserStock::create([
            'user_id' => $request->user_id,
            'stock_id' => $request->stock_id,
            'agency_id' => $request->agency_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('user-stocks.index')
            ->with('success', 'Association créée avec succès.');
    }

    /**
     * Afficher les détails d'une association
     */
    public function show(UserStock $userStock)
    {
        $userStock->load(['user', 'stock', 'agency', 'createdBy']);

        return view('user-stocks.show', compact('userStock'));
    }

    /**
     * Supprimer une association (soft delete)
     */
    public function destroy(UserStock $userStock)
    {
        $userStock->delete();

        return redirect()->route('user-stocks.index')
            ->with('success', 'Association supprimée avec succès.');
    }

    /**
     * Associer plusieurs stocks à un utilisateur
     */
    public function assignMultipleStocks(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'stock_ids' => 'required|array',
            'stock_ids.*' => 'exists:stocks,id',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);

        $created = 0;
        $existing = 0;

        foreach ($request->stock_ids as $stockId) {
            $existingAssociation = UserStock::where('user_id', $request->user_id)
                ->where('stock_id', $stockId)
                ->first();

            if (!$existingAssociation) {
                UserStock::create([
                    'user_id' => $request->user_id,
                    'stock_id' => $stockId,
                    'agency_id' => $request->agency_id,
                    'created_by' => Auth::id(),
                ]);
                $created++;
            } else {
                $existing++;
            }
        }

        $message = "Associations créées: $created";
        if ($existing > 0) {
            $message .= " (Existantes ignorées: $existing)";
        }

        return back()->with('success', $message);
    }

    /**
     * Désassocier tous les stocks d'un utilisateur
     */
    public function removeAllStocks(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $count = UserStock::where('user_id', $request->user_id)->count();
        UserStock::where('user_id', $request->user_id)->delete();

        return back()->with('success', "$count associations supprimées.");
    }

    /**
     * API pour obtenir les stocks d'un utilisateur
     */
    public function getUserStocks($userId)
    {
        $userStocks = UserStock::with('stock')
            ->where('user_id', $userId)
            ->get()
            ->pluck('stock');

        return response()->json($userStocks);
    }

    /**
     * API pour obtenir les utilisateurs d'un stock
     */
    public function getStockUsers($stockId)
    {
        $stockUsers = UserStock::with('user')
            ->where('stock_id', $stockId)
            ->get()
            ->pluck('user');

        return response()->json($stockUsers);
    }
}
