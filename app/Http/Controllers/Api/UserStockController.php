<?php

namespace App\Http\Controllers\Api;

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

        // return json if api request

            return response()->json([
                'success' => true,
                'data' => [
                    'userStocks' => $userStocks->toArray(),
                    'users' => $users->toArray(),
                    'stocks' => $stocks->toArray(),
                    'agencies' => $agencies->toArray()
                ]
            ]);



    }

    /**
     * Afficher le formulaire de création d'association
     */
    public function create(Request $request)
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        $stocks = Stock::select('id', 'name', 'code', 'description')->get();
        $agencies = Agency::select('id', 'name')->get();

        // return json if api request

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users->toArray(),
                    'stocks' => $stocks->toArray(),
                    'agencies' => $agencies->toArray()
                ]
            ]);



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

        // return json if api request

            return response()->json([
                'success' => true,
                'message' => 'Association créée avec succès.'
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

        // return json if api request

            return response()->json([
                'success' => true,
                'data' => $userStock
            ]);


        return view('userStock.show', compact('userStock'));
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

        // return json if api request

            return response()->json([
                'success' => true,
                'message' => $message
            ]);


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

        // return json if api request

            return response()->json([
                'success' => true,
                'message' => "$count associations supprimées."
            ]);


        return  back()->with('success', "$count associations supprimées.");
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

        // return json if api request

            return response()->json($stockUsers);


        //return view('userStock.index', compact('stockUsers'));
    }

     /**
     * Afficher la page de gestion des stocks pour un utilisateur
     */
    public function manage(User $user)
    {
        $assignedStocks = $user->stocks()->withPivot('created_at')->get();

        $assignedStockIds = $assignedStocks->pluck('id')->toArray();
        $availableStocks = Stock::whereNotIn('id', $assignedStockIds)
            ->get();

            $data = [
                'user' => $user,
                'assignedStocks' => $assignedStocks,
                'availableStocks' => $availableStocks
            ];
            return sendResponse($data, 'Stocks de l\'utilisateur récupérés avec succès', 200);

    }

    /**
     * Assigner des stocks à un utilisateur
     */
    public function attach(Request $request, User $user)
    {
        $request->validate([
            'stock_ids' => 'required|array|min:1',
            'stock_ids.*' => 'exists:stocks,id'
        ], [
            'stock_ids.required' => 'Veuillez sélectionner au moins un stock.',
            'stock_ids.min' => 'Veuillez sélectionner au moins un stock.',
            'stock_ids.*.exists' => 'Un ou plusieurs stocks sélectionnés sont invalides.'
        ]);

        try {
            \DB::beginTransaction();

            $attachedCount = 0;
            $alreadyAssigned = [];

            foreach ($request->stock_ids as $stockId) {
                // Vérifier si le stock n'est pas déjà assigné
                $existingAssignment = UserStock::where('user_id', $user->id)
                    ->where('stock_id', $stockId)
                    ->first();

                if (!$existingAssignment) {
                    // Récupérer le stock pour obtenir l'agence
                    $stock = Stock::findOrFail($stockId);

                    UserStock::create([
                        'user_id' => $user->id,
                        'stock_id' => $stockId,
                        'agency_id' => $stock->agency_id ?? $user->agency_id,
                        'created_by' => Auth::id(),
                    ]);

                    $attachedCount++;
                } else {
                    $stock = Stock::find($stockId);
                    $alreadyAssigned[] = $stock->name;
                }
            }

            \DB::commit();

            $message = "Stock(s) assigné(s) avec succès.";
            if ($attachedCount > 0) {
                $message = "{$attachedCount} stock(s) assigné(s) avec succès.";
            }

            if (!empty($alreadyAssigned)) {
                $message .= " Attention: " . implode(', ', $alreadyAssigned) . " étai(en)t déjà assigné(s).";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);



        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation des stocks: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Désassigner un stock d'un utilisateur
     */
    public function detach(User $user, Stock $stock)
    {
        try {
            $userStock = UserStock::where('user_id', $user->id)
                ->where('stock_id', $stock->id)
                ->first();

            if (!$userStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce stock n\'est pas assigné à cet utilisateur.'
                ]);
            }

            $userStock->delete();

            return response()->json([
                'success' => true,
                'message' => "Le stock \"{$stock->name}\" a été désassigné avec succès."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la désassignation du stock: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Désassigner tous les stocks d'un utilisateur
     */
    public function detachAll(User $user)
    {
        try {
            $deletedCount = UserStock::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} stock(s) désassigné(s) avec succès."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la désassignation des stocks: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Afficher l'historique des assignations de stocks pour un utilisateur
     */
   public function history(Request $request, User $user)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');

        $query = UserStock::withTrashed()
            ->where('user_id', $user->id)
            ->with(['stock', 'agency', 'createdBy']);


        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }


        if ($status === 'active') {
            $query->whereNull('deleted_at');
        } elseif ($status === 'deleted') {
            $query->whereNotNull('deleted_at');
        }


        $data = $query->orderBy('created_at', 'desc')->paginate(15);

        $statistics = [
            'total' => UserStock::withTrashed()->where('user_id', $user->id)->count(),
            'active' => UserStock::where('user_id', $user->id)->count(),
            'deleted' => UserStock::onlyTrashed()->where('user_id', $user->id)->count(),
            'lastAction' => UserStock::withTrashed()
                ->where('user_id', $user->id)
                ->latest('created_at')
                ->value('created_at'),
        ];

        return sendResponse([
            'user' => $user,
            'data' => $data->items(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'total' => $data->total(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'statistics' => $statistics,
        ], 'Historique des assignations de stocks récupéré avec succès', 200);
    }



    /**
     * API: Obtenir les stocks d'un utilisateur en JSON
     */
    public function getUserStocks(User $user)
    {
        $stocks = $user->stocks()
            ->with('agency')
            ->withPivot('created_at')
            ->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email
            ],
            'stocks' => $stocks->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'name' => $stock->name,
                    'agency' => $stock->agency ? $stock->agency->name : null,
                    'assigned_at' => $stock->pivot->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Assigner un stock via AJAX
     */
    public function attachAjax(Request $request, User $user)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id'
        ]);

        try {
            $existingAssignment = UserStock::where('user_id', $user->id)
                ->where('stock_id', $request->stock_id)
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce stock est déjà assigné à cet utilisateur.'
                ], 400);
            }

            $stock = Stock::findOrFail($request->stock_id);

            UserStock::create([
                'user_id' => $user->id,
                'stock_id' => $request->stock_id,
                'agency_id' => $stock->agency_id ?? $user->agency_id,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock assigné avec succès.',
                'stock' => [
                    'id' => $stock->id,
                    'name' => $stock->name,
                    'agency' => $stock->agency ? $stock->agency->name : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désassigner un stock via AJAX
     */
    public function detachAjax(Request $request, User $user)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id'
        ]);

        try {
            $userStock = UserStock::where('user_id', $user->id)
                ->where('stock_id', $request->stock_id)
                ->first();

            if (!$userStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce stock n\'est pas assigné à cet utilisateur.'
                ], 400);
            }

            $userStock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock désassigné avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la désassignation: ' . $e->getMessage()
            ], 500);
        }

    }
}
