<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditTvaStoreRequest;
use App\Http\Requests\CreditTvaUpdateRequest;
use App\Http\Resources\CreditTvaCollection;
use App\Http\Resources\CreditTvaResource;
use App\Models\CreditTva;
use Illuminate\Http\Request;

class CreditTvaController extends Controller
{
    public function index(Request $request)
    {
        $creditTvas = CreditTva::all();

        return sendResponse($creditTvas, 'Credit TVAs retrieved successfully.');
    }

    public function store(CreditTvaStoreRequest $request)
    {
        $creditTva = CreditTva::create($request->validated());

        return new CreditTvaResource($creditTva);
    }

    public function show(Request $request, CreditTva $creditTva)
    {
        return new CreditTvaResource($creditTva);
    }

    public function update(CreditTvaUpdateRequest $request, CreditTva $creditTva)
    {
        $creditTva->update($request->validated());

        return new CreditTvaResource($creditTva);
    }

    public function destroy(Request $request, CreditTva $creditTva)
    {
        $creditTva->delete();

        return response()->noContent();
    }
}