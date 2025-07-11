<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::query()->with('agency');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->input('agency_id'));
        }

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();
        $agencies = \App\Models\Agency::orderBy('name')->get();

        return view('supplier.index', [
            'suppliers' => $suppliers,
            'agencies' => $agencies,
        ]);
    }

    public function create(Request $request): View
    {
        $agencies = \App\Models\Agency::orderBy('name')->get();
        return view('supplier.create', compact('agencies'));
    }

    public function store(SupplierStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $supplier = Supplier::create($data);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Request $request, Supplier $supplier): View
    {
        return view('supplier.show', compact('supplier'));
    }

    public function edit(Request $request, Supplier $supplier): View
    {
        $agencies = \App\Models\Agency::orderBy('name')->get();
        return view('supplier.edit', compact('supplier', 'agencies'));
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fournisseur modifié avec succès.');
    }

    public function destroy(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
}
