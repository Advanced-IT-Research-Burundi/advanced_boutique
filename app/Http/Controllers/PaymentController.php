<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::all();

        return view('payment.index', [
            'payments' => $payments,
        ]);
    }

    public function create(Request $request): View
    {
        return view('payment.create');
    }

    public function store(PaymentStoreRequest $request): RedirectResponse
    {
        $payment = Payment::create($request->validated());

        $request->session()->flash('payment.id', $payment->id);

        return redirect()->route('payments.index');
    }

    public function show(Request $request, Payment $payment): View
    {
        return view('payment.show', [
            'payment' => $payment,
        ]);
    }

    public function edit(Request $request, Payment $payment): View
    {
        return view('payment.edit', [
            'payment' => $payment,
        ]);
    }

    public function update(PaymentUpdateRequest $request, Payment $payment): RedirectResponse
    {
        $payment->update($request->validated());

        $request->session()->flash('payment.id', $payment->id);

        return redirect()->route('payments.index');
    }

    public function destroy(Request $request, Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('payments.index');
    }
}
