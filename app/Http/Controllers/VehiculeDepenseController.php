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
    public function index(Request $request): Response
    {
        $vehiculeDepenses = VehiculeDepense::all();

        return new VehiculeDepenseCollection($vehiculeDepenses);
    }

    public function store(VehiculeDepenseStoreRequest $request): Response
    {
        $vehiculeDepense = VehiculeDepense::create($request->validated());

        return new VehiculeDepenseResource($vehiculeDepense);
    }

    public function show(Request $request, VehiculeDepense $vehiculeDepense): Response
    {
        return new VehiculeDepenseResource($vehiculeDepense);
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
