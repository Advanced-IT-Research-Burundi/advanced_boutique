@extends('layouts.app')

@section('title', 'Modifier la Dépense')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Modifier la Dépense</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('expenses.update', $expense) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="expense_date" class="form-label">Date</label>
                            <input type="datetime-local" name="expense_date" class="form-control" value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="expense_type_id" class="form-label">Type de dépense</label>
                            <select name="expense_type_id" class="form-select" required>
                                <option value="">Sélectionner</option>
                                @foreach($expenseTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('expense_type_id', $expense->expense_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant</label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock_id" class="form-label">Stock</label>
                            <select name="stock_id" class="form-select" required>
                                <option value="">Sélectionner</option>
                                @foreach($stocks as $stock)
                                    <option value="{{ $stock->id }}" {{ old('stock_id', $expense->stock_id) == $stock->id ? 'selected' : '' }}>
                                        {{ $stock->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Utilisateur</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Sélectionner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $expense->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select name="agency_id" class="form-select">
                                <option value="">Sélectionner</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ old('agency_id', $expense->agency_id) == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" required>{{ old('description', $expense->description) }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
