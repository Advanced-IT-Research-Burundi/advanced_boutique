<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommandeStoreRequest;
use App\Http\Requests\CommandeUpdateRequest;
use App\Http\Resources\CommandeCollection;
use App\Http\Resources\CommandeResource;
use App\Models\CommandeDetails;
use App\Models\Commandes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommandesController extends Controller
{
    public function index(Request $request)
    {
        $commandes = Commandes::latest()->paginate(10);

        return sendResponse($commandes, 'Commandes retrieved successfully', 200);
    }

    public function store( Request $request)
    {

        $commande = Commandes::create([
            'vehicule_id' => $request->vehicule_id,
            'matricule' => $request->matricule,
            'commentaire' => $request->commentaire,
            'poids' => $request->poids,
            'date_livraison' => $request->date_livraison,
            'description' => $request->description,
        ]);

        // Assuming you have a CommandeResource to format the response
        foreach ($request->products as $detail) {
            CommandeDetails::create([
                'commande_id' => $commande->id,
                'product_code' => $detail['product_code'],
                'item_name' => $detail['item_name'],
                'company_code' => $detail['company_code'],
                'quantity' => $detail['quantity'],
                'weight_kg' => $detail['weight_kg'] ?? 0,
                'total_weight' => $detail['total_weight'] ?? 0,
                'pu' => $detail['pu'] ?? 0,
                'remise' => 0,
                'statut' => "En attente",
            ]);
        }
        return $request->all();

       // return new CommandeResource($commande);
    }

    public function show(Request $request, Commandes $commande)
    {
        return sendResponse($commande->load('details'), 'Commande retrieved successfully', 200);
    }

    public function update(CommandeUpdateRequest $request, Commande $commande)
    {
        $commande->update($request->validated());

        return new CommandeResource($commande);
    }

    public function destroy(Request $request, Commande $commande)
    {
        $commande->delete();
        return response()->noContent();
    }
}
