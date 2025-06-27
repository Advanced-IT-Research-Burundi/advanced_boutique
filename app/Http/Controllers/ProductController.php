<?php

namespace App\Http\Controllers;

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
        $query = Product::with(['category', 'agency', 'createdBy', 'user']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }


        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        // Données pour les filtres
        $categories = Category::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();

        return view('product.index', compact('products', 'categories', 'agencies'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'agency', 'createdBy', 'user']);
        return view('product.show', compact('product'));
    }


    public function create(Request $request)
    {
        $categories = Category::latest()->get();
        $stocks = Stock::where('agency_id', Auth::user()->agency_id)->latest()->get();

        $selectedCategoryId = $request->query('category_id');

        return view('product.create', compact('categories', 'stocks', 'selectedCategoryId'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'=> 'required|string|max:100|unique:products,code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'alert_quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_id' => 'required|exists:stocks,id',
            // 'quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {


            // Traitement de l'image
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // Créer le produit
            $product = new Product();
            $product->code = $validated['code'];
            $product->name = $validated['name'];
            $product->category_id = $validated['category_id'];
            $product->description = $validated['description'];
            $product->purchase_price = $validated['purchase_price'];
            $product->sale_price = $validated['sale_price'];
            $product->unit = $validated['unit'];
            $product->alert_quantity = $validated['alert_quantity'];
            $product->image = $imagePath;
            $product->created_by = Auth::id();
            $product->save();

            // Créer l'entrée dans la table pivot stock_products
            $product->stocks()->attach($validated['stock_id'], [
                'quantity' => 0, // Initialiser la quantité à 0
                'agency_id' => Auth::user()->agency_id,
            ]);

            DB::commit();

            return redirect()->route('products.index')
                            ->with('success', 'Produit créé avec succès et ajouté au stock.');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();

            // Supprimer l'image si elle a été uploadée
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()->withErrors(['error' => 'Une erreur est survenue lors de la création du produit.'])
                        ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::latest()->get();
        $stocks = Stock::where('agency_id', Auth::user()->agency_id)->latest()->get();

        // Récupérer la relation stock-produit pour l'agence actuelle
        $stockProduct = $product->stocks()
                            ->wherePivot('agency_id', Auth::user()->agency_id)
                            ->first();

        $selectedStockId = $stockProduct ? $stockProduct->id : null;
        $currentQuantity = $stockProduct ? $stockProduct->pivot->quantity : 0;

        return view('product.edit', compact(
            'product',
            'categories',
            'stocks',
            'selectedStockId',
            'currentQuantity'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code'=> 'required|string|max:100|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'alert_quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_id' => 'required|exists:stocks,id',
            // 'quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Vérifier que le stock appartient à la même agence que l'utilisateur
            $stock = Stock::findOrFail($validated['stock_id']);
            if ($stock->agency_id !== Auth::user()->agency_id) {
                return back()->withErrors(['stock_id' => 'Vous ne pouvez pas modifier ce produit dans ce stock.']);
            }

            // Traitement de l'image
            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // Mettre à jour le produit
            $product->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'purchase_price' => $validated['purchase_price'],
                'sale_price' => $validated['sale_price'],
                'unit' => $validated['unit'],
                'alert_quantity' => $validated['alert_quantity'],
                'image' => $imagePath,
            ]);

            // Mettre à jour ou créer l'entrée dans la table pivot
            $product->stocks()->wherePivot('agency_id', Auth::user()->agency_id)->detach();
            $product->stocks()->attach($validated['stock_id'], [
                'quantity' => 0,
                'agency_id' => Auth::user()->agency_id,
            ]);

            DB::commit();

            return redirect()->route('products.index')
                            ->with('success', 'Produit mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour du produit.'])
                        ->withInput();
        }
    }
    public function destroy(Product $product)
    {
        // Supprimer l'image si elle existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
                        ->with('success', 'Produit supprimé avec succès.');
    }
}
