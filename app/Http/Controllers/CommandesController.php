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
use App\Models\StockProduct;
use App\Models\StockProductMouvement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommandesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $commandes = Commandes::with(['details'])
            ->where(function ($query) use ($search) {
                if ($search) {

                    if (is_numeric($search)) {
                        $query->where('id', $search)
                            ->orWhere('poids', $search)
                           ;
                    }else{
                        $query->where('matricule', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('details', function ($q) use ($search) {
                            $q->where('item_name', 'like', '%' . $search . '%');
                        });
                    }

                }
            })
            ->latest()
            ->paginate(10);


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

    public function livraisonValide(Request $request)
    {
        try {
            DB::beginTransaction();

            $commande = Commandes::find($request->commande_id);
            if (!$commande) {
                return sendError('Commande non trouvée', 404);
            }

            if (!$commande->details || $commande->details->isEmpty()) {
                return sendError('Aucun détail trouvé pour cette commande', 422);
            }

            $commande->statut = 'Valide';
            $commande->save();

            // Récupérer le stock principal (ID = 1)
            $stockPrincipal = Stock::find(1);
            if (!$stockPrincipal) {
                \DB::rollBack();
                return sendError('Stock principal non trouvé (ID: 1)', 404);
            }

            $entriesCount = 0;
            $totalQuantity = 0;
            $totalValue = 0;
            $errors = [];

            foreach ($commande->details as $detail) {
                try {
                    $stockProduct = StockProduct::where('stock_id', 1)
                        ->where(function($query) use ($detail) {
                            $query->where('product_name', $detail->item_name);
                            // $query->where('product_code', $detail->product_code);
                                // ->orWhere('company_code', $detail->company_code);
                        })
                        ->first();

                    if (!$stockProduct) {
                        $errors[] = "Produit non trouvé dans le stock: {$detail->product_code} - {$detail->item_name}";
                        continue;
                    }

                    $quantity = (float) $detail->quantity;
                    $price = (float) $detail->pu;
                    $stockProduct->quantity += $quantity;
                    $stockProduct->user_id = Auth::id();
                    $stockProduct->agency_id = Auth::user()->agency_id ?? $stockPrincipal->agency_id;
                    $stockProduct->purchase_price = $price;
                    $stockProduct->sale_price_ht = $price;
                    $stockProduct->sale_price_ttc = $price;
                    $stockProduct->save();

                    $this->createStockMovement($stockProduct, $quantity, $price, $stockPrincipal);

                    // Mettre à jour le statut du détail de commande
                    $detail->statut = 'Livré';
                    $detail->date_livraison = now();
                    $detail->save();

                    $entriesCount++;
                    $totalQuantity += $quantity;
                    $totalValue += ($quantity * $price);

                } catch (\Exception $e) {
                    $errors[] = "Erreur pour le produit {$detail->product_code}: " . $e->getMessage();
                    continue;
                }
            }

            \DB::commit();

            // Préparer la réponse
            $responseData = [
                'commande' => $commande->fresh(['details']),
                'stock_entries' => [
                    'entries_count' => $entriesCount,
                    'total_quantity' => $totalQuantity,
                    'total_value' => $totalValue,
                    'stock_id' => 1
                ]
            ];

            if (!empty($errors)) {
                $responseData['warnings'] = $errors;
            }

            $message = "Livraison validée avec succès. {$entriesCount} produit(s) ajouté(s) au stock principal.";

            if (!empty($errors)) {
                $message .= " " . count($errors) . " erreur(s) détectée(s).";
            }

            return sendResponse($responseData, $message, 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return sendError(
                'Erreur lors de la validation de la livraison',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Create stock movement record
     */
    private function createStockMovement($stockProduct, $quantity, $price, $stock)
    {
        try {
            StockProductMouvement::create([
                'agency_id' => Auth::user()->agency_id ?? $stock->agency_id,
                'stock_id' => $stock->id,
                'stock_product_id' => $stockProduct->id,
                'item_code' => $stockProduct->product->code ?? 'N/A',
                'item_designation' => $stockProduct->product->name ?? 'N/A',
                'item_quantity' => $quantity,
                'item_measurement_unit' => $stockProduct->product->unit ?? 'pcs',
                'item_purchase_or_sale_price' => $price,
                'item_purchase_or_sale_currency' => 'FBU',
                'item_movement_type' => 'EN',
                'item_movement_date' => now(),
                'item_movement_note' => 'Entrée multiple',
                'user_id' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            // Log error but don't fail the main transaction
            \Log::error('Failed to create stock movement: ' . $e->getMessage());
            return sendError('Erreur lors de la création du mouvement de stock', 500, ['error' => $e->getMessage()]);
        }
    }
}
