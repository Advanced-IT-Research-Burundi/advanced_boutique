<?php

namespace App\Http\Controllers\Api;

use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\User;
use App\Models\Stock;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    public function index(Request $request)
    {
        $query = CashRegister::with(['user', 'stock', 'agency', 'createdBy']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('first_name', 'like', '%' . $request->search . '%')
                             ->orWhere('last_name', 'like', '%' . $request->search . '%')
                             ->orWhere('email', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('stock', function ($stockQuery) use ($request) {
                    $stockQuery->where('name', 'like', '%' . $request->search . '%');
                })
                ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        $cashRegisters = $query->orderBy('created_at', 'desc')->paginate(15);

        // Données pour les filtres
        $agencies = Agency::all();
        $users = User::all();
        $stocks = Stock::all();

        return view('cashRegister.index', compact(
            'cashRegisters',
            'agencies',
            'users',
            'stocks'
        ));
    }

    public function create()
    {
        $users = User::all();
        $stocks = Stock::all();
        $agencies = Agency::all();

        return view('cashRegister.create', compact('users', 'stocks', 'agencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'opening_balance' => 'required|numeric|min:0',
            'status' => 'required|in:open,closed,suspended',
            'opened_at' => 'required|date',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['closing_balance'] = $data['opening_balance']; // Initialement égal au solde d'ouverture

        CashRegister::create($data);

        return redirect()->route('cash-registers.index')
                        ->with('success', 'Caisse enregistreuse créée avec succès.');
    }

    public function show(CashRegister $cashRegister)
    {
        $cashRegister->load(['user', 'stock', 'agency', 'createdBy']);

        // Récupérer les transactions de cette caisse
        $transactions = CashTransaction::where('cash_register_id', $cashRegister->id)
                                     ->with(['createdBy', 'agency'])
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);

        // Calculer les totaux
        $totalIn = CashTransaction::where('cash_register_id', $cashRegister->id)
                                ->where('type', 'in')
                                ->sum('amount');

        $totalOut = CashTransaction::where('cash_register_id', $cashRegister->id)
                                 ->where('type', 'out')
                                 ->sum('amount');

        $currentBalance = $cashRegister->opening_balance + $totalIn - $totalOut;

        return view('cashRegister.show', compact(
            'cashRegister',
            'transactions',
            'totalIn',
            'totalOut',
            'currentBalance'
        ));
    }

    public function edit(CashRegister $cashRegister)
    {
        $users = User::all();
        $stocks = Stock::all();
        $agencies = Agency::all();

        return view('cashRegister.edit', compact('cashRegister', 'users', 'stocks', 'agencies'));
    }

    public function update(Request $request, CashRegister $cashRegister)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'opening_balance' => 'required|numeric|min:0',
            'closing_balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:open,closed,suspended',
            'opened_at' => 'required|date',
            'closed_at' => 'nullable|date|after:opened_at',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);

        $cashRegister->update($request->all());

        return redirect()->route('cash-registers.index')
                        ->with('success', 'Caisse enregistreuse modifiée avec succès.');
    }

    public function destroy(CashRegister $cashRegister)
    {
        // Vérifier s'il y a des transactions liées
        if ($cashRegister->transactions()->count() > 0) {
            return redirect()->route('cash-registers.index')
                           ->with('error', 'Impossible de supprimer cette caisse car elle contient des transactions.');
        }

        $cashRegister->delete();

        return redirect()->route('cash-registers.index')
                        ->with('success', 'Caisse enregistreuse supprimée avec succès.');
    }

    public function close(CashRegister $cashRegister)
    {
        if ($cashRegister->status === 'closed') {
            return redirect()->back()->with('error', 'Cette caisse est déjà fermée.');
        }

        // Calculer le solde de fermeture
        $totalIn = CashTransaction::where('cash_register_id', $cashRegister->id)
                                ->where('type', 'in')
                                ->sum('amount');

        $totalOut = CashTransaction::where('cash_register_id', $cashRegister->id)
                                 ->where('type', 'out')
                                 ->sum('amount');

        $closingBalance = $cashRegister->opening_balance + $totalIn - $totalOut;

        $cashRegister->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closing_balance' => $closingBalance
        ]);

        return redirect()->back()->with('success', 'Caisse fermée avec succès.');
    }

    public function addTransaction(Request $request, CashRegister $cashRegister)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'reference_id' => 'nullable|integer',
        ]);

        CashTransaction::create([
            'cash_register_id' => $cashRegister->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'reference_id' => $request->reference_id ?? 0,
            'agency_id' => $cashRegister->agency_id,
            'created_by' => Auth::id(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Transaction ajoutée avec succès.');
    }
}
