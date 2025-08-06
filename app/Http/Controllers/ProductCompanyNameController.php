<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCompanyNameStoreRequest;
use App\Http\Requests\ProductCompanyNameUpdateRequest;
use App\Http\Resources\ProductCompanyNameCollection;
use App\Http\Resources\ProductCompanyNameResource;
use App\Models\ProductCompanyName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductCompanyNameController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        if ($searchTerm) {
            $productCompanyNames = ProductCompanyName::
            
                   
            where(
                function ($query) use ($searchTerm) {
                    $query->where('product_code', 'like', '%' . $searchTerm . '%')
                        ->orWhere('item_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('company_code', 'like', '%' . $searchTerm . '%');
                }
            )
            ->whereNotNull('product_code')
            ->latest()
            ->paginate(5);
        } else {
            $productCompanyNames = ProductCompanyName::whereNotNull('product_code')->latest()->paginate();
        }
        

        return sendResponse(  $productCompanyNames , 'Product Company Names retrieved successfully', 200);
    }

    public function importCompanyProducts(Request $request)
    {
        // Logic for importing company products
        // This method should handle the import logic and return a response
        $data = $request->all(); // Assuming the request contains the necessary data for import
        // remove first line if not needed
        $data = array_slice($data, 1); // Remove the first line if it's a header
        // insert on thos collumn 
            // data with key values
        $data = array_map(function ($item) {
            return [
                'product_code' => $item[0],
                'company_code' => $item[1],
                'item_name' => $item[2],
                'size' => $item[3],
                'packing_details' => $item[4],
                'mfg_location' => $item[5],
                'weight_kg' => $item[6],
                'order_qty' => $item[7],
                'total_weight' => $item[8],
                'pu' => $item[9],
                'total_weight_pu' => $item[10],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $data);

        // divise le tableau en morceaux de 500 lignes
        if (empty($data)) {
            return response()->json(['error' => 'No data to import'], 400);
        }
        $errors = [];
        
        foreach ($data as $item) {
            try {
                // Insert each item into the database
                ProductCompanyName::create($item);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle database errors, such as duplicate entries
                $errors[] = [
                    'item' => $item["product_code"] . ' - ' . $item["item_name"],
                    'error' =>"Error d'importation",
                ];
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 200);
        }
       
        return sendResponse(  $data , 'Import successful', 200);
    }

    public function store(ProductCompanyNameStoreRequest $request)
    {
        $productCompanyName = ProductCompanyName::create($request->validated());

        return new ProductCompanyNameResource($productCompanyName);
    }

    public function show(Request $request, ProductCompanyName $productCompanyName)
    {
        return new ProductCompanyNameResource($productCompanyName);
    }

    public function update(ProductCompanyNameUpdateRequest $request, ProductCompanyName $productCompanyName)
    {
        $productCompanyName->update($request->validated());

        return new ProductCompanyNameResource($productCompanyName);
    }

    public function destroy(Request $request, ProductCompanyName $productCompanyName)
    {
        $productCompanyName->delete();

        return response()->noContent();
    }
}
