@extends('layouts.app')

@section('title', 'Gestion des Dépenses')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-cash-stack"></i> Dépenses
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouvelle Dépense
            </a>
        </div>
    </nav>

    <!-- Accordion Filters -->
    <div class="accordion mb-4" id="filterAccordion">
        <div class="accordion-item shadow-sm">
            <h2 class="accordion-header" id="headingFilters">
                <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
                    <i class="bi bi-funnel me-2"></i> Filtres de recherche
                </button>
            </h2>
            <div id="collapseFilters" class="accordion-collapse collapse show" aria-labelledby="headingFilters" data-bs-parent="#filterAccordion">
                <div class="accordion-body">
                    <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                    class="form-control"
                                    id="search"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Description, montant...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="expense_type_id" class="form-label">Type de dépense</label>
                            <select class="form-select" id="expense_type_id" name="expense_type_id">
                                <option value="">Tous</option>
                                @foreach($expenseTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('expense_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select class="form-select" id="agency_id" name="agency_id">
                                <option value="">Toutes</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">Utilisateur</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Tous</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 d-flex align-items-end mt-2">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Liste des Dépenses ({{ $expenses->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Stock</th>
                            <th>Utilisateur</th>
                            <th>Agence</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y H:i') }}</td>
                                <td>{{ $expense->expenseType->name ?? '-' }}</td>
                                <td>{{ number_format($expense->amount, 2) }}</td>
                                <td>{{ $expense->stock->name ?? '-' }}</td>
                                <td>{{ $expense->user->name ?? '-' }}</td>
                                <td>{{ $expense->agency->name ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette dépense ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Aucune dépense trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
