<?php

namespace App\Http\Controllers\Api;

use App\Models\CashTransaction;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CashTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashTransaction::with(['cashRegister.user', 'createdBy', 'agency'])
                                ->orderBy('created_at', 'desc');

        // Filtrage par caisse
        if ($request->filled('cash_register_id')) {
            $query->where('cash_register_id', $request->cash_register_id);
        }

        // Filtrage par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtrage par agence
        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        // Filtrage par utilisateur
        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }

        // Filtrage par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtrage par montant
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_id', 'like', "%{$search}%")
                  ->orWhereHas('cashRegister', function ($subQuery) use ($search) {
                      $subQuery->where('id', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(15);

        // Données pour les filtres
        $cashRegisters = CashRegister::with('user')->get();
        $agencies = \App\Models\Agency::all();
        $users = \App\Models\User::all();

        // Statistiques
        $allRecords = $query->get();
        $stats = [
            'total_count' => $allRecords->count(),
            'total_in' => $allRecords->where('type', 'in')->sum('amount'),
            'total_out' => $allRecords->where('type', 'out')->sum('amount'),
            'today_count' => $allRecords->where('created_at', '>=', today()->startOfDay())->count(),
        ];
        // dd($stats['total_out']);

        return sendResponse([
            'transactions' => $transactions,
            'cashRegisters' => $cashRegisters,
            'agencies' => $agencies,
            'users' => $users,
            'stats' => $stats
        ], 'Cash transactions retrieved successfully', 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $cashRegister = null;

        // Si une caisse est spécifiée
        if ($request->filled('cash_register_id')) {
            $cashRegister = CashRegister::findOrFail($request->cash_register_id);

            // Vérifier que la caisse est ouverte
            if ($cashRegister->status !== 'open') {
                return redirect()->route('cash-registers.show', $cashRegister)
                                ->with('error', 'Impossible d\'ajouter des transactions à une caisse fermée.');
            }
        }

        $cashRegisters = CashRegister::where('status', 'open')
                                   ->with('user')
                                   ->get();

        $agencies = \App\Models\Agency::all();

        return sendResponse([
            'cashRegister' => $cashRegister,
            'cashRegisters' => $cashRegisters,
            'agencies' => $agencies
        ], 'Cash transaction created successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cash_register_id' => 'required|exists:cash_registers,id',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'reference_id' => 'nullable|integer',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Vérifier que la caisse existe et est ouverte
            $cashRegister = CashRegister::findOrFail($request->cash_register_id);

            if ($cashRegister->status !== 'open') {
                return sendResponse([
                    'cashRegister' => $cashRegister,
                ], 'Cash register closed successfully', 200);
            }

            // Vérifier les limites de montant si nécessaire
            if ($request->type === 'out') {
                $currentBalance = $this->calculateCurrentBalance($cashRegister);
                if ($request->amount > $currentBalance) {
                    return sendResponse([
                        'cashRegister' => $cashRegister,
                    ], 'Cash register closed successfully', 200);
                }
            }

            // Créer la transaction
            $transaction = CashTransaction::create([
                'cash_register_id' => $request->cash_register_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'reference_id' => $request->reference_id,
                'agency_id' => $request->agency_id ?? $cashRegister->agency_id,
                'created_by' => Auth::id(),
                'user_id' => Auth::id(),
            ]);

            // Enregistrer l'activité
            // activity()
            //     ->performedOn($transaction)
            //     ->causedBy(Auth::user())
            //     ->withProperties([
            //         'cash_register_id' => $cashRegister->id,
            //         'type' => $request->type,
            //         'amount' => $request->amount,
            //     ])
            //     ->log('Transaction de caisse créée');

            DB::commit();

            $message = $request->type === 'in'
                ? 'Entrée de ' . number_format($request->amount, 2) . ' Fbu enregistrée avec succès.'
                : 'Sortie de ' . number_format($request->amount, 2) . ' Fbu enregistrée avec succès.';

            // Redirection selon la source
            if ($request->has('redirect_to_register')) {
                return sendResponse([
                    'cashRegister' => $cashRegister,
                ], 'Cash register closed successfully', 200);
            }

            return sendResponse([
                'cashRegister' => $cashRegister,
            ], 'Cash register closed successfully', 200);

            return sendResponse([
                'cashRegister' => $cashRegister,
            ], 'Cash register closed successfully', 200);

        } catch (\Exception $e) {
            DB::rollback();

            return sendResponse([
                'cashRegister' => $cashRegister,
            ], 'Cash register closed successfully', 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CashTransaction $cashTransaction)
    {
        $cashTransaction->load(['cashRegister.user', 'createdBy', 'agency']);

        // Calculer le solde au moment de la transaction
        $balanceAtTransaction = $this->calculateBalanceAtTransaction($cashTransaction);

        // Transactions précédentes et suivantes
        $previousTransaction = CashTransaction::where('cash_register_id', $cashTransaction->cash_register_id)
                                            ->where('created_at', '<', $cashTransaction->created_at)
                                            ->orderBy('created_at', 'desc')
                                            ->first();

        $nextTransaction = CashTransaction::where('cash_register_id', $cashTransaction->cash_register_id)
                                        ->where('created_at', '>', $cashTransaction->created_at)
                                        ->orderBy('created_at', 'asc')
                                        ->first();

        return sendResponse([
            'cashTransaction' => $cashTransaction,
            'balanceAtTransaction' => $balanceAtTransaction,
            'previousTransaction' => $previousTransaction,
            'nextTransaction' => $nextTransaction
        ], 'Cash transaction retrieved successfully', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashTransaction $cashTransaction)
    {
        // Vérifier que la transaction peut être modifiée
        if (!$this->canEditTransaction($cashTransaction)) {
            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }

        $cashTransaction->load(['cashRegister', 'agency']);

        $cashRegisters = CashRegister::where('status', 'open')
                                   ->with('user')
                                   ->get();

        $agencies = \App\Models\Agency::all();

        return sendResponse([
            'cashTransaction' => $cashTransaction,
            'cashRegisters' => $cashRegisters,
            'agencies' => $agencies
        ], 'Cash transaction retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashTransaction $cashTransaction)
    {
        // Vérifier que la transaction peut être modifiée
        if (!$this->canEditTransaction($cashTransaction)) {
            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'reference_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }

        try {
            DB::beginTransaction();

            $oldData = $cashTransaction->toArray();

            // Vérifier les limites de montant si c'est une sortie
            if ($request->type === 'out') {
                $currentBalance = $this->calculateCurrentBalance($cashTransaction->cashRegister, $cashTransaction);
                if ($request->amount > $currentBalance) {
                    return sendResponse([
                        'cashTransaction' => $cashTransaction,
                    ], 'Cash transaction retrieved successfully', 200);
                }
            }

            // Mettre à jour la transaction
            $cashTransaction->update([
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'reference_id' => $request->reference_id,
            ]);

            // Enregistrer l'activité
            // activity()
            //     ->performedOn($cashTransaction)
            //     ->causedBy(Auth::user())
            //     ->withProperties([
            //         'old' => $oldData,
            //         'new' => $cashTransaction->toArray(),
            //     ])
            //     ->log('Transaction de caisse modifiée');

            DB::commit();

            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);

        } catch (\Exception $e) {
            DB::rollback();

            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashTransaction $cashTransaction)
    {
        // Vérifier que la transaction peut être supprimée
        if (!$this->canDeleteTransaction($cashTransaction)) {
            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }

        try {
            DB::beginTransaction();

            $cashRegister = $cashTransaction->cashRegister;
            $transactionData = $cashTransaction->toArray();

            // Enregistrer l'activité avant suppression
            // activity()
            //     ->performedOn($cashTransaction)
            //     ->causedBy(Auth::user())
            //     ->withProperties($transactionData)
            //     ->log('Transaction de caisse supprimée');

            $cashTransaction->delete();

            DB::commit();

            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);

        } catch (\Exception $e) {
            DB::rollback();

            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }
    }

    /**
     * Annuler une transaction
     */
    public function cancel(CashTransaction $cashTransaction)
    {
        if (!$this->canCancelTransaction($cashTransaction)) {
            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }

        try {
            DB::beginTransaction();

            // Créer une transaction inverse
            $cancelTransaction = CashTransaction::create([
                'cash_register_id' => $cashTransaction->cash_register_id,
                'type' => $cashTransaction->type === 'in' ? 'out' : 'in',
                'amount' => $cashTransaction->amount,
                'description' => 'ANNULATION - ' . $cashTransaction->description,
                'reference_id' => $cashTransaction->reference_id,
                'agency_id' => $cashTransaction->agency_id,
                'created_by' => Auth::id(),
                'user_id' => Auth::id(),
            ]);

            // Marquer la transaction originale comme annulée
            $cashTransaction->update([
                'description' => '[ANNULÉE] ' . $cashTransaction->description
            ]);

            // Enregistrer l'activité
            // activity()
            //     ->performedOn($cashTransaction)
            //     ->causedBy(Auth::user())
            //     ->withProperties([
            //         'cancel_transaction_id' => $cancelTransaction->id,
            //         'original_amount' => $cashTransaction->amount,
            //     ])
                // ->log('Transaction de caisse annulée');

            DB::commit();


            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);

        } catch (\Exception $e) {
            DB::rollback();

            return sendResponse([
                'cashTransaction' => $cashTransaction,
            ], 'Cash transaction retrieved successfully', 200);
        }
    }

    /**
     * Export des transactions
     */
    public function export(Request $request)
    {
        $query = CashTransaction::with(['cashRegister.user', 'createdBy', 'agency'])
                                ->orderBy('created_at', 'desc');

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('cash_register_id')) {
            $query->where('cash_register_id', $request->cash_register_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();

        // Générer le CSV
        $filename = 'transactions_caisse_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Date',
                'Caisse',
                'Utilisateur Caisse',
                'Type',
                'Montant',
                'Description',
                'Référence',
                'Agence',
                'Créé par'
            ]);

            // Données
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->created_at->format('d/m/Y H:i:s'),
                    'Caisse #' . $transaction->cashRegister->id,
                    $transaction->cashRegister->user->name,
                    $transaction->type === 'in' ? 'Entrée' : 'Sortie',
                    number_format($transaction->amount, 2),
                    $transaction->description,
                    $transaction->reference_id ?? '',
                    $transaction->agency->name ?? '',
                    $transaction->createdBy->name ?? ''
                ]);
            }

            fclose($file);
        };

        return sendResponse([
            'transactions' => $transactions,
        ], 'Cash transaction retrieved successfully', 200);
    }

    /**
     * Calculer le solde actuel d'une caisse
     */
    private function calculateCurrentBalance(CashRegister $cashRegister, CashTransaction $excludeTransaction = null)
    {
        $query = CashTransaction::where('cash_register_id', $cashRegister->id);

        if ($excludeTransaction) {
            $query->where('id', '!=', $excludeTransaction->id);
        }

        $totalIn = $query->where('type', 'in')->sum('amount');
        $totalOut = $query->where('type', 'out')->sum('amount');

        return $cashRegister->opening_balance + $totalIn - $totalOut;
    }

    /**
     * Calculer le solde au moment d'une transaction
     */
    private function calculateBalanceAtTransaction(CashTransaction $transaction)
    {
        $totalIn = CashTransaction::where('cash_register_id', $transaction->cash_register_id)
                                 ->where('created_at', '<=', $transaction->created_at)
                                 ->where('type', 'in')
                                 ->sum('amount');

        $totalOut = CashTransaction::where('cash_register_id', $transaction->cash_register_id)
                                  ->where('created_at', '<=', $transaction->created_at)
                                  ->where('type', 'out')
                                  ->sum('amount');

        return $transaction->cashRegister->opening_balance + $totalIn - $totalOut;
    }

    /**
     * Vérifier si une transaction peut être modifiée
     */
    private function canEditTransaction(CashTransaction $transaction)
    {
        // Vérifier que la caisse est ouverte
        if ($transaction->cashRegister->status !== 'open') {
            return false;
        }

        // Vérifier que la transaction est récente (moins de 24h)
        if ($transaction->created_at->diffInHours() > 24) {
            return false;
        }

        // Vérifier les permissions
        if (!Auth::user()->can('edit', $transaction)) {
            return false;
        }

        return true;
    }

    /**
     * Vérifier si une transaction peut être supprimée
     */
    private function canDeleteTransaction(CashTransaction $transaction)
    {
        // Vérifier que la caisse est ouverte
        if ($transaction->cashRegister->status !== 'open') {
            return false;
        }

        // Vérifier que la transaction est très récente (moins de 1h)
        if ($transaction->created_at->diffInMinutes() > 60) {
            return false;
        }

        // Vérifier les permissions
        if (!Auth::user()->can('delete', $transaction)) {
            return false;
        }

        return true;
    }

    /**
     * Vérifier si une transaction peut être annulée
     */
    private function canCancelTransaction(CashTransaction $transaction)
    {
        // Vérifier que la caisse est ouverte
        if ($transaction->cashRegister->status !== 'open') {
            return false;
        }

        // Vérifier que la transaction n'est pas déjà annulée
        if (strpos($transaction->description, '[ANNULÉE]') !== false) {
            return false;
        }

        // Vérifier que la transaction est récente (moins de 48h)
        if ($transaction->created_at->diffInHours() > 48) {
            return false;
        }

        // Vérifier les permissions
        if (!Auth::user()->can('cancel', $transaction)) {
            return false;
        }

        return true;
    }
}
