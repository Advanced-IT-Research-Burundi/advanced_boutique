<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StockTransferStoreRequest;
use App\Http\Requests\StockTransferUpdateRequest;
use App\Models\StockTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    public function index(Request $request): View
    {
        $stockTransfers = StockTransfer::all();

        return view('stockTransfer.index', [
            'stockTransfers' => $stockTransfers,
        ]);
    }

    public function create(Request $request): View
    {
        return view('stockTransfer.create');
    }

    public function store(StockTransferStoreRequest $request): RedirectResponse
    {
        $stockTransfer = StockTransfer::create($request->validated());

        $request->session()->flash('stockTransfer.id', $stockTransfer->id);

        return redirect()->route('stockTransfers.index');
    }

    public function show(Request $request, StockTransfer $stockTransfer): View
    {
        return view('stockTransfer.show', [
            'stockTransfer' => $stockTransfer,
        ]);
    }

    public function edit(Request $request, StockTransfer $stockTransfer): View
    {
        return view('stockTransfer.edit', [
            'stockTransfer' => $stockTransfer,
        ]);
    }

    public function update(StockTransferUpdateRequest $request, StockTransfer $stockTransfer): RedirectResponse
    {
        $stockTransfer->update($request->validated());

        $request->session()->flash('stockTransfer.id', $stockTransfer->id);

        return redirect()->route('stockTransfers.index');
    }

    public function destroy(Request $request, StockTransfer $stockTransfer): RedirectResponse
    {
        $stockTransfer->delete();

        return redirect()->route('stockTransfers.index');
    }
}
