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
use App\Models\Stock;
use App\Models\StockProductMouvement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommandesController extends Controller
{

    public function bonEntre(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $commandes = Commandes::with(['vehicule'])
                            ->where('status', '=','approved')
                            ->latest()->paginate(10);

        return sendResponse($commandes, 'Commandes retrieved successfully', 200);
    }
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $commandes = Commandes::with(['details','vehicule'])
            ->where(function ($query) use ($search) {
                if ($search !== '') {
                    if (is_numeric($search)) {
                        $query->where('id', $search)
                            ->orWhere('poids', $search);
                    } else {
                        $query->where('matricule', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhereHas('details', function ($q) use ($search) {
                                $q->where('item_name', 'like', '%' . $search . '%');
                            });
                    }
                }
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($request->get('per_page', 10));


        return sendResponse($commandes, 'Commandes retrieved successfully', 200);
    }

     public function livraison(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $commandes = Commandes::with(['details'])
            ->where(function ($query) use ($search) {
                if ($search !== '') {
                    if (is_numeric($search)) {
                        $query->where('id', $search)
                            ->orWhere('poids', $search);
                    } else {
                        $query->where('matricule', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhereHas('details', function ($q) use ($search) {
                                $q->where('item_name', 'like', '%' . $search . '%');
                            });
                    }
                }
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($request->get('per_page', 10));


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
                'statut' => "pending",
            ]);
        }
        return sendResponse($commande, 'Commande created successfully', 201);

    }

    public function show(Request $request, Commandes $commande)
    {
        return sendResponse($commande->load(['details','vehicule']), 'Commande retrieved successfully', 200);
    }


    public function update(Request $request, $id)
    {
        $commande = Commandes::findOrFail($id);

        $commande->update([
            'vehicule_id' => $request->vehicule_id,
            'matricule' => $request->matricule,
            'commentaire' => $request->commentaire,
            'poids' => $request->poids,
            'date_livraison' => $request->date_livraison,
            'description' => $request->description,
        ]);

        CommandeDetails::where('commande_id', $commande->id)->delete();

        foreach ($request->details as $detail) {
            CommandeDetails::create([
                'commande_id' => $commande->id,
                'product_code' => $detail['product_code'],
                'item_name' => $detail['item_name'],
                'company_code' => $detail['company_code'],
                'quantity' => $detail['quantity'],
                'weight_kg' => $detail['weight_kg'] ?? 0,
                'total_weight' => ( $detail['quantity'] * $detail['weight_kg']) ?? 0,
                'pu' => $detail['pu'] ?? 0,
                'remise' => 0,
                'statut' => "pending",
            ]);
        }

        return sendResponse($commande->load('details'), 'Commande updated successfully', 200);
    }

    public function destroy(Request $request, Commande $commande)
    {
        $commande->delete();
        return sendResponse($commande, 'Commande deleted successfully', 200);
    }

    public function livraisonValide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required|exists:stocks,id',
            'commandes' => 'required|array|min:1',
            'commandes.*.id' => 'required|integer|exists:commandes,id',
            'commandes.*.matricule' => 'required|string',
            'commandes.*.poids' => 'required|numeric|min:0',
            'commandes.*.details' => 'required|array|min:1',
            'commandes.*.details.*.product_code' => 'nullable|string',
            'commandes.*.details.*.item_name' => 'nullable|string',
            'commandes.*.details.*.company_code' => 'nullable|string',
            'commandes.*.details.*.quantity' => 'required|numeric|min:0',
            'commandes.*.details.*.weight_kg' => 'nullable|numeric|min:0',
            'commandes.*.details.*.total_weight' => 'nullable|numeric|min:0',
            'commandes.*.details.*.pu' => 'required|numeric|min:0',
            'commandes.*.details.*.remise' => 'nullable|numeric|min:0',
            'commandes.*.details.*.date_livraison' => 'nullable|date',
            'commandes.*.details.*.statut' => 'nullable|string',
        ],[

        ]);

        if ($validator->fails()) {
            return sendError(
                'Données invalides '.$validator->errors()
                ,
                422,
                $validator->errors()
            );
        }

        try {
            DB::beginTransaction();

            $stockPrincipal = Stock::find($request->stock_id);
            if (!$stockPrincipal) {
                return sendError('Stock principal non trouvé (ID: 1)', 404);
            }

            $globalEntriesCount = 0;
            $globalQuantity = 0;
            $globalValue = 0;
            $errors = [];

            foreach ($request->commandes as $commandeInput) {
                $commande = Commandes::find($commandeInput['id']);

                if (!$commande) {
                    $errors[] = "Commande non trouvée: ID {$commandeInput['id']}";
                    continue;
                }
                if ($commande->status !== 'pending') {
                    $errors[] = "Commande non en attente: ID {$commandeInput['id']}";
                    continue;
                }

                if (empty($commandeInput['details'])) {
                    $errors[] = "Aucun détail pour la commande ID {$commande->id}";
                    continue;
                }

                $commande->status = 'approved';
                $commande->save();

                foreach ($commandeInput['details'] as $detailInput) {
                    try {
                        $stockProduct = StockProduct::where('stock_id', $stockPrincipal->id)
                            ->whereHas('product', function ($query) use ($detailInput) {
                                $query->where('code', $detailInput['product_code']);
                            })
                            ->first();

                        if (!$stockProduct) {
                            $errors[] = "Produit non trouvé: {$detailInput['product_code']} - {$detailInput['item_name']}";
                            continue;
                        }

                        $quantity = (float) $detailInput['quantity'];
                        $price = (float) $detailInput['pu'];

                        $stockProduct->quantity += $quantity;
                        $stockProduct->user_id = Auth::id();
                        $stockProduct->agency_id = Auth::user()->agency_id ?? $stockPrincipal->agency_id;
                        $stockProduct->purchase_price = $price;
                        $stockProduct->sale_price_ht = $price;
                        $stockProduct->sale_price_ttc = $price;
                        $stockProduct->save();

                        $this->createStockMovement($stockProduct, $quantity, $price, $stockPrincipal);

                        // Mise à jour des détails dans la table commande_details
                        CommandeDetails::where('commande_id', $commande->id)
                            ->where('product_code', $detailInput['product_code'])
                            ->update([
                                'statut' => 'Livré',
                                'date_livraison' => now(),
                            ]);

                        $globalEntriesCount++;
                        $globalQuantity += $quantity;
                        $globalValue += $quantity * $price;

                    } catch (\Exception $e) {
                        $errors[] = "Erreur sur produit {$detailInput['product_code']}: " . $e->getMessage();
                    }
                }
            }

            DB::commit();

            $response = [
                'stock_entries' => [
                    'entries_count' => $globalEntriesCount,
                    'total_quantity' => $globalQuantity,
                    'total_value' => $globalValue,
                    'stock_id' => 1
                ]
            ];

            $message = "Livraison validée avec succès. {$globalEntriesCount} produit(s) ajouté(s).";
           if (!empty($errors)) {
                $response['warnings'] = $errors;
                $message .= " " . count($errors) . " erreur(s) détectée(s). [" . implode(", ", $errors) . "]";
                return sendError($message, 500, ['error' => $errors]);
            }


            return sendResponse($response, $message, 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return sendError(
                'Erreur lors de la validation de la livraison'.$e->getMessage(),
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
