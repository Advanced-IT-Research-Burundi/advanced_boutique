@extends('layouts.app')

@section('title', 'Détail de la Dépense')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0"><i class="bi bi-eye me-2"></i> Détail de la Dépense</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8">{{ $expense->expenseType->name ?? '' }}</dd>

                        <dt class="col-sm-4">Montant</dt>
                        <dd class="col-sm-8">{{ number_format($expense->amount, 2) }}</dd>

                        <dt class="col-sm-4">Stock</dt>
                        <dd class="col-sm-8">{{ $expense->stock->name ?? '' }}</dd>

                        <dt class="col-sm-4">Utilisateur</dt>
                        <dd class="col-sm-8">{{ $expense->user->name ?? '' }}</dd>

                        <dt class="col-sm-4">Agence</dt>
                        <dd class="col-sm-8">{{ $expense->agency->name ?? '' }}</dd>

                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $expense->description }}</dd>
                    </dl>
                </div>
                <div class="card-footer bg-light d-flex justify-content-end">
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
