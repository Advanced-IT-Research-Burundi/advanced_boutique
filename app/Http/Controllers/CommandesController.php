<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommandeResource;
use App\Models\CommandeDetails;
use App\Models\Commandes;
use Illuminate\Http\Request;
use App\Models\StockProduct;
use App\Models\Stock;
use App\Models\StockProductMouvement;
use App\Models\Product;
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
                            ->whereHas('vehicule')
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

        $commandes = Commandes::with(['details','vehicule'])
            // ->where(function ($query) use ($search) {
            //     if ($search !== '') {
            //         if (is_numeric($search)) {
            //             $query->where('id', $search)
            //                 ->orWhere('poids', $search);
            //         } else {
            //             $query->where('matricule', 'like', '%' . $search . '%')
            //                 ->orWhere('description', 'like', '%' . $search . '%')
            //                 ->orWhereHas('details', function ($q) use ($search) {
            //                     $q->where('item_name', 'like', '%' . $search . '%');
            //                 });
            //         }
            //     }
            // })
            // ->when($status !== '', function ($query) use ($status) {
            //     $query->where('status', $status);
            // })
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
            'status' => 'pending',
            'user_id' => Auth::user()->id,
            'exchange_rate' => $request->exchange_rate,
            'poids' => $request->poids,
            'currency' => $request->currency,
            'date_livraison' => $request->date_livraison,
            'description' => $request->description,
        ]);

        // Assuming you have a CommandeResource to format the response
        foreach ($request->products as $detail) {
            // Search for the product by code
            $product = Product::where('code', $detail['product_code'])->first();
            if($product){
            CommandeDetails::create([
                'commande_id' => $commande->id,
                'product_code' => $detail['product_code'],
                'item_name' => $detail['item_name'],
                'company_code' => $detail['company_code'],
                'quantity' => $detail['quantity'],
                'weight_kg' => $detail['weight_kg'] ?? 0,
                'total_weight' => $detail['quantity'] * $detail['weight_kg'] ?? 0,
                'pu' => $detail['pu'] ?? 0,
                'remise' => 0,
                'prix_vente' => $product->sale_price_ttc ?? 0,
                'prix_achat' => $detail['pu'] ?? 0,
                'statut' => "pending",
                'total_price' => $detail['quantity'] * $detail['pu'] * ( $request->exchange_rate ?? 1),
                'total_price_v' => $product->sale_price_ttc * $detail['quantity'],
            ]);
            }
        }
        return sendResponse($commande, 'Commande created successfully', 201);

    }

    public function show(Request $request, Commandes $commande)
{

    $details = $commande->details()
        ->get()
        ->map(function ($d) use ($commande) {
            return [
                ... $commande->toArray(),
                'id'      => $d->id,
                'code'      => $d->product_code,
                'libelle'   => $d->item_name,
                'pu'        => $d->pu,
                'cours'     => $commande->exchange_rate ?? 1,
                'qte'       => $d->quantity,
                'quantity'       => $d->quantity,
                'prix_vente' => $d->prix_vente,
                'weight_kg' => $d->weight_kg,
                'total_weight' => $d->total_weight,
                'company_code' => $d->company_code,
                'total_pa'  => round(($d->pu ?? 0) * ($d->quantity ?? 0) * ($commande->exchange_rate ?? 1)),
                'total_pv'  => ($d->prix_vente ?? 0) * ($d->quantity ?? 0),
            ];
        });

    $depenses = $commande->depenses()
        ->with("depenseImportationType")

        ->get()
        ->map(function ($d) {
        return [
            'id' => $d->id,
            'libelle' => $d->depenseImportationType->name ?? '-',
            // SI le montant n'est pas en BIF
            'amount' => $d->amount ?? ($d->amount_currency * ($commande->exchange_rate ?? 1)),
        ];
    });

    $commande->setRelation('details', $details);
    $commande->setRelation('depenses', $depenses);

    return sendResponse($commande, 'Commande retrieved successfully', 200);
}

    public function update(Request $request, $id)
    {
        $commande = Commandes::findOrFail($id);

        $commande->update([
            'vehicule_id' => $request->vehicule_id,
            'matricule' => $request->matricule,
            'commentaire' => $request->commentaire,
            'status' => $request->status,
            'user_id' => Auth::user()->id,
            'exchange_rate' => $request->exchange_rate,
            'poids' => $request->poids,
            'date_livraison' => $request->date_livraison,
            'description' => $request->description,
        ]);

        CommandeDetails::where('commande_id', $commande->id)->delete();

        foreach ($request->details as $detail) {
            $product = Product::where('code', $detail['product_code'])->first();
            if($product){
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
                'prix_vente' => $product->sale_price_ttc ?? 0,
                'prix_achat' => $detail['pu'] ?? 0,
                'statut' => "pending",
                'total_price' => $detail['quantity'] * $detail['pu'] * ( $request->exchange_rate ?? 1),
                'total_price_v' => $product->sale_price_ttc * $detail['quantity'],
            ]);
            }
        }

        return sendResponse($commande->load('details'), 'Commande updated successfully', 200);
    }

    public function destroy(Request $request, Commandes $commande)
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

            $allMessages = [];

            foreach ($validator->errors()->toArray() as $field => $messages) {
                foreach ($messages as $msg) {
                    $allMessages[] = $msg;
                }
            }

            $message = "Données invalides : " . implode(', ', $allMessages);

            return sendError(
                $message,
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
                if ($commande->status == 'approved') {
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
                            //  create the product in stock if not exists
                            $product = Product::where('code', $detailInput['product_code'])->first();
                            if ($product) {
                                $stockProduct = new StockProduct();
                                $stockProduct->stock_id = $stockPrincipal->id;
                                $stockProduct->product_id = $product->id;
                                $stockProduct->product_name = $product->name;
                            }else{
                                $productcompany = \App\Models\ProductCompanyName::where('product_code', $detailInput['product_code'])->where('item_name', $detailInput['item_name'])->first();
                                if ($productcompany) {
                                    // create product
                                    $product = new Product();
                                    $product->code = $productcompany->product_code;
                                    $product->name = $productcompany->item_name ?? 'Produit sans nom';
                                    $product->description = $productcompany->packing_details ?? 'Aucune description';
                                    $product->category_id = $categoryId ?? 1;
                                    $product->purchase_price = $productcompany->pu ?? 0;
                                    $product->sale_price_ht = $productcompany->pu ?? 0;
                                    $product->sale_price_ttc = $productcompany->total_weight_pu ?? 0;
                                    $product->unit = $productcompany->size ?? 'pcs';
                                    $product->alert_quantity = $productcompany->order_qty ?? 1;
                                    $product->agency_id = auth()->user()->agency_id ?? null;
                                    $product->created_by = auth()->id();
                                    $product->user_id = auth()->id();
                                    $product->save();

                                    // create stock product
                                    $stockProduct = new StockProduct();
                                    $stockProduct->stock_id = $stockPrincipal->id;
                                    $stockProduct->product_id = $product->id;
                                    $stockProduct->product_name = $product->name;
                                }else{
                                    $errors[] = "Produit non trouvé: {$detailInput['product_code']} - {$detailInput['item_name']}";
                                    continue;
                                }
                            }
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
                        DB::rollBack();
                        $errors[] = "Erreur sur produit {$detailInput['product_code']}: " . $e->getMessage();
                    }
                }
            }



            $response = [
                'stock_entries' => [
                    'entries_count' => $globalEntriesCount,
                    'total_quantity' => $globalQuantity,
                    'total_value' => $globalValue,
                    'stock_id' => 1
                ]
            ];

            if (!empty($errors)) {
                DB::rollBack();
                $response['warnings'] = $errors;
                $message = "Livraison non validée " . count($errors) . " erreur(s) détectée(s). [" . implode(", ", $errors) . "]";
                return sendError($message, 500, ['error' => $errors]);
            }

            DB::commit();

            $message = "Livraison validée avec succès. {$globalEntriesCount} produit(s) ajouté(s).";
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