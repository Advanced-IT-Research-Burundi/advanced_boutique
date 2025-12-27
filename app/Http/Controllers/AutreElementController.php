<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutreElementStoreRequest;
use App\Http\Requests\AutreElementUpdateRequest;
use App\Http\Resources\AutreElementCollection;
use App\Http\Resources\AutreElementResource;
use App\Models\AutreElement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AutreElementController extends Controller
{
    public function index(Request $request)
    {
        $autreElements = AutreElement::all();

        return new AutreElementCollection($autreElements);
    }

    public function store(AutreElementStoreRequest $request)
    {
        $autreElement = AutreElement::create($request->validated());

        return new AutreElementResource($autreElement);
    }

    public function show(Request $request, AutreElement $autreElement)
    {
        return new AutreElementResource($autreElement);
    }

    public function update(AutreElementUpdateRequest $request, AutreElement $autreElement)
    {
        $autreElement->update($request->validated());

        return new AutreElementResource($autreElement);
    }

    public function destroy(Request $request, AutreElement $autreElement)
    {
        $autreElement->delete();

        return response()->noContent();
    }
}
