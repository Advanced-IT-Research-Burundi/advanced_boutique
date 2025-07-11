@extends('layouts.app')

@section('title', 'Gestion des Stocks')

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
                <a href="{{ route('stocks.index') }}" class="text-decoration-none">
                    <i class="bi bi-boxes"></i> Stocks
                </a>
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('stocks.transfer') }}" class="btn btn-primary">
                <i class="bi bi-boxes me-2"></i>
                Transfert Entre Stock
            </a>
        </div>
        <div class="ms-auto">
            <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau Stock
            </a>
        </div>
    </nav>

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
                    <form method="GET" action="{{ route('stocks.index') }}" class="row g-3">
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
                                    placeholder="Nom, localisation ou description...">
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

                        <div class="col-md-2">
                            <label for="user_id" class="form-label">Assigné à</label>
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

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
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
                Liste des Stocks ({{ $stocks->total() }} résultats)
            </h5>
        </div>
        <div class="p-0 card-body">
            @if($stocks->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Localisation</th>
                                <th>Description</th>
                                <th>Agence</th>
                                <th>Créé par</th>
                                <th>Assigné à</th>
                                <th>Créé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stocks as $stock)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-box-fill text-primary me-2"></i>
                                            <strong>{{ $stock->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($stock->location)
                                            <i class="bi bi-geo-alt text-muted me-1"></i>
                                            {{ $stock->location }}
                                        @else
                                            <span class="text-muted">Non spécifiée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($stock->description)
                                            <span title="{{ $stock->description }}">
                                                {{ Str::limit($stock->description, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Aucune description</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($stock->agency)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-info me-1"></i>
                                                {{ $stock->agency->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-success me-1"></i>
                                            {{ $stock->createdBy->full_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($stock->user)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-check text-warning me-1"></i>
                                                {{ $stock->user->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        <small>{{ $stock->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('stocks.show', $stock) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('stocks.edit', $stock) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <!-- <form action="{{ route('stocks.destroy', $stock) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form> -->
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($stocks->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $stocks->firstItem() }} à {{ $stocks->lastItem() }}
                                sur {{ $stocks->total() }} résultats
                            </div>
                            {{ $stocks->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="py-5 text-center">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Aucun stock trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez un nouveau stock.</p>
                    <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer le premier stock
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
