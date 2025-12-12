<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepenseImportationType;
use App\Models\DepensesImportation;
use App\Models\Proforma;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RapportController extends Controller
{
    //
    public function depense_annuel()
    {
        $start_date = request()->start_date ? Carbon::parse(request()->start_date) : Carbon::now()->startOfYear();
        $end_date = request()->end_date ? Carbon::parse(request()->end_date) : Carbon::now()->endOfYear();

        $items = DepensesImportation::with('depenseImportationType')
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
        // This method can be used to trigger any database updates or migrations
        // For now, it just returns a success message

        $proformas = Proforma::whereNull('status')->get();

        $item_list = [];
        $profomas_list = [];
        foreach ($proformas as $proforma) {
            $listeProduit = [];
            foreach (json_decode($proforma->proforma_items) as $item) {
                // Assuming each item has 'product_id' and 'quantity'
                $stock = StockProduct::where('stock_id', $proforma->stock_id)
                ->where('product_id', $item->product_id)
                ->first();
                $listeProduit[] = array_merge((array) $item, ['product_id' => $stock ? $stock->id : null]);
            }
            $proforma->proforma_items = json_encode($listeProduit);
            //   $profomas_list[] = $proforma;
            $proforma->save();
        }

        // return $profomas_list;


        // \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        //   \DB::table('depense_importation_types')->truncate();
        // \DB::statement('TRUNCATE TABLE depense_importation_types');

        //   \DB::table('depense_importation_types')->insert([
        //     ['name' => 'TRANSPORT', 'description' => 'Frais liés au transport des marchandises'],
        //     ['name' => 'DEDOUANEMENT', 'description' => 'Frais de dédouanement'],
        //     ['name' => 'LICENCE', 'description' => 'Droits de douane et licences'],
        //     ['name' => 'ASSURANCE', 'description' => 'Frais d\'assurances'],
        //     ['name' => 'IMPREVU', 'description' => 'Frais imprevus'],
        //     ['name' => 'BBN', 'description' => 'Frais liés au bon de livraison'],
        //     ['name' => 'DECHARGEMENT', 'description' => 'Frais de déchargement'],
        //     ['name' => 'PALETTES', 'description' => 'frais pour les palettes'],
        //     ['name' => 'FOURNISSEUR', 'description' => 'frais pour les fournisseurs'],
        // ]);
        // \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return sendResponse([], "Database updated successfully");
    }
}
