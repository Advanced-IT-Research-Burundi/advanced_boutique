<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockProductMouvement;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockMovementController extends Controller
{
    const MOVEMENT_TYPES = [
        'EN' => 'Entrée Normale', 'ER' => 'Entrée par Retour', 'EI' => 'Entrée par Inventaire',
        'EAJ' => 'Entrée par Ajustement', 'ET' => 'Entrée par Transfert', 'EAU' => 'Entrée Autre',
        'SN' => 'Sortie Normale', 'SP' => 'Sortie par Perte', 'SV' => 'Sortie par Vente',
        'SD' => 'Sortie par Destruction', 'SC' => 'Sortie par Consommation', 'SAJ' => 'Sortie par Ajustement',
        'ST' => 'Sortie par Transfert', 'SAU' => 'Sortie Autre'
    ];

    const ENTRY_TYPES = ['EN', 'ER', 'EI', 'EAJ', 'ET', 'EAU'];

    /**
     * Afficher la page de mouvement pour un produit en stock
     */
    public function show(StockProduct $stockProduct): JsonResponse
    {
        try {
            $stockProduct = StockProduct::with(['product', 'stock'])->findOrFail($stockProduct->id);

            // Récupérer l'historique des mouvements
            $movements = StockProductMouvement::with(['user:id,name'])
                ->where('stock_product_id', $stockProduct->id)
                ->orderBy('item_movement_date', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'stock_product' => [
                        'id' => $stockProduct->id,
                        'product_name' => $stockProduct->product->name,
                        'stock_name' => $stockProduct->stock->name,
                        'quantity' => $stockProduct->quantity,
                        'measurement_unit' => $stockProduct->measurement_unit ?? 'pcs'
                    ],
                    'movements' => $movements,
                    'movement_types' => self::MOVEMENT_TYPES
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Enregistrer un nouveau mouvement pour le produit
     */
    public function store(Request $request, int $stockProductId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_movement_type' => ['required', Rule::in(array_keys(self::MOVEMENT_TYPES))],
                'item_purchase_or_sale_price' => 'required|numeric|min:0',
                'item_purchase_or_sale_currency' => 'required|string|max:3',
                'item_quantity' => 'required|numeric|gt:0',
                'item_movement_date' => 'required|date',
                'item_movement_note' => 'nullable|string|max:1000',
            ]);

            $stockProduct = StockProduct::with(['product', 'stock'])->findOrFail($stockProductId);

            // Vérification quantité pour sorties
            if (!in_array($validated['item_movement_type'], self::ENTRY_TYPES)) {
                if ($stockProduct->quantity < $validated['item_quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Quantité insuffisante. Disponible: {$stockProduct->quantity}"
                    ], 400);
                }
            }

            DB::transaction(function () use ($validated, $stockProduct) {
                // Créer le mouvement
                $movement = StockProductMouvement::create([
                    'agency_id' => auth()->user()->agency_id ?? 1,
                    'stock_id' => $stockProduct->stock->id,
                    'stock_product_id' => $stockProduct->id,
                    'item_code' => $stockProduct->id,
                    'item_designation' => $stockProduct->product->name,
                    'item_quantity' => $validated['item_quantity'],
                    'item_measurement_unit' => $stockProduct->measurement_unit ?? 'pcs',
                    'item_purchase_or_sale_price' => $validated['item_purchase_or_sale_price'],
                    'item_purchase_or_sale_currency' => $validated['item_purchase_or_sale_currency'],
                    'item_movement_type' => $validated['item_movement_type'],
                    'item_movement_date' => $validated['item_movement_date'],
                    'item_movement_note' => $validated['item_movement_note'],
                    'user_id' => auth()->id(),
                ]);

                // Mettre à jour le stock
                $isEntry = in_array($movement->item_movement_type, self::ENTRY_TYPES);
                $stockProduct->quantity += $isEntry ? $movement->item_quantity : -$movement->item_quantity;
                $stockProduct->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Mouvement enregistré avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
