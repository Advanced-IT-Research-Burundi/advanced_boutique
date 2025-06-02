<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // if ($request->filled('created_by')) {
        //     $query->where('created_by', $request->created_by);
        // }

        // if ($request->filled('user_id')) {
        //     $query->where('user_id', $request->user_id);
        // }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        // Données pour les filtres
        $categories = Category::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();
        // $creators = User::whereIn('id', Product::distinct()->pluck('created_by'))->latest()->get();
        // $users = User::latest()->get();

        return view('product.index', compact('products', 'categories', 'agencies'));
    }

    public function create()
    {
        $categories = Category::latest()->get();
        $agencies = Agency::latest()->get();
        $users = User::latest()->get();

        return view('product.create', compact('categories', 'agencies', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:255',
            'alert_quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'agency_id' => 'nullable|exists:agencies,id',

        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['user_id'] = Auth::id();

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')
                        ->with('success', 'Produit créé avec succès.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'agency', 'createdBy', 'user']);
        return view('product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::latest()->get();
        $agencies = Agency::latest()->get();
        $users = User::latest()->get();

        return view('product.edit', compact('product', 'categories', 'agencies', 'users'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:255',
            'alert_quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'agency_id' => 'nullable|exists:agencies,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $data = $request->all();

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
                        ->with('success', 'Produit modifié avec succès.');
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
