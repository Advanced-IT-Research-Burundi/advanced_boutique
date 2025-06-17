@extends('layouts.app')

@section('title', 'Gestion des caisses')

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
            <li class="breadcrumb-item">
                <a href="{{ route('cash-registers.index') }}" class="text-decoration-none">
                    <i class="bi bi-cash-coin"></i> Caisses
                </a>
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('cash-registers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouvelle caisse
            </a>
        </div>
    </nav>

    <!-- Accordion Filters -->
    @php
    $filtersActive = request()->hasAny(['search', 'agency_id', 'status', 'date_from', 'date_to']);
@endphp
@php
    $filtersActive = request()->hasAny(['search', 'agency_id', 'status', 'date_from', 'date_to']);
@endphp
<!-- Accordion Filters -->
<div class="accordion mb-4" id="filterAccordion">
    <div class="accordion-item shadow-sm">
        <h2 class="accordion-header" id="headingFilters">
            <button class="accordion-button bg-light {{ $filtersActive ? '' : 'collapsed' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseFilters"
                    aria-expanded="{{ $filtersActive ? 'true' : 'false' }}"
                    aria-controls="collapseFilters">
                <i class="bi bi-funnel me-2"></i> Filtres de recherche
            </button>
        </h2>
        <div id="collapseFilters"
             class="accordion-collapse collapse {{ $filtersActive ? 'show' : '' }}"
             aria-labelledby="headingFilters"
             data-bs-parent="#filterAccordion">
            <div class="accordion-body">
                <form method="GET" action="{{ route('cash-registers.index') }}" class="row g-3">
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
                                   placeholder="Utilisateur, stock, statut...">
                        </div>
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
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Ouverte</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Fermée</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendue</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date début</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date fin</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Results Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Liste des caisses ({{ $cashRegisters->total() }} résultats)
            </h5>
        </div>
        <div class="card-body p-0">
            @if($cashRegisters->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                {{-- <th>Solde ouverture</th>
                                <th>Solde fermeture</th> --}}
                                <th>Solde actuel</th>
                                <th>Statut</th>
                                <th>Dates</th>
                                <th>Agence</th>
                                <th>Créé par</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cashRegisters as $cashRegister)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $cashRegister->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $cashRegister->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- <td>
                                        <span class="badge bg-success">
                                            <i class="bi bi-cash me-1"></i>
                                            {{ number_format($cashRegister->opening_balance, 2) }} Fbu
                                        </span>
                                    </td>
                                    <td>
                                        @if($cashRegister->closing_balance)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-cash-stack me-1"></i>
                                                {{ number_format($cashRegister->closing_balance, 2) }} Fbu
                                            </span>
                                        @else
                                            <span class="text-muted">Non défini</span>
                                        @endif
                                    </td> --}}
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="bi bi-cash-stack me-1"></i>
                                            {{ number_format($cashRegister->balance, 2) }} Fbu
                                        </span>
                                    </td>
                                    <td>
                                        @switch($cashRegister->status)
                                            @case('open')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-unlock me-1"></i>
                                                    Ouverte
                                                </span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-lock me-1"></i>
                                                    Fermée
                                                </span>
                                                @break
                                            @case('suspended')
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-pause me-1"></i>
                                                    Suspendue
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <div>
                                                <i class="bi bi-calendar-check text-success me-1"></i>
                                                {{ \Carbon\Carbon::parse($cashRegister->opened_at)->format('d/m/Y H:i') }}
                                            </div>
                                            @if($cashRegister->closed_at)
                                                <div>
                                                    <i class="bi bi-calendar-x text-danger me-1"></i>
                                                    {{ \Carbon\Carbon::parse($cashRegister->closed_at)->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($cashRegister->agency)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-info me-1"></i>
                                                {{ $cashRegister->agency->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-success me-1"></i>
                                            {{ $cashRegister->createdBy->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('cash-registers.show', $cashRegister) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('cash-registers.edit', $cashRegister) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($cashRegister->status === 'open')
                                                <form action="{{ route('cash-register.close', $cashRegister) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir fermer cette caisse ?')">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            title="Fermer la caisse">
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('cash-registers.destroy', $cashRegister) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($cashRegisters->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $cashRegisters->firstItem() }} à {{ $cashRegisters->lastItem() }}
                                sur {{ $cashRegisters->total() }} résultats
                            </div>
                            {{ $cashRegisters->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-cash-coin display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucune caisse trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez une nouvelle caisse.</p>
                    <a href="{{ route('cash-registers.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer la première caisse
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
