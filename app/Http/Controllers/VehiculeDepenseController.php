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
                $query = VehiculeDepense::where('vehicule_id', $vehicule_id);

                if ($request->filled('search')) {
                    $search = $request->input('search');
                    $query->where('description', 'LIKE', '%' . $search . '%');
                }

                if ($request->filled('currency')) {
                    $query->where('currency', $request->input('currency'));
                }

                if ($request->filled('date_from')) {
                    $query->whereDate('date_depense', '>=', $request->input('date_from'));
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('date_depense', '<=', $request->input('date_to'));
                }

                $vehiculeDepenses = $query->latest()->paginate($request->get('per_page', 10));

                return sendResponse($vehiculeDepenses, 'Vehicule depenses retrieved successfully', 200);

            } catch (\Throwable $th) {
                return sendError('Error loading vehicule depenses', 500, ['error' => $th->getMessage()]);
            }


    }

    public function store(Request $request)
    {
        try{

            $validated = $request->validate([
                'vehicule_id' => 'required|exists:vehicules,id',
                'amount' => 'required|numeric',
                'date' => 'required|date',
                'currency'=>'nullable|string',
                'exchange_rate'=>'nullable|numeric',
                'description' => 'nullable|string',
            ]);

            $validated['user_id'] = auth()->user()->id;

            $vehiculeDepense = VehiculeDepense::create($validated);

            return sendResponse($vehiculeDepense, 'Vehicule depense created successfully', 201);
         }catch (\Throwable $th) {
            return sendError('Error loading vehicule depenses'.$th->getMessage(),500,['error' => $th->getMessage()]);
        }
    }

    public function show(VehiculeDepense $vehiculeDepense)
    {
        return sendResponse($vehiculeDepense, 'Vehicule depense retrieved successfully', 200);
    }

    public function update(Request $request, $id)
    {
        try{
            $validated = $request->validate([
                'vehicule_id' => 'required|exists:vehicules,id',
                'amount' => 'required|numeric',
                'date' => 'required|date',
                'currency'=>'nullable|string',
                'exchange_rate'=>'nullable|numeric',
                'description' => 'nullable|string',
            ]);

            $vehiculeDepense = VehiculeDepense::findOrFail($id);

            $vehiculeDepense->update(array_merge(
                $validated,
                ['user_id' => auth()->user()->id]
            ));

            return sendResponse($vehiculeDepense, 'Vehicule depense updated successfully');
        }catch (\Throwable $th) {
            return sendError('Error loading vehicule depenses'.$th->getMessage(),500,['error' => $th->getMessage()]);
        }
    }


    public function destroy(Request $request, VehiculeDepense $vehiculeDepense)
    {
        try {
            $vehiculeDepense->delete();
            return sendResponse(null, 'Vehicule depense deleted successfully');
        } catch (\Throwable $th) {
            return sendError('Error loading vehicule depenses'.$th->getMessage(),500,['error' => $th->getMessage()]);
        }
    }
}
