<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgencyStoreRequest;
use App\Http\Requests\AgencyUpdateRequest;
use App\Models\Agency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgencyController extends Controller
{
    public function index(Request $request): View
    {
        $agencies = Agency::all();

        return view('agency.index', [
            'agencies' => $agencies,
        ]);
    }

    public function create(Request $request): View
    {
        return view('agency.create');
    }

    public function store(AgencyStoreRequest $request): RedirectResponse
    {
        $agency = Agency::create($request->validated());

        $request->session()->flash('agency.id', $agency->id);

        return redirect()->route('agencies.index');
    }

    public function show(Request $request, Agency $agency): View
    {
        return view('agency.show', [
            'agency' => $agency,
        ]);
    }

    public function edit(Request $request, Agency $agency): View
    {
        return view('agency.edit', [
            'agency' => $agency,
        ]);
    }

    public function update(AgencyUpdateRequest $request, Agency $agency): RedirectResponse
    {
        $agency->update($request->validated());

        $request->session()->flash('agency.id', $agency->id);

        return redirect()->route('agencies.index');
    }

    public function destroy(Request $request, Agency $agency): RedirectResponse
    {
        $agency->delete();

        return redirect()->route('agencies.index');
    }
}
