<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProduitsTmpStoreRequest;
use App\Http\Requests\ProduitsTmpUpdateRequest;
use App\Http\Resources\ProduitsTmpCollection;
use App\Http\Resources\ProduitsTmpResource;
use App\Models\ProduitsTmp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProduitsTmpController extends Controller
{
    public function index(Request $request): Response
    {
        $produitsTmps = ProduitsTmp::all();

        return new ProduitsTmpCollection($produitsTmps);
    }

    public function store(ProduitsTmpStoreRequest $request): Response
    {
        $produitsTmp = ProduitsTmp::create($request->validated());

        return new ProduitsTmpResource($produitsTmp);
    }

    public function show(Request $request, ProduitsTmp $produitsTmp): Response
    {
        return new ProduitsTmpResource($produitsTmp);
    }

    public function update(ProduitsTmpUpdateRequest $request, ProduitsTmp $produitsTmp): Response
    {
        $produitsTmp->update($request->validated());

        return new ProduitsTmpResource($produitsTmp);
    }

    public function destroy(Request $request, ProduitsTmp $produitsTmp): Response
    {
        $produitsTmp->delete();

        return response()->noContent();
    }
}
