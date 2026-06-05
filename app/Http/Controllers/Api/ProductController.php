<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Models\Category;
use App\Models\Agency;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $allowedStock = User::find(Auth::id())->allowed_stocks();

         $query = Product::with(['category', 'agency','unit'])
         ->whereHas('stocks', function ($q) use( $allowedStock) {
            if(!Auth::user()->isAdmin()){
             $q->whereIN('id',  $allowedStock ?? []);
            }
         })
         ;

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
        $products = $query->latest()->paginate(20);

        $categories = Category::whereIn('id', Product::select('category_id')->distinct()->pluck('category_id'))->get();
        $agencies = Agency::whereIn('id', Product::select('agency_id')->distinct()->pluck('agency_id'))->get();
        $data = [
            'products' => new ProductCollection($products),
            'categories' => $categories,
            'agencies' => $agencies
            // pagination informations
            ,'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
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
                'prix_promotionnel' => 'sometimes|numeric|min:0',
                'tva' => 'nullable|numeric|min:0',
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
            $product->prix_promotionnel = $validated['prix_promotionnel'];
            $product->tva = $validated['tva'] ?? null;
            $product->unit = $validated['unit'];
            $product->unit_id = $validated['unit'];
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
            'prix_promotionnel' => 'sometimes|numeric|min:0',
            'tva' => 'nullable|string|max:50',
       //     'unit' => 'required|exists:units,id',
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
                'description' => $validated['description'] ?? 0,
                'purchase_price' => $validated['purchase_price'],
                'sale_price_ht' => $validated['sale_price_ht'] ?? 0,
                'sale_price_ttc' => $validated['sale_price_ttc'],
                'prix_promotionnel' => $validated['prix_promotionnel'] ?? 0,
                'unit' => $validated['unit']  ?? 0,
                'tva' => $validated['tva'] ?? 0,
                'unit_id' => $validated['unit'],
                'alert_quantity' => $validated['alert_quantity'],
                'image' => $imagePath,
            ]);

            // Update All price for this product in stocks
             $stocks = $product->stocks;

            foreach ($stocks as $stock) {
                $stock->update([
                    'purchase_price' => $validated['purchase_price'],
                    'sale_price_ht' => $validated['sale_price_ht'] ?? 0,
                    'sale_price_ttc' => $validated['sale_price_ttc'],
                    'prix_promotionnel' => $validated['prix_promotionnel'] ?? 0,
                ]);
            }

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

    public function download()
    {
        $products = Product::with(['category', 'stockProducts'])
            ->orderBy('category_id')
            ->orderBy('code')
            ->get()
            ->map(function ($product) {
                // Prendre le premier stock associé s'il existe
                $stockProduct = $product->stockProducts->first();
                return [
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category ? $product->category->name : 'N/A',
                    'stock_id' => $stockProduct ? $stockProduct->stock_id : '',
                    'quantity' => $stockProduct ? $stockProduct->quantity : 0,
                    'prix_vente_ttc' => $product->sale_price_ttc,
                    'purchase_price' => $product->purchase_price,
                    'sale_price_ht' => $product->sale_price_ht,
                    'alert_quantity' => $product->alert_quantity,
                ];
            });

        return sendResponse($products, 'Produits récupérés avec succès');
    }

    /**
     * Import products from Excel file data
     */
    public function import(Request $request)
    {
        // Validation basique - juste vérifier que products est un array
        if (!$request->has('products') || !is_array($request->input('products'))) {
            return sendError('Le champ products est requis et doit être un tableau', 422);
        }

        $products = $request->input('products');

        if (count($products) === 0) {
            return sendError('Aucun produit à importer', 422);
        }

        $imported = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($products as $index => $productData) {
                try {
                    // Normaliser les clés (lowercase, trim, remplacer espaces par underscore)
                    $normalizedData = [];
                    foreach ($productData as $key => $value) {
                        $normalizedKey = strtolower(trim(str_replace(' ', '_', $key)));
                        $normalizedData[$normalizedKey] = $value;
                    }

                    // Mapper les différentes variations de noms de colonnes
                    $code = trim($normalizedData['code'] ?? $normalizedData['product_code'] ?? $normalizedData['code_produit'] ?? '');
                    $name = trim($normalizedData['name'] ?? $normalizedData['nom'] ?? $normalizedData['product_name'] ?? $normalizedData['nom_produit'] ?? '');

                    if (empty($code) || empty($name)) {
                        $failed++;
                        $errors[] = "Ligne " . ($index + 2) . ": Code ou nom manquant";
                        continue;
                    }

                    // Chercher ou créer la catégorie si spécifiée
                    $categoryId = null;
                    $categoryName = $normalizedData['category'] ?? $normalizedData['categorie'] ?? $normalizedData['catégorie'] ?? null;
                    if (!empty($categoryName)) {
                        $category = Category::firstOrCreate(
                            ['name' => trim($categoryName)],
                            [
                                'description' => '',
                                'created_by' => auth()->user()->id ?? 1,
                                'user_id' => auth()->user()->id ?? 1,

                            ],
                        );
                        $categoryId = $category->id;
                    }

                    // Mapper les prix avec différentes variations
                    $purchasePrice = floatval($normalizedData['purchase_price'] ?? $normalizedData['prix_achat'] ?? $normalizedData['prix_d\'achat'] ?? 0);
                    $salePriceHt = floatval($normalizedData['sale_price_ht'] ?? $normalizedData['prix_vente_ht'] ?? $normalizedData['prix_ht'] ?? 0);
                    $salePriceTtc = floatval($normalizedData['sale_price_ttc'] ?? $normalizedData['prix_vente_ttc'] ?? $normalizedData['prix_ttc'] ?? $normalizedData['prix_vente'] ?? 0);
                    $tva = floatval($normalizedData['tva'] ?? $normalizedData['taxe'] ?? 0);
                    $unit = $normalizedData['unit'] ?? $normalizedData['unite'] ?? $normalizedData['unité'] ?? 'Pièce';
                    $alertQuantity = intval($normalizedData['alert_quantity'] ?? $normalizedData['seuil_alerte'] ?? $normalizedData['alerte'] ?? 0);
                    $description = $normalizedData['description'] ?? null;

                    // Mapper quantité et stock_id
                    $quantity = floatval($normalizedData['quantity'] ?? $normalizedData['quantite'] ?? $normalizedData['quantité'] ?? $normalizedData['qte'] ?? 0);
                    $stockId = intval($normalizedData['stock_id'] ?? $normalizedData['id_stock'] ?? 0);

                    // Si pas de prix HT mais prix TTC, calculer HT
                    if ($salePriceHt == 0 && $salePriceTtc > 0) {
                        $salePriceHt = $tva > 0 ? $salePriceTtc / (1 + ($tva / 100)) : $salePriceTtc;
                    }

                    // Vérifier si le produit existe déjà
                    $existingProduct = Product::where('code', $code)->first();

                    if ($existingProduct) {
                        // Mettre à jour le produit existant
                        $existingProduct->update([
                            'name' => $name,
                            'category_id' => $categoryId ?? $existingProduct->category_id,
                            'description' => $description ?? $existingProduct->description,
                            'purchase_price' => $purchasePrice > 0 ? $purchasePrice : $existingProduct->purchase_price,
                            'sale_price_ht' => $salePriceHt > 0 ? $salePriceHt : $existingProduct->sale_price_ht,
                            'sale_price_ttc' => $salePriceTtc > 0 ? $salePriceTtc : $existingProduct->sale_price_ttc,
                            'tva' => $tva,
                            'unit' => $unit,
                            'alert_quantity' => $alertQuantity,
                        ]);
                        $productId = $existingProduct->id;
                        $imported++;
                    } else {
                        // Créer un nouveau produit
                        $newProduct = Product::create([
                            'code' => $code,
                            'name' => $name,
                            'category_id' => $categoryId,
                            'description' => $description,
                            'purchase_price' => $purchasePrice,
                            'sale_price_ht' => $salePriceHt,
                            'sale_price_ttc' => $salePriceTtc,
                            'tva' => $tva,
                            'unit' => $unit,
                            'unit_id' => null,
                            'alert_quantity' => $alertQuantity,
                            'created_by' => auth()->user()->id ?? 1,
                            'user_id' => auth()->user()->id ?? 1,
                        ]);
                        $productId = $newProduct->id;
                        $imported++;
                    }

                    // Créer ou mettre à jour le StockProduct si stock_id est fourni
                    if ($stockId > 0) {
                        $stock = Stock::find($stockId);
                        if ($stock) {
                            \App\Models\StockProduct::updateOrCreate(
                                [
                                    'product_id' => $productId,
                                    'stock_id' => $stockId,
                                ],
                                [
                                    'quantity' => $quantity,
                                    'product_name' => $name,
                                    'category_id' => $categoryId,
                                    'agency_id' => $stock->agency_id ?? 1,
                                    'user_id' => auth()->user()->id ?? 1,
                                ]
                            );
                        } else {
                            $errors[] = "Ligne " . ($index + 2) . ": Stock ID $stockId introuvable";
                        }
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            return sendResponse([
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors
            ], $imported . ' produit(s) importé(s) avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return sendError('Erreur lors de l\'importation: ' . $e->getMessage(), 500);
        }
    }

}
