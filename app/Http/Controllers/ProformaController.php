<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProformaStoreRequest;
use App\Http\Requests\ProformaUpdateRequest;
use App\Models\Proforma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProformaController extends Controller
{
    public function index(Request $request): Response
    {
        $proformas = Proforma::all();

        return view('proforma.index', [
            'proformas' => $proformas,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('proforma.create');
    }

    public function store(ProformaStoreRequest $request): Response
    {
        $proforma = Proforma::create($request->validated());

        $request->session()->flash('proforma.id', $proforma->id);

        return redirect()->route('proformas.index');
    }

    public function show(Request $request, Proforma $proforma): Response
    {
        return view('proforma.show', [
            'proforma' => $proforma,
        ]);
    }

    public function edit(Request $request, Proforma $proforma): Response
    {
        return view('proforma.edit', [
            'proforma' => $proforma,
        ]);
    }

    public function update(ProformaUpdateRequest $request, Proforma $proforma): Response
    {
        $proforma->update($request->validated());

        $request->session()->flash('proforma.id', $proforma->id);

        return redirect()->route('proformas.index');
    }

    public function destroy(Request $request, Proforma $proforma): Response
    {
        $proforma->delete();

        return redirect()->route('proformas.index');
    }
}
