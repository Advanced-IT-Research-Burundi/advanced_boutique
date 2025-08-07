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
    public function index(Request $request)
    {
        $depensesImportations = DepensesImportation::latest()->paginate($request->get('per_page', 15));
        return sendResponse(
            $depensesImportations,
            "Depenses Importations retrieved successfully."
        );
    }

    public function importationCommandes($id){
        $depenses = DepensesImportation::where('commande_id', $id)->get();
        return sendResponse(
            $depenses,
            "Commandes for the importation retrieved successfully."
        );
    }

    public function store(DepensesImportationStoreRequest $request)
    {
        $depensesImportation = DepensesImportation::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        )   );

        return sendResponse($depensesImportation , "Depenses Importation created successfully.");
    }

    public function show(Request $request, DepensesImportation $depensesImportation)
    {
        return new DepensesImportationResource($depensesImportation);
    }

    public function update(DepensesImportationUpdateRequest $request, DepensesImportation $depensesImportation)
    {
        $depensesImportation->update($request->validated());

        return new DepensesImportationResource($depensesImportation);
    }

    public function destroy(Request $request, DepensesImportation $depensesImportation)
    {
        $depensesImportation->delete();

        return response()->noContent();
    }
}
