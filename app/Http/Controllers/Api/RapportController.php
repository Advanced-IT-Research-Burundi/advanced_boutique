<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepenseImportationType;
use App\Models\DepensesImportation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RapportController extends Controller
{
    //

    public function depense_annuel(){
      $start_date = request()->start_date ??  Carbon::now()->startOfYear();
      $end_date = request()->end_date ?? Carbon::now()->endOfYear();

      $depensesImportation = DepensesImportation::with('depenseImportationType')
                              ->whereBetween('created_at', [$start_date, $end_date])
                              ->get();

    $listType = DepenseImportationType::get()->map->name->toArray();

    $dataLower = array_map('strtolower', $listType);

    $formatData = $depensesImportation->map(function ($depenseImportation) use ($dataLower) {
      return array_merge(
      array_fill_keys($dataLower,0),
       
    [
      'numero' => $depenseImportation->id,
      'date' => $depenseImportation->created_at,
      'montant' => $depenseImportation->amount_currency,
      strtolower( str_replace(' ', '_', $depenseImportation->depenseImportationType->name)) => $depenseImportation->amount_currency
    ]);
    });

        return sendResponse($formatData, "Good");
    }


    public function update_database()
    {
        // This method can be used to trigger any database updates or migrations
        // For now, it just returns a success message

      
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
          \DB::table('depense_importation_types')->truncate();
        \DB::statement('TRUNCATE TABLE depense_importation_types');

          \DB::table('depense_importation_types')->insert([
            ['name' => 'TRANSPORT', 'description' => 'Frais liés au transport des marchandises'],
            ['name' => 'DEDOUANEMENT', 'description' => 'Frais de dédouanement'],
            ['name' => 'LICENCE', 'description' => 'Droits de douane et licences'],
            ['name' => 'ASSURANCE', 'description' => 'Frais d\'assurances'],
            ['name' => 'IMPREVU', 'description' => 'Frais imprevus'],
            ['name' => 'BBN', 'description' => 'Frais liés au bon de livraison'],
            ['name' => 'DECHARGEMENT', 'description' => 'Frais de déchargement'],
            ['name' => 'PALETTES', 'description' => 'frais pour les palettes'],
            ['name' => 'FOURNISSEUR', 'description' => 'frais pour les fournisseurs'],
        ]);
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return sendResponse([], "Database updated successfully");
    }
}
