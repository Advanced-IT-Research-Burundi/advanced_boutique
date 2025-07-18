<?php

namespace App\Http\Controllers\Api;

use App\Models\Agency;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        $query = Agency::with(['company', 'manager', 'parentAgency', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('adresse', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }

        if ($request->filled('is_main_office')) {
            $query->where('is_main_office', $request->is_main_office);
        }

        if ($request->filled('parent_agency_id')) {
            $query->where('parent_agency_id', $request->parent_agency_id);
        }

        $agencies = $query->paginate(10)->appends($request->all());

        // Données pour les filtres
        $companies = Company::latest()->get();
        $parentAgencies = Agency::whereNull('parent_agency_id')->latest()->get();
        $managers = User::whereIn('id', Agency::select('user_id')->distinct()->pluck('user_id'))->get();


        $data = [
            'agencies' => $agencies,
            'companies' => $companies,
            'parentAgencies' => $parentAgencies,
            'managers' => $managers
        ];

        return sendResponse($data, 'Produits récupérés avec succès');


    }

    public function create()
    {
        $companies = Company::latest()->get();
        $managers = User::latest()->get();
        $parentAgencies = Agency::whereNull('parent_agency_id')->latest()->get();

        return view('agency.create', compact('companies', 'managers', 'parentAgencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|string|max:255|unique:agencies,code',
            'name' => 'required|string|max:255',
            'adresse' => 'required|string|max:500',
            'manager_id' => 'nullable|exists:users,id',
            'parent_agency_id' => 'nullable|exists:agencies,id',
            'is_main_office' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['user_id'] = Auth::id();
        $validated['is_main_office'] = $request->has('is_main_office');

        Agency::create($validated);

        return redirect()->route('agencies.index')->with('success', 'Agence créée avec succès!');
    }

    public function show(Agency $agency)
    {
        $agency->load(['company', 'manager', 'parentAgency', 'createdBy', 'user']);
        return view('agency.show', compact('agency'));
    }

    public function edit(Agency $agency)
    {
        $companies = Company::latest()->get();
        $managers = User::latest()->get();
        $parentAgencies = Agency::where('id', '!=', $agency->id)
            ->whereNull('parent_agency_id')
            ->latest()
            ->get();

        return view('agency.edit', compact('agency', 'companies', 'managers', 'parentAgencies'));
    }

    public function update(Request $request, Agency $agency)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|string|max:255|unique:agencies,code,' . $agency->id,
            'name' => 'required|string|max:255',
            'adresse' => 'required|string|max:500',
            'manager_id' => 'nullable|exists:users,id',
            'parent_agency_id' => 'nullable|exists:agencies,id|not_in:' . $agency->id,
            'is_main_office' => 'boolean',
        ]);


        $validated['created_by'] = Auth::id();
        $validated['user_id'] = Auth::id();
        $validated['is_main_office'] = $request->has('is_main_office');

        $agency->update($validated);

        return redirect()->route('agencies.index')->with('success', 'Agence mise à jour avec succès!');
    }

    public function destroy(Agency $agency)
    {
        try {
            $agency->delete();
            return redirect()->route('agencies.index')->with('success', 'Agence supprimée avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette agence. Elle pourrait être utilisée ailleurs.');
        }
    }
}
