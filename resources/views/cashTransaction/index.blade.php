@extends('layouts.app')

@section('title', 'Gestion des transactions de caisse')

@section('content')
<div class="px-4 container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="justify-between mb-4 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('cash-transactions.index') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left-right"></i> Transactions
                </a>
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('cash-transactions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouvelle transaction
            </a>
        </div>
    </nav>

    <!-- Stats Cards -->
    <div class="mb-4 row">
        <div class="col-md-3">
            <div class="text-white card bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Transactions</h6>
                            <h4>{{ $stats['total_count'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-ul display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-white card bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Entrées</h6>
                            <h4>{{ number_format($stats['total_in'], 0) }} Fbu</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-arrow-down-circle display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-white card bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Sorties</h6>
                            <h4>{{ number_format($stats['total_out'], 0) }} Fbu</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-arrow-up-circle display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-white card bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Aujourd'hui</h6>
                            <h4>{{ $stats['today_count'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-day display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accordion Filters -->
    <div class="mb-4 accordion" id="filterAccordion">
        <div class="shadow-sm accordion-item">
            <h2 class="accordion-header" id="headingFilters">
                <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                    <i class="bi bi-funnel me-2"></i> Filtres de recherche
                </button>
            </h2>
            <div id="collapseFilters" class="accordion-collapse collapse show" aria-labelledby="headingFilters" data-bs-parent="#filterAccordion">
                <div class="accordion-body">
                    <form method="GET" action="{{ route('cash-transactions.index') }}" class="row g-3">
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
                                    placeholder="Description, référence...">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label for="cash_register_id" class="form-label">Caisse</label>
                            <select class="form-select" id="cash_register_id" name="cash_register_id">
                                <option value="">Toutes</option>
                                @foreach($cashRegisters as $cashRegister)
                                    <option value="{{ $cashRegister->id }}"
                                            {{ request('cash_register_id') == $cashRegister->id ? 'selected' : '' }}>
                                        Caisse #{{ $cashRegister->id }} - {{ $cashRegister->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tous</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrée</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Sortie</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select class="form-select" id="agency_id" name="agency_id">
                                <option value="">Toutes</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}"
                                            {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="user_id" class="form-label">Utilisateur</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Tous</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label for="date_from" class="form-label">Date début</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date fin</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="amount_min" class="form-label">Montant min</label>
                            <input type="number" class="form-control" id="amount_min" name="amount_min" value="{{ request('amount_min') }}" placeholder="0">
                        </div>

                        <div class="col-md-2">
                            <label for="amount_max" class="form-label">Montant max</label>
                            <input type="number" class="form-control" id="amount_max" name="amount_max" value="{{ request('amount_max') }}" placeholder="1000000">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('cash-transactions.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                            <a href="" class="btn btn-outline-success">
                                <i class="bi bi-download"></i>
                            </a>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Card -->
    <div class="shadow-sm card">
        <div class="bg-white card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 card-title">
                <i class="bi bi-list-ul me-2"></i>
                Liste des transactions ({{ $transactions->total() }} résultats)
            </h5>
        </div>
        <div class="p-0 card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Caisse</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Description</th>
                                <th>Référence</th>
                                <th>Agence</th>
                                <th>Créé par</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <strong>#{{ $transaction->id }}</strong>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ $transaction->created_at->format('d/m/Y') }}
                                            <br>
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $transaction->created_at->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-cash-coin text-primary me-2"></i>
                                            <div>
                                                <strong>Caisse #{{ $transaction->cashRegister->id }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $transaction->cashRegister->user->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($transaction->type === 'in')
                                            <span class="badge bg-success">
                                                <i class="bi bi-arrow-down-circle me-1"></i>
                                                Entrée
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-arrow-up-circle me-1"></i>
                                                Sortie
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $transaction->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                            <i class="bi bi-cash me-1"></i>
                                            {{ number_format($transaction->amount, 2) }} Fbu
                                        </span>
                                    </td>
                                    <td>
                                        <div style="max-width: 200px;">
                                            {{ Str::limit($transaction->description, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($transaction->reference_id)
                                            <span class="badge bg-info">
                                                #{{ $transaction->reference_id }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->agency)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-info me-1"></i>
                                                {{ $transaction->agency->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-success me-1"></i>
                                            {{ $transaction->createdBy->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('cash-transactions.show', $transaction) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($transaction->created_at->diffInHours() <= 24)
                                                <a href="{{ route('cash-transactions.edit', $transaction) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if($transaction->created_at->diffInHours() <= 48 && !str_contains($transaction->description, '[ANNULÉE]'))
                                                <form action="{{ route('cash-transactions.cancel', $transaction) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette transaction ?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-warning"
                                                            title="Annuler">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($transaction->created_at->diffInMinutes() <= 60)
                                                <form action="{{ route('cash-transactions.destroy', $transaction) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $transactions->firstItem() }} à {{ $transactions->lastItem() }}
                                sur {{ $transactions->total() }} résultats
                            </div>
                            {{ $transactions->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="py-5 text-center">
                    <i class="bi bi-arrow-left-right display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Aucune transaction trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez une nouvelle transaction.</p>
                    <a href="{{ route('cash-transactions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer la première transaction
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
