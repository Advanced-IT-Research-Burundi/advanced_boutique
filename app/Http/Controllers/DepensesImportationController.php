<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepensesImportationStoreRequest;
use App\Http\Requests\DepensesImportationUpdateRequest;
use App\Http\Resources\DepensesImportationCollection;
use App\Http\Resources\DepensesImportationResource;
use App\Models\DepensesImportation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DepensesImportationController extends Controller
{
    public function index(Request $request): Response
    {
        $depensesImportations = DepensesImportation::all();

        return new DepensesImportationCollection($depensesImportations);
    }

    public function store(DepensesImportationStoreRequest $request): Response
    {
        $depensesImportation = DepensesImportation::create($request->validated());

        return new DepensesImportationResource($depensesImportation);
    }

    public function show(Request $request, DepensesImportation $depensesImportation): Response
    {
        return new DepensesImportationResource($depensesImportation);
    }

    public function update(DepensesImportationUpdateRequest $request, DepensesImportation $depensesImportation): Response
    {
        $depensesImportation->update($request->validated());

        return new DepensesImportationResource($depensesImportation);
    }

    public function destroy(Request $request, DepensesImportation $depensesImportation): Response
    {
        $depensesImportation->delete();

        return response()->noContent();
    }
}
