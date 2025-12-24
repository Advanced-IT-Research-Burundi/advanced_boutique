<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditTvaDetailStoreRequest;
use App\Http\Requests\CreditTvaDetailUpdateRequest;
use App\Http\Resources\CreditTvaDetailCollection;
use App\Http\Resources\CreditTvaDetailResource;
use App\Models\CreditTvaDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreditTvaDetailController extends Controller
{
    public function index(Request $request)
    {
        $creditTvaDetails = CreditTvaDetail::latest()->paginate();

        return sendResponse($creditTvaDetails, 'Credit TVA Details retrieved successfully.');
    }

    public function store(CreditTvaDetailStoreRequest $request)
    {
        $creditTvaDetail = CreditTvaDetail::create($request->validated());

        return new CreditTvaDetailResource($creditTvaDetail);
    }

    public function show(Request $request, CreditTvaDetail $creditTvaDetail)
    {
        return new CreditTvaDetailResource($creditTvaDetail);
    }

    public function update(CreditTvaDetailUpdateRequest $request, CreditTvaDetail $creditTvaDetail)
    {
        $creditTvaDetail->update($request->validated());

        return new CreditTvaDetailResource($creditTvaDetail);
    }

    public function destroy(Request $request, CreditTvaDetail $creditTvaDetail)
    {
        $creditTvaDetail->delete();

        return response()->noContent();
    }
}