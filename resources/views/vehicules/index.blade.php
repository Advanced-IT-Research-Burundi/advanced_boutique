@extends('layouts.app')

@section('title', 'Liste des véhicules')

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
                <i class="bi bi-car-front"></i> Véhicules
            </li>
        </ol>
        <div>
            <a href="{{ route('vehicules.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau véhicule
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
                    <form method="GET" action="{{ route('vehicules.index') }}" class="row g-3">

                        <!-- Search -->
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
                                    placeholder="Marque, modèle, immatriculation...">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-2">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous</option>
                                <option value="disponible" {{ request('status') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="en_location" {{ request('status') == 'en_location' ? 'selected' : '' }}>En location</option>
                                <option value="en_reparation" {{ request('status') == 'en_reparation' ? 'selected' : '' }}>En réparation</option>
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="col-md-2">
                            <label for="brand" class="form-label">Marque</label>
                            <input type="text"
                                class="form-control"
                                id="brand"
                                name="brand"
                                value="{{ request('brand') }}"
                                placeholder="Peugeot, Renault...">
                        </div>

                        <!-- Year -->
                        <div class="col-md-2">
                            <label for="year" class="form-label">Année</label>
                            <input type="number"
                                class="form-control"
                                id="year"
                                name="year"
                                value="{{ request('year') }}"
                                placeholder="2020">
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('vehicules.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total véhicules
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $vehicules->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-car-front fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Disponibles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $vehicules->where('statut', 'disponible')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En location
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $vehicules->where('statut', 'en_location')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                En maintenance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $vehicules->where('statut', 'maintenance')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Tableau des véhicules -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Liste des véhicules ({{ $vehicules->total() }} résultat{{ $vehicules->total() > 1 ? 's' : '' }})
            </h6>

            <!-- Options de tri -->
            {{-- <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                        data-bs-toggle="dropdown">
                    <i class="bi bi-sort-down"></i> Trier par
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => 'desc']) }}">
                        <i class="bi bi-clock"></i> Plus récent
                    </a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'brand', 'sort_order' => 'asc']) }}">
                        <i class="bi bi-alphabet"></i> Marque A-Z
                    </a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'year', 'sort_order' => 'desc']) }}">
                        <i class="bi bi-calendar"></i> Année récente
                    </a></li>
                </ul>
            </div> --}}
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">
                                <i class="bi bi-car-front-fill me-1"></i>
                                Véhicule
                            </th>
                            <th class="border-0">
                                <i class="bi bi-calendar3 me-1"></i>
                                Année
                            </th>
                            <th class="border-0">
                                <i class="bi bi-credit-card me-1"></i>
                                Immatriculation
                            </th>
                            <th class="border-0">
                                <i class="bi bi-circle-fill me-1"></i>
                                Statut
                            </th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicules as $vehicule)
                            <tr>
                                <td class="fw-medium">{{ $vehicule->id }}</td>
                                <td>
                                    <div>
                                        <div class="fw-medium">{{ $vehicule->brand }}</div>
                                        <small class="text-muted">{{ $vehicule->model }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $vehicule->year }}</span>
                                </td>
                                <td>
                                    <code class="text-primary">{{ $vehicule->immatriculation }}</code>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'disponible' => ['class' => 'bg-success', 'icon' => 'bi-check-circle', 'text' => 'Disponible'],
                                            'en_location' => ['class' => 'bg-warning text-dark', 'icon' => 'bi-clock', 'text' => 'En location'],
                                            'en_reparation' => ['class' => 'bg-danger', 'icon' => 'bi-tools', 'text' => 'En réparation']
                                        ];
                                        $config = $statusConfig[$vehicule->status] ?? $statusConfig['disponible'];
                                    @endphp
                                    <span class="badge {{ $config['class'] }}">
                                        <i class="bi {{ $config['icon'] }} me-1"></i>
                                        {{ $config['text'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('vehicules.show', $vehicule) }}"
                                           class="btn btn-outline-info btn-sm"
                                           title="Voir les détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('vehicules.edit', $vehicule) }}"
                                           class="btn btn-outline-primary btn-sm"
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $vehicule->id }}"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de confirmation de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $vehicule->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer le véhicule :</p>
                                                    <strong>{{ $vehicule->brand }} {{ $vehicule->model }} ({{ $vehicule->immatriculation }})</strong>
                                                    <p class="text-danger mt-2">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        Cette action est irréversible.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Annuler
                                                    </button>
                                                    <form action="{{ route('vehicules.destroy', $vehicule) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-car-front display-4 d-block mb-3"></i>
                                        <p class="mb-0">Aucun véhicule trouvé</p>
                                        <small>Essayez de modifier vos critères de recherche</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($vehicules->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Affichage de {{ $vehicules->firstItem() }} à {{ $vehicules->lastItem() }}
                        sur {{ $vehicules->total() }} résultats
                    </div>
                    {{ $vehicules->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.avatar-sm {
    width: 2rem;
    height: 2rem;
}
.w-fit {
    width: fit-content !important;
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>
@endsection
