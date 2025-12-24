<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoincePointerStoreRequest;
use App\Http\Requests\InvoincePointerUpdateRequest;
use App\Http\Resources\InvoincePointerCollection;
use App\Http\Resources\InvoincePointerResource;
use App\Models\InvoincePointer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoincePointerController extends Controller
{
    public function index(Request $request): InvoincePointerCollection
    {
        $invoincePointers = InvoincePointer::all();

        return new InvoincePointerCollection($invoincePointers);
    }

    public function store(InvoincePointerStoreRequest $request): InvoincePointerResource
    {
        $invoincePointer = InvoincePointer::create($request->validated());

        return new InvoincePointerResource($invoincePointer);
    }

    public function show(Request $request, InvoincePointer $invoincePointer): InvoincePointerResource
    {
        return new InvoincePointerResource($invoincePointer);
    }

    public function update(InvoincePointerUpdateRequest $request, InvoincePointer $invoincePointer): InvoincePointerResource
    {
        $invoincePointer->update($request->validated());

        return new InvoincePointerResource($invoincePointer);
    }

    public function destroy(Request $request, InvoincePointer $invoincePointer): Response
    {
        $invoincePointer->delete();

        return response()->noContent();
    }
}
