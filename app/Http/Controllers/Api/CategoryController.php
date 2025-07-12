<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // Recherche
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sortBy', 'name');
            $sortOrder = $request->get('sortOrder', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('perPage', 10);
            $categories = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Catégories récupérées avec succès',
                'data' => $categories,
                'error' => null,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des catégories',
                'data' => null,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'data' => null,
                    'error' => $validator->errors(),
                ], 422);
            }

            $category = Category::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'data' => $category,
                'error' => null,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie',
                'data' => null,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie récupérée avec succès',
                'data' => $category,
                'error' => null,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie introuvable',
                'data' => null,
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'data' => null,
                    'error' => $validator->errors(),
                ], 422);
            }

            $category->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès',
                'data' => $category,
                'error' => null,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la catégorie',
                'data' => null,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès',
                'data' => null,
                'error' => null,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la catégorie',
                'data' => null,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:categories,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'data' => null,
                    'error' => $validator->errors(),
                ], 422);
            }

            $deletedCount = Category::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} catégories supprimées avec succès",
                'data' => ['deleted_count' => $deletedCount],
                'error' => null,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression multiple',
                'data' => null,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->restore();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie restaurée avec succès',
                'data' => $category,
                'error' => null,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la restauration',
                'data' => null,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
