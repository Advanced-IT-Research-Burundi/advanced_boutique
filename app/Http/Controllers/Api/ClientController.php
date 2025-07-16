<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['agency', 'createdBy']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('societe', 'like', "%{$search}%")
                  ->orWhere('nif', 'like', "%{$search}%");
            });
        }

        // Filtre par type de patient
        if ($request->filled('patient_type')) {
            $query->where('patient_type', $request->patient_type);
        }

        // Filtre par agence
        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        // Filtre par créateur
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $perPage = $request->get('perPage', 10);
        $clients = $query->latest()->paginate($perPage);
        $agencies = Agency::all();
        $creators = User::all();

        return sendResponse($clients, 'Clients récupérés avec succès');
    }

    public function create()
    {
        $agencies = Agency::all();
        return view('client.create', compact('agencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_type' => 'required|in:physique,morale',
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'nif' => 'nullable|string|max:255',
            'societe' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['user_id'] = Auth::id();
        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['agency_id'] = auth()->user()->agency_id ?? 1;

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client)
    {
        $client->load(['agency', 'createdBy']);

        return sendResponse($client, 'Client rencontré avec succès');
    }

    public function edit(Client $client)
    {
        $agencies = Agency::all();
        return view('client.edit', compact('client', 'agencies'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'patient_type' => 'required|in:physique,morale',
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'nif' => 'nullable|string|max:255',
            'societe' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
        ]);
        $validated['agency_id'] = auth()->user()->agency_id ?? 1;

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
