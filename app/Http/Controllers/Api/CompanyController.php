<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::all();

        return sendResponse([
            'companies' => $companies,
        ], 'Companies retrieved successfully', 200);
    }

    public function create(Request $request)
    {
        return sendResponse([
            'companies' => Company::all(),
        ], 'Company created successfully', 200);
    }

    public function store(CompanyStoreRequest $request)
    {
        $company = Company::create($request->validated());

        return sendResponse([
            'company' => $company,
        ], 'Company created successfully', 200);
    }

    public function show(Request $request, Company $company)
    {
        return sendResponse([
            'company' => $company,
        ], 'Company retrieved successfully', 200);
    }

    public function edit(Request $request, Company $company)
    {
        return sendResponse([
            'company' => $company,
        ], 'Company retrieved successfully', 200);
    }

    public function update(CompanyUpdateRequest $request, Company $company)
    {
        $company->update($request->validated());

        return sendResponse([
            'company' => $company,
        ], 'Company updated successfully', 200);
    }

    public function destroy(Request $request, Company $company)
    {
        $company->delete();

        return sendResponse([
            'company' => $company,
        ], 'Company deleted successfully', 200);
    }
}
