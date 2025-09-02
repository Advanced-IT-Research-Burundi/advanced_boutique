<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    /**
     * Display a listing of units with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Unit::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('abbreviation', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Get paginated results
        $units = $query->orderBy('name')
                      ->paginate($request->get('per_page', 15));


        return sendResponse($units,'Unités de mesure récupérées avec succès',200);


    }

    /**
     * Store a newly created unit
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:units,name',
                'abbreviation' => 'nullable|string|max:20|unique:units,abbreviation',
                'description' => 'nullable|string|max:1000'
            ]);

            $unit = Unit::create($validated);

            return sendResponse($unit,'Unité créée avec succès',201);

        } catch (\Throwable $e) {
            return sendError('Erreur de validation',422,$e->getMessage());
        }
    }

    /**
     * Display the specified unit
     */
    public function show(Unit $unit): JsonResponse
    {
        return sendResponse($unit,'Unité récupérée avec succès',200);
    }

    /**
     * Update the specified unit
     */
    public function update(Request $request, Unit $unit): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
                'abbreviation' => 'nullable|string|max:20|unique:units,abbreviation,' . $unit->id,
                'description' => 'nullable|string|max:1000'
            ]);

            $unit->update($validated);

            return sendResponse($unit,'Unité modifiée avec succès',200);

        } catch (\Throwable $e) {
            return sendError('Erreur de validation',422,$e->getMessage());
        }
    }

    /**
     * Remove the specified unit
     */
    public function destroy(Unit $unit): JsonResponse
    {
        try {
            $unit->delete();

            return sendResponse(null,'Unité supprimée avec succès',200);

        } catch (\Exception $e) {
            return sendError('Erreur lors de la suppression',500,$e->getMessage());
        }
    }
}
