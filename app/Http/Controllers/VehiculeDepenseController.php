<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehiculeDepenseStoreRequest;
use App\Http\Requests\VehiculeDepenseUpdateRequest;
use App\Http\Resources\VehiculeDepenseCollection;
use App\Http\Resources\VehiculeDepenseResource;
use App\Models\VehiculeDepense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VehiculeDepenseController extends Controller
{
    public function index(Request $request)
    {
        try {

            $vehicule_id = $request->input('vehicule_id');
            $vehiculeDepenses = VehiculeDepense::where('vehicule_id', $vehicule_id)
                        ->latest()
                        ->paginate($request->get('per_page', 10));

            return sendResponse($vehiculeDepenses, 'Vehicule depenses retrieved successfully', 200);

        } catch (\Throwable $th) {
            return sendError('Error loading vehicule depenses',500,['error' => $th->getMessage()]);
        }

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->user()->id;

        $vehiculeDepense = VehiculeDepense::create($validated);

        return sendResponse($vehiculeDepense, 'Vehicule depense created successfully', 201);
    }

    public function show(VehiculeDepense $vehiculeDepense)
    {
        return sendResponse($vehiculeDepense, 'Vehicule depense retrieved successfully', 200);
    }

    public function update(VehiculeDepenseUpdateRequest $request, VehiculeDepense $vehiculeDepense): Response
    {
        $vehiculeDepense->update($request->validated());

        return new VehiculeDepenseResource($vehiculeDepense);
    }

    public function destroy(Request $request, VehiculeDepense $vehiculeDepense): Response
    {
        $vehiculeDepense->delete();

        return response()->noContent();
    }
}
