<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use App\Models\Agency;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'agency']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }
        $products = $query->paginate(10);
        $categories = Category::whereIn('id', Product::select('category_id')->distinct()->pluck('category_id'))->get();
        $agencies = Agency::whereIn('id', Product::select('agency_id')->distinct()->pluck('agency_id'))->get();
        $data = [
            'products' => $products,
            'categories' => $categories,
            'agencies' => $agencies
        ];

        return sendResponse($data, 'Produits récupérés avec succès');
    }

    public function show($id)
    {
        $product = Product::with(['category'])->findOrFail($id);
        return sendResponse($product, 'Product retrieved successfully', 200);
    }


    /**
    * Store a newly created resource in storage.
    */
   public function store(Request $request)
    {
        $imagePath = null;

        try {
            $validated = $request->validate([
                'code' => 'required|string|max:100|unique:products,code',
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'nullable|string',
                'purchase_price' => 'required|numeric|min:0',
                'sale_price_ht' => 'nullable|numeric|min:0',
                'sale_price_ttc' => 'required|numeric|min:0',
                'unit' => 'required|string|max:50',
                'alert_quantity' => 'required|numeric|min:0',
                // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product = new Product();
            $product->code = $validated['code'];
            $product->name = $validated['name'];
            $product->category_id = $validated['category_id'];
            $product->description = $validated['description'] ?? null;
            $product->purchase_price = $validated['purchase_price'];
            $product->sale_price_ht = $validated['sale_price_ht'] ?? null;
            $product->sale_price_ttc = $validated['sale_price_ttc'];
            $product->unit = $validated['unit'];
            $product->alert_quantity = $validated['alert_quantity'];
            $product->image = $imagePath;
            $product->created_by = Auth::id();
            $product->save();

            DB::commit();

            return sendResponse([
                'product' => $product
            ], 'Produit créé avec succès', 201);

        } catch (\Exception $e) {
            DB::rollback();

            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return sendError('Une erreur est survenue lors de la création du produit. ' . $e->getMessage(), 500);
        }
    }



    public function update(Request $request, Product $product)
    {


        DB::beginTransaction();

        try {
            $validated = $request->validate([
            'code' => 'required|string|max:100|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price_ht' => 'nullable|numeric|min:0',
            'sale_price_ttc' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'alert_quantity' => 'required|numeric|min:0',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
                'purchase_price' => $validated['purchase_price'],
                'sale_price_ht' => $validated['sale_price_ht'] ?? null,
                'sale_price_ttc' => $validated['sale_price_ttc'],
                'unit' => $validated['unit'],
                'alert_quantity' => $validated['alert_quantity'],
                'image' => $imagePath,
            ]);

            // $product->stocks()->wherePivot('agency_id', Auth::user()->agency_id)->detach();
            // $product->stocks()->attach($validated['stock_id'], [
            //     'quantity' => 0,
            //     'agency_id' => Auth::user()->agency_id,
            // ]);

            DB::commit();

            return sendResponse([
                'product' => $product
            ], 'Produit mis à jour avec succès', 200);

        } catch (\Exception $e) {
            DB::rollback();

            return sendError('Une erreur est survenue lors de la mise à jour du produit. ' . $e->getMessage(), 400);
        }
    }


    public function destroy(Product $product)
    {
        // Supprimer l'image si elle existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return sendResponse([
            'product' => $product
        ], 'Produit supprimé avec succès', 200);
    }

    public function getProductById($id)
    {
        $product = Product::select('id', 'code', 'name', 'category_id', 'description', 'purchase_price', 'sale_price_ht', 'sale_price_ttc', 'unit', 'alert_quantity', 'image')->findOrFail($id);
        return sendResponse($product, 'Product retrieved successfully', 200);
    }
}
