<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepenseImportationTypeStoreRequest;
use App\Http\Requests\DepenseImportationTypeUpdateRequest;
use App\Http\Resources\DepenseImportationTypeResource;
use App\Models\DepenseImportationType;
use Illuminate\Http\Request;

class DepenseImportationTypeController extends Controller
{
    public function index(Request $request)
    {
        $depenseImportationTypes = DepenseImportationType::all();

        return sendResponse($depenseImportationTypes,"Depense Importation Types retrieved successfully.");
    }

    public function store(DepenseImportationTypeStoreRequest $request)
    {
        $depenseImportationType = DepenseImportationType::create($request->validated());

        return new DepenseImportationTypeResource($depenseImportationType);
    }

    public function show(Request $request, DepenseImportationType $depenseImportationType)
    {
        return new DepenseImportationTypeResource($depenseImportationType);
    }

    public function update(DepenseImportationTypeUpdateRequest $request, DepenseImportationType $depenseImportationType)
    {
        $depenseImportationType->update($request->validated());

        return new DepenseImportationTypeResource($depenseImportationType);
    }

    public function destroy(Request $request, DepenseImportationType $depenseImportationType)
    {
        $depenseImportationType->delete();

        return response()->noContent();
    }
}
