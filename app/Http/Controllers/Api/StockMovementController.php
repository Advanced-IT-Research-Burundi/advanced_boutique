<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockProduct;
use App\Models\StockProductMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    // Helper functions from your code
    private function sendResponse($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'error' => null,
        ], $code);
    }

    private function sendError($message, $code = 400, $error = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => $error,
        ], $code);
    }

    /**
     * Get stock product details
     */
    public function getStockProduct($id)
    {
        try {
            $stockProduct = StockProduct::with(['product', 'stock', 'category'])
                ->findOrFail($id);

            return $this->sendResponse([
                'stock_product' => $stockProduct
            ], 'Stock product retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Stock product not found', 404);
        }
    }

    /**
     * Get movements for a stock product
     */
    public function getMovements(Request $request)
    {
        try {
            $stockProductId = $request->get('stock_product_id');
            $perPage = $request->get('per_page', 10);

            $movements = StockProductMouvement::with(['user'])
                ->where('stock_product_id', $stockProductId)
                ->orderBy('item_movement_date', 'desc')
                ->paginate($perPage);

            return $this->sendResponse([
                'movements' => $movements
            ], 'Movements retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error loading movements');
        }
    }

    /**
     * Create a new stock movement
     */
    public function createMovement(Request $request)
    {
        try {
            $validated = $request->validate([
                'stock_product_id' => 'required|exists:stock_products,id',
                'item_movement_type' => 'required|in:EN,ER,EI,EAJ,ET,EAU,SN,SP,SV,SD,SC,SAJ,ST,SAU',
                'item_purchase_or_sale_price' => 'required|numeric|min:0',
                'item_purchase_or_sale_currency' => 'required|string|max:3',
                'item_quantity' => 'required|numeric|gt:0',
                'item_movement_date' => 'required|date',
                'item_movement_note' => 'nullable|string|max:1000',
            ]);

            $stockProduct = StockProduct::findOrFail($validated['stock_product_id']);

            $movement = StockProductMouvement::create([
                'agency_id' => Auth::user()->agency_id ?? 1,
                'stock_id' => $stockProduct->stock_id,
                'stock_product_id' => $validated['stock_product_id'],
                'item_code' => $stockProduct->product->code ?? 'N/A',
                'item_designation' => $stockProduct->product_name ?? 'N/A',
                'item_quantity' => $validated['item_quantity'],
                'item_measurement_unit' => $stockProduct->measurement_unit ?? 'pcs',
                'item_purchase_or_sale_price' => $validated['item_purchase_or_sale_price'],
                'item_purchase_or_sale_currency' => $validated['item_purchase_or_sale_currency'],
                'item_movement_type' => $validated['item_movement_type'],
                'item_movement_date' => $validated['item_movement_date'],
                'item_movement_note' => $validated['item_movement_note'],
                'user_id' => Auth::id(),
            ]);

            // Update stock quantity
            $this->updateStockQuantity($stockProduct, $movement);

            return $this->sendResponse([
                'movement' => $movement->load('user')
            ], 'Stock movement recorded successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->sendError('Error saving stock movement: ' . $e->getMessage());
        }
    }

    /**
     * Update stock quantity based on movement type
     */
    private function updateStockQuantity($stockProduct, $movement)
    {
        $entryTypes = ['EN', 'ER', 'EI', 'EAJ', 'ET', 'EAU'];
        $isEntry = in_array($movement->item_movement_type, $entryTypes);

        if ($isEntry) {
            $stockProduct->quantity += $movement->item_quantity;
        } else {
            $stockProduct->quantity -= $movement->item_quantity;
            // Ensure quantity doesn't go below 0
            if ($stockProduct->quantity < 0) {
                $stockProduct->quantity = 0;
            }
        }

        $stockProduct->save();
    }

    /**
     * Get movement statistics
     */
    public function getMovementStats($stockProductId)
    {
        try {
            $totalMovements = StockProductMouvement::where('stock_product_id', $stockProductId)->count();
            $totalEntries = StockProductMouvement::where('stock_product_id', $stockProductId)
                ->whereIn('item_movement_type', ['EN', 'ER', 'EI', 'EAJ', 'ET', 'EAU'])
                ->sum('item_quantity');
            $totalExits = StockProductMouvement::where('stock_product_id', $stockProductId)
                ->whereIn('item_movement_type', ['SN', 'SP', 'SV', 'SD', 'SC', 'SAJ', 'ST', 'SAU'])
                ->sum('item_quantity');
            $lastMovement = StockProductMouvement::where('stock_product_id', $stockProductId)
                ->latest('item_movement_date')
                ->first();

            return $this->sendResponse([
                'stats' => [
                    'total_movements' => $totalMovements,
                    'total_entries' => $totalEntries,
                    'total_exits' => $totalExits,
                    'last_movement' => $lastMovement
                ]
            ], 'Movement statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error loading movement statistics');
        }
    }
}
