@extends('layouts.app')

@section('title', 'Gestion des Clients')

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
                <a href="{{ route('clients.index') }}" class="text-decoration-none">
                    <i class="bi bi-people"></i> Clients
                </a>
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau Client
            </a>
        </div>
    </nav>

    <!-- Accordion Filters -->
    <div class="accordion mb-4" id="filterAccordion">
        <div class="accordion-item shadow-sm">
            <h2 class="accordion-header" id="headingFilters">
                <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                    <i class="bi bi-funnel me-2"></i> Filtres de recherche
                </button>
            </h2>
            <div id="collapseFilters" class="accordion-collapse collapse show" aria-labelledby="headingFilters" data-bs-parent="#filterAccordion">
                <div class="accordion-body">
                    <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
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
                                    placeholder="Nom, email, téléphone, société...">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label for="patient_type" class="form-label">Type</label>
                            <select class="form-select" id="patient_type" name="patient_type">
                                <option value="">Tous</option>
                                <option value="physique" {{ request('patient_type') == 'physique' ? 'selected' : '' }}>
                                    Personne physique
                                </option>
                                <option value="morale" {{ request('patient_type') == 'morale' ? 'selected' : '' }}>
                                    Personne morale
                                </option>
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
                            <label for="created_by" class="form-label">Créé par</label>
                            <select class="form-select" id="created_by" name="created_by">
                                <option value="">Tous</option>
                                @foreach($creators as $creator)
                                    <option value="{{ $creator->id }}"
                                            {{ request('created_by') == $creator->id ? 'selected' : '' }}>
                                        {{ $creator->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
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
                Liste des Clients ({{ $clients->total() }} résultats)
            </h5>
        </div>
        <div class="card-body p-0">
            @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Contact</th>
                                <th>Société/NIF</th>
                                {{-- <th>Solde</th> --}}
                                <th>Agence</th>
                                <th>Créé par</th>
                                <th>Créé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-fill text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $client->name }}</strong>
                                                @if($client->first_name || $client->last_name)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $client->first_name }} {{ $client->last_name }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{-- @dump($client->patient_type) --}}
                                        @if($client->patient_type == 'physique')
                                            <span class="badge bg-success">
                                                <i class="bi bi-person me-1"></i>
                                                Physique
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="bi bi-building me-1"></i>
                                                Morale
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($client->email)
                                            <div class="mb-1">
                                                <i class="bi bi-envelope text-muted me-1"></i>
                                                <small>{{ $client->email }}</small>
                                            </div>
                                        @endif
                                        @if($client->phone)
                                            <div>
                                                <i class="bi bi-telephone text-muted me-1"></i>
                                                <small>{{ $client->phone }}</small>
                                            </div>
                                        @endif
                                        @if(!$client->email && !$client->phone)
                                            <span class="text-muted">Non renseigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($client->societe)
                                            <div class="mb-1">
                                                <i class="bi bi-building text-muted me-1"></i>
                                                <small>{{ $client->societe }}</small>
                                            </div>
                                        @endif
                                        @if($client->nif)
                                            <div>
                                                <i class="bi bi-card-text text-muted me-1"></i>
                                                <small>{{ $client->nif }}</small>
                                            </div>
                                        @endif
                                        @if(!$client->societe && !$client->nif)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        <span class="fw-bold {{ $client->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($client->balance, 0, ',', ' ') }} F
                                        </span>
                                    </td> --}}
                                    <td>
                                        @if($client->agency)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-info me-1"></i>
                                                {{ $client->agency->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-success me-1"></i>
                                            {{ $client->createdBy->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        <small>{{ $client->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('clients.show', $client) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('clients.edit', $client) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('clients.destroy', $client) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
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
                @if($clients->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $clients->firstItem() }} à {{ $clients->lastItem() }}
                                sur {{ $clients->total() }} résultats
                            </div>
                            {{ $clients->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-person-x display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucun client trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez un nouveau client.</p>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer le premier client
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
