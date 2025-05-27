<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashRegisterStoreRequest;
use App\Http\Requests\CashRegisterUpdateRequest;
use App\Models\CashRegister;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function index(Request $request): View
    {
        $cashRegisters = CashRegister::all();

        return view('cashRegister.index', [
            'cashRegisters' => $cashRegisters,
        ]);
    }

    public function create(Request $request): View
    {
        return view('cashRegister.create');
    }

    public function store(CashRegisterStoreRequest $request): RedirectResponse
    {
        $cashRegister = CashRegister::create($request->validated());

        $request->session()->flash('cashRegister.id', $cashRegister->id);

        return redirect()->route('cashRegisters.index');
    }

    public function show(Request $request, CashRegister $cashRegister): View
    {
        return view('cashRegister.show', [
            'cashRegister' => $cashRegister,
        ]);
    }

    public function edit(Request $request, CashRegister $cashRegister): View
    {
        return view('cashRegister.edit', [
            'cashRegister' => $cashRegister,
        ]);
    }

    public function update(CashRegisterUpdateRequest $request, CashRegister $cashRegister): RedirectResponse
    {
        $cashRegister->update($request->validated());

        $request->session()->flash('cashRegister.id', $cashRegister->id);

        return redirect()->route('cashRegisters.index');
    }

    public function destroy(Request $request, CashRegister $cashRegister): RedirectResponse
    {
        $cashRegister->delete();

        return redirect()->route('cashRegisters.index');
    }
}
