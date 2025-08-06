<?php

namespace App\Http\Controllers\Api;

use App\Models\Vehicule;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicule::query();

        // Filtres
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('brand', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhere('immatriculation', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->get('brand') . '%');
        }

        if ($request->filled('year')) {
            $query->where('year', $request->get('year'));
        }
        $vehicules = $query->latest()->paginate(10)->appends($request->query());
        $data = [
            'vehicules' => $vehicules
        ];

        return sendResponse($data, 'Liste des véhicules récupérée avec succès');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'immatriculation' => 'required|string|unique:vehicules|max:20',
            'poids' => 'nullable|numeric|min:0',
            'status' => 'required|in:disponible,en_location,en_reparation',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->user()->id;
        $validated['user_id'] = auth()->user()->id;

        Vehicule::create($validated);

        return redirect()->route('vehicules.index')
                        ->with('success', 'Véhicule créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicule $vehicule)
    {
        return sendResponse($vehicule    , 'Véhicule récupéré avec succès');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicule $vehicule)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'immatriculation' => 'required|string|max:20|unique:vehicules,immatriculation,' . $vehicule->id,
            'status' => 'required|in:disponible,en_location,en_reparation',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->user()->id;

        $vehicule->update($validated);

        return redirect()->route('vehicules.index')
                        ->with('success', 'Véhicule mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicule $vehicule)
    {
        $vehicule->delete();

        return redirect()->route('vehicules.index')
                        ->with('success', 'Véhicule supprimé avec succès');
    }
}
