<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        $query = Category::with(['agency', 'createdBy', 'user']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Tri par défaut
        $query->orderBy('created_at', 'desc');

        $categories = $query->paginate(15)->withQueryString();

        // Données pour les filtres
        $agencies = Agency::latest()->get();
        $creators = User::latest()->get();

        // If request is json
        if ($request->wantsJson()) {
            return response()->json([
                'categories' => $categories,
                'agencies' => $agencies,
                'creators' => $creators,
            ]);
        }


        return view('category.index', compact('categories', 'agencies', 'creators'));
        // $categories = Category::all();

        // return view('category.index', [
        //     'categories' => $categories,
        // ]);
    }

    public function create(Request $request)
    {
        $agencies = Agency::latest()->get();
        // if request is json
        if ($request->wantsJson()) {
            return response()->json([
                'agencies' => $agencies,
            ]);
        }
        return view('category.create',compact('agencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $data = $request->all();
        $data['created_by'] = auth()->user()->id;
        $data['user_id'] = auth()->user()->id;

        $category = Category::create($data);

        // Redirect with message success message en francais
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Categorie cree avec success',
                'category' => $category,
            ]);
        }
        return redirect()->route('categories.index')->with('success', 'Categorie cree avec success');

    }

    public function show(Category $category)
    {

        $allProducts = $category->products()
            ->with(['stockProducts'])
            ->get()
            ->map(function ($product) {
                $product->total_stock = $product->stockProducts->sum('quantity');
                return $product;
            });

        // Statistiques
        $totalProducts = $allProducts->count();
        $productsInStock = $allProducts->filter(function($product) {
            return $product->total_stock > 0;
        })->count();

        $productsLowStock = $allProducts->filter(function($product) {
            return $product->total_stock > 0 && $product->total_stock <= $product->alert_quantity;
        })->count();

        $productsOutOfStock = $allProducts->filter(function($product) {
            return $product->total_stock <= 0;
        })->count();

        $products = $category->products()
            ->with(['agency', 'createdBy', 'stockProducts.stock', 'stockProducts.agency'])
            ->paginate(15)
            ->through(function ($product) {
                $product->total_stock = $product->stockProducts->sum('quantity');
                return $product;
            });

        return view('category.show', compact(
            'category',
            'products',
            'totalProducts',
            'productsInStock',
            'productsLowStock',
            'productsOutOfStock'
        ));
    }

    public function edit(Request $request, Category $category)
    {
        $agencies = Agency::latest()->get();
        // if request is json
        if ($request->wantsJson()) {
            return response()->json([
                'agencies' => $agencies,
                'category' => $category,
            ]);
        }
        return view('category.edit', [
            'category' => $category,
            'agencies' => $agencies
        ]);
    }

    public function update(Request $request, Category $category)
    {

        $request->validate([
            'name' => 'required',
        ]);


        $data = $request->all();
        $data['created_by'] = auth()->user()->id;
        $data['user_id'] = auth()->user()->id;

        $category->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Categorie mis a jour avec success',
                'category' => $category,
            ]);
        }
        return redirect()->route('categories.index')->with('success', 'Categorie mis a jour avec success');

    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Categorie supprimee avec success',
                'category' => $category,
            ]);
        }
        return redirect()->route('categories.index');
    }
}
