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
}
