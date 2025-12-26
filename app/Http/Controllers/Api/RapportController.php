<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommandeDetails;
use App\Models\Commandes;
use App\Models\DepenseImportationType;
use App\Models\DepensesImportation;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RapportController extends Controller
{
    public function stock_billan()
    {
        // Get all products with their stock information

        // group by stock_id and sum quantities * sale_price_ht
        $stock_produits = StockProduct::with('stock')
        ->whereHas('stock')
        ->get()
        ->groupBy('stock_id')
        ->map(function ($items, $stock_id) {
            $total_value = 0;
            foreach ($items as $item) {
                    $total_value += $item->quantity * ($item->sale_price_ht ?? 0);
            }
            return [
                'stock_id' => $stock_id,
                'stock_name' => Stock::where('id', $stock_id)->value('name'),
                'total_value' => $total_value,
            ];
        })->values();

        return sendResponse([
            'stock_produits' => $stock_produits,
        ], 'Stock Product'); 
    }
    //
    public function depense_annuel()
    {
        $start_date = request()->start_date ? Carbon::parse(request()->start_date) : Carbon::now()->startOfYear();
        $end_date = request()->end_date ? Carbon::parse(request()->end_date) : Carbon::now()->endOfYear();

        $items = DepensesImportation::with('depenseImportationType')
                ->whereHas('commande')
                ->whereBetween('date', [$start_date, $end_date])
                ->orderBy('date')
                ->get();

        // Map DB type names to the desired columns
        // Build columns from DB types
        $columns = [];
        $labels = []; // optional, to help your UI display headers
        foreach (DepenseImportationType::pluck('name')->all() as $typeName) {
            $key = strtoupper($typeName);          // lookup key
            $val = Str::slug($typeName, '_');      // JSON field name
            $columns[$key] = $val;
            $labels[$val] = $typeName;             // label for UI
        }

        $toBif = function ($d) {
            return ($d->amount_currency && $d->amount_currency > 0)
            ? $d->amount_currency
            : $d->amount;
        };

        $grouped = $items->groupBy('commande_id');

        $rows = $grouped->map(function ($group, $commandeId) use ($columns, $toBif) {
            $first = $group->sortBy('date')->first();
            $dateObj = $first && $first->date ? new Carbon($first->date) : null;

            $commande = Commandes::find($commandeId);
            $repport = $commande ? $commande->getRepportCommande() : null;

            $row = [
                'date' => $dateObj ? $dateObj->format('d/m/Y') : null,
                'numero' => $dateObj
                ? str_pad((string)$commandeId, 5, '0', STR_PAD_LEFT) . '/' . $dateObj->format('Y')
                : (string)$commandeId,

            ];


            // Initialize all columns to 0
            foreach ($columns as $colKey) {
                $row[$colKey] = 0;
            }

            $row["fournisseur"] = $repport["fournisseur"] ?? 0;


            // Sum per type
            foreach ($group as $d) {
                $typeName = strtoupper(optional($d->depenseImportationType)->name ?? '');
                if (isset($columns[$typeName])) {
                    $row[$columns[$typeName]] += $toBif($d);
                }
            }

            return $row;
        })->values();

        // Totals row
        $totals = [
            'date' => 'Totaux',
            'numero' => null,
        ];
        foreach ($columns as $colKey) {
            $totals[$colKey] = $rows->sum($colKey);
        }

        return sendResponse([
            'rows' => $rows,
            'totals' => $totals,
        ], 'Good');
    }


    public function update_database()
    {
        // Add commandes details

        $commandesDetails = CommandeDetails::all();

        foreach ($commandesDetails as $commandesDetail) {

            $product = Product::where('code', $commandesDetail->product_code)->first();
            if (!$product) {
                continue;
            }
            $commandesDetail->prix_vente = $product->sale_price_ttc ?? 0;
            $commandesDetail->prix_achat = $commandesDetail->pu ?? 0;
            $commandesDetail->total_price = $commandesDetail->quantity * $commandesDetail->pu * ( $commandesDetail->commande->exchange_rate ?? 1);
            $commandesDetail->total_price_v = $product->sale_price_ttc * $commandesDetail->quantity;
            $commandesDetail->save();
        }
        return sendResponse([], "Database updated successfully");
    }
}