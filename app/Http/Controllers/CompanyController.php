<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): Response
    {
        $companies = Company::all();

        return view('company.index', [
            'companies' => $companies,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('company.create');
    }

    public function store(CompanyStoreRequest $request): Response
    {
        $company = Company::create($request->validated());

        $request->session()->flash('company.id', $company->id);

        return redirect()->route('companies.index');
    }

    public function show(Request $request, Company $company): Response
    {
        return view('company.show', [
            'company' => $company,
        ]);
    }

    public function edit(Request $request, Company $company): Response
    {
        return view('company.edit', [
            'company' => $company,
        ]);
    }

    public function update(CompanyUpdateRequest $request, Company $company): Response
    {
        $company->update($request->validated());

        $request->session()->flash('company.id', $company->id);

        return redirect()->route('companies.index');
    }

    public function destroy(Request $request, Company $company): Response
    {
        $company->delete();

        return redirect()->route('companies.index');
    }
}
