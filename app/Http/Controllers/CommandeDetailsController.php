<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommandeDetailStoreRequest;
use App\Http\Requests\CommandeDetailUpdateRequest;
use App\Http\Resources\CommandeDetailCollection;
use App\Http\Resources\CommandeDetailResource;
use App\Models\CommandeDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommandeDetailsController extends Controller
{
    public function index(Request $request): Response
    {
        $commandeDetails = CommandeDetail::all();

        return new CommandeDetailCollection($commandeDetails);
    }

    public function store(CommandeDetailStoreRequest $request): Response
    {
        $commandeDetail = CommandeDetail::create($request->validated());

        return new CommandeDetailResource($commandeDetail);
    }

    public function show(Request $request, CommandeDetail $commandeDetail): Response
    {
        return new CommandeDetailResource($commandeDetail);
    }

    public function update(CommandeDetailUpdateRequest $request, CommandeDetail $commandeDetail): Response
    {
        $commandeDetail->update($request->validated());

        return new CommandeDetailResource($commandeDetail);
    }

    public function destroy(Request $request, CommandeDetail $commandeDetail): Response
    {
        $commandeDetail->delete();

        return response()->noContent();
    }
}
