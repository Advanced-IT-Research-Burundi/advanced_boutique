<?php

namespace App\Http\Controllers\Api;

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

            return sendResponse($categories, 'Catégories récupérées avec succès');

        } catch (Exception $e) {
            return sendError('Erreur lors de la récupération des catégories', 500, $e->getMessage());
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
                return sendError('Données invalides', 422, $validator->errors());
            }

            $category = Category::create($request->validated());

            return sendResponse($category, 'Catégorie créée avec succès', 201);

        } catch (Exception $e) {
            return sendError('Erreur lors de la création de la catégorie', 500, $e->getMessage());
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            return sendResponse($category, 'Catégorie récupérée avec succès');

        } catch (Exception $e) {
            return sendError('Catégorie introuvable', 404, $e->getMessage());
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
                return sendError('Données invalides', 422, $validator->errors());
            }

            $category->update($request->validated());

            return sendResponse($category, 'Catégorie mise à jour avec succès');

        } catch (Exception $e) {
            return sendError('Erreur lors de la mise à jour de la catégorie', 500, $e->getMessage());
        }
    }


    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return sendResponse(null, 'Catégorie supprimée avec succès');

        } catch (Exception $e) {
            return sendError('Erreur lors de la suppression de la catégorie', 500, $e->getMessage());
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
                return sendError('Données invalides', 422, $validator->errors());
            }

            $deletedCount = Category::whereIn('id', $request->ids)->delete();

            return sendResponse(null, "{$deletedCount} catégories supprimées avec succès");

        } catch (Exception $e) {
            return sendError('Erreur lors de la suppression multiple', 500, $e->getMessage());
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->restore();

            return sendResponse(    $category, 'Catégorie restaurée avec succès');

        } catch (Exception $e) {
            return sendError('Erreur lors de la restauration', 500, $e->getMessage());
        }
    }

    public function bulkRestore(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:categories,id'
            ]);

            if ($validator->fails()) {
                return sendError('Données invalides', 422, $validator->errors());
            }

            $restoredCount = Category::onlyTrashed()->whereIn('id', $request->ids)->restore();

            return sendResponse(null, "{$restoredCount} catégories restaurées avec succès");

        } catch (Exception $e) {
            return sendError('Erreur lors de la restauration', 500, $e->getMessage());
        }
    }
}
