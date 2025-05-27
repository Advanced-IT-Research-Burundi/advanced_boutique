@extends('layouts.app')

@section('title', 'Gestion des Agences')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-building text-primary me-2"></i>
                        Gestion des Agences
                    </h1>
                    <p class="text-muted mb-0">Gérez toutes vos agences en un seul endroit</p>
                </div>
                <a href="{{ route('agencies.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nouvelle Agence
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                    <form method="GET" action="{{ route('agencies.index') }}" class="row g-3">
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
                                    placeholder="Nom, code ou adresse...">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label for="company_id" class="form-label">Entreprise</label>
                            <select class="form-select" id="company_id" name="company_id">
                                <option value="">Toutes</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}"
                                            {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="manager_id" class="form-label">Manager</label>
                            <select class="form-select" id="manager_id" name="manager_id">
                                <option value="">Tous</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}"
                                            {{ request('manager_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="parent_agency_id" class="form-label">Agence Parent</label>
                            <select class="form-select" id="parent_agency_id" name="parent_agency_id">
                                <option value="">Toutes</option>
                                @foreach($parentAgencies as $parentAgency)
                                    <option value="{{ $parentAgency->id }}"
                                            {{ request('parent_agency_id') == $parentAgency->id ? 'selected' : '' }}>
                                        {{ $parentAgency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="is_main_office" class="form-label">Type</label>
                            <select class="form-select" id="is_main_office" name="is_main_office">
                                <option value="">Tous</option>
                                <option value="1" {{ request('is_main_office') === '1' ? 'selected' : '' }}>
                                    Siège Social
                                </option>
                                <option value="0" {{ request('is_main_office') === '0' ? 'selected' : '' }}>
                                    Agence
                                </option>
                            </select>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('agencies.index') }}" class="btn btn-outline-secondary">
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
                Liste des Agences ({{ $agencies->total() }} résultats)
            </h5>
        </div>
        <div class="card-body p-0">
            @if($agencies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Entreprise</th>
                                <th>Adresse</th>
                                <th>Manager</th>
                                <th>Type</th>
                                <th>Agence Parent</th>
                                <th>Créé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agencies as $agency)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $agency->code }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-building-fill text-primary me-2"></i>
                                            <strong>{{ $agency->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $agency->company->name ?? 'N/A' }}</td>
                                    <td>
                                        <i class="bi bi-geo-alt text-muted me-1"></i>
                                        {{ Str::limit($agency->adresse, 30) }}
                                    </td>
                                    <td>
                                        @if($agency->manager)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle text-info me-1"></i>
                                                {{ $agency->manager->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($agency->is_main_office)
                                            <span class="badge bg-success">
                                                <i class="bi bi-star me-1"></i>
                                                Siège Social
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="bi bi-building me-1"></i>
                                                Agence
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($agency->parentAgency)
                                            <span class="text-primary">{{ $agency->parentAgency->name }}</span>
                                        @else
                                            <span class="text-muted">Aucune</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        <small>{{ $agency->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agencies.show', $agency) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('agencies.edit', $agency) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('agencies.destroy', $agency) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette agence ?')">
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
                @if($agencies->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $agencies->firstItem() }} à {{ $agencies->lastItem() }}
                                sur {{ $agencies->total() }} résultats
                            </div>
                            {{ $agencies->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucune agence trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez une nouvelle agence.</p>
                    <a href="{{ route('agencies.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer la première agence
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
