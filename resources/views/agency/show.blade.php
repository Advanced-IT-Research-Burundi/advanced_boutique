@extends('layouts.app')

@section('title', 'Détails de l\'Agence - ' . $agency->name)

@section('content')
<div class="container-fluid px-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('agencies.index') }}" class="text-decoration-none">
                    <i class="bi bi-building"></i> Agences
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-eye"></i> {{ $agency->name }}
            </li>
        </ol>
        <div class="d-flex gap-2">
            <a href="{{ route('agencies.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour
            </a>
        </div>
    </nav>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations Générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-hash text-primary me-2"></i>
                                    Code de l'agence
                                </label>
                                <div class="info-value">
                                    <span class="badge bg-secondary fs-6">{{ $agency->code }}</span>
                                </div>
                            </div>

                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-building text-primary me-2"></i>
                                    Nom de l'agence
                                </label>
                                <div class="info-value">{{ $agency->name }}</div>
                            </div>

                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-briefcase text-primary me-2"></i>
                                    Entreprise
                                </label>
                                <div class="info-value">
                                    @if($agency->company)
                                        <span class="badge bg-info">{{ $agency->company->name }}</span>
                                    @else
                                        <span class="text-muted">Non renseignée</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-person-gear text-primary me-2"></i>
                                    Manager
                                </label>
                                <div class="info-value">
                                    @if($agency->manager)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($agency->manager->name, 0, 1)) }}
                                            </div>
                                            {{ $agency->manager->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">Aucun manager assigné</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-diagram-3 text-primary me-2"></i>
                                    Agence Parent
                                </label>
                                <div class="info-value">
                                    @if($agency->parentAgency)
                                        <span class="badge bg-warning">{{ $agency->parentAgency->name }}</span>
                                    @else
                                        <span class="text-muted">Agence principale</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-gear text-primary me-2"></i>
                                    Type d'agence
                                </label>
                                <div class="info-value">
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    Adresse
                                </label>
                                <div class="info-value">
                                    <div class="address-card">
                                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                        {{ $agency->adresse }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- References Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-link-45deg me-2"></i>
                        Références et Associations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-person text-success me-2"></i>
                                    Utilisateur Associé
                                </label>
                                <div class="info-value">
                                    @if($agency->user)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($agency->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $agency->user->name }}</div>
                                                <small class="text-muted">{{ $agency->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Aucun utilisateur associé</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">
                                    <i class="bi bi-person-plus text-success me-2"></i>
                                    Créé par
                                </label>
                                <div class="info-value">
                                    @if($agency->createdBy)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-secondary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($agency->createdBy->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $agency->createdBy->full_name }}</div>
                                                <small class="text-muted">{{ $agency->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Non renseigné</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub-Agencies Card -->
            @if($agency->agencies->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Agences Filiales
                        <span class="badge bg-dark ms-2">{{ $agency->agencies->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($agency->agencies as $subAgency)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <div class="icon-circle bg-warning text-dark mb-2 mx-auto">
                                        <i class="bi bi-building fs-5"></i>
                                    </div>
                                    <h6 class="card-title">{{ $subAgency->name }}</h6>
                                    <p class="card-text small text-muted">{{ $subAgency->code }}</p>
                                    <a href="{{ route('agencies.show', $subAgency) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-eye me-1"></i>
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-gradient-primary text-white shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold text-white-50 text-uppercase">Produits</div>
                                    <div class="h5">{{ $agency->products->count() }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-box-seam fs-2 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-gradient-success text-white shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold text-white-50 text-uppercase">Clients</div>
                                    <div class="h5">{{ $agency->clients->count() }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-people fs-2 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-gradient-info text-white shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold text-white-50 text-uppercase">Fournisseurs</div>
                                    <div class="h5">{{ $agency->suppliers->count() }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-truck fs-2 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-gradient-warning text-white shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold text-white-50 text-uppercase">Ventes</div>
                                    <div class="h5">{{ $agency->sales->count() }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-graph-up fs-2 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('agencies.edit', $agency) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square me-2"></i>
                            Modifier l'agence
                        </a>

                        @if($agency->products->count() > 0)
                        <a href="{{ route('products.index', ['agency' => $agency->id]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-box-seam me-2"></i>
                            Voir les produits
                        </a>
                        @endif

                        @if($agency->clients->count() > 0)
                        <a href="{{ route('clients.index', ['agency' => $agency->id]) }}" class="btn btn-outline-success">
                            <i class="bi bi-people me-2"></i>
                            Voir les clients
                        </a>
                        @endif

                        @if($agency->suppliers->count() > 0)
                        <a href="{{ route('suppliers.index', ['agency' => $agency->id]) }}" class="btn btn-outline-info">
                            <i class="bi bi-truck me-2"></i>
                            Voir les fournisseurs
                        </a>
                        @endif

                        <div class="dropdown-divider"></div>

                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>
                            Supprimer l'agence
                        </button>
                    </div>
                </div>
            </div>

            <!-- Agency Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Statut de l'Agence
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="text-success fs-4">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="small text-muted">Statut</div>
                                <div class="fw-bold text-success">Active</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-primary fs-4">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="small text-muted">Créée le</div>
                            <div class="fw-bold">{{ $agency->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="text-info fs-4">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </div>
                                <div class="small text-muted">Dernière MAJ</div>
                                <div class="fw-bold small">{{ $agency->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-warning fs-4">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <div class="small text-muted">Sous-agences</div>
                            <div class="fw-bold">{{ $agency->agencies->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2"></i>
                        Activité Récente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($agency->sales->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="fw-bold">Dernière vente</div>
                                <div class="small text-muted">
                                    {{ $agency->sales->latest()->first()->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($agency->purchases->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="fw-bold">Dernier achat</div>
                                <div class="small text-muted">
                                    {{ $agency->purchases->latest()->first()->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <div class="fw-bold">Agence créée</div>
                                <div class="small text-muted">
                                    {{ $agency->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="text-danger fs-1 mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5>Êtes-vous sûr de vouloir supprimer cette agence ?</h5>
                    <p class="text-muted">
                        Cette action supprimera définitivement l'agence <strong>{{ $agency->name }}</strong>
                        et toutes les données associées. Cette action est irréversible.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Annuler
                </button>
                <form action="{{ route('agencies.destroy', $agency) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    display: block;
}

.info-value {
    font-size: 1rem;
    color: #212529;
    font-weight: 500;
}

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 0.875rem;
    font-weight: 600;
}

.address-card {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    border-left: 4px solid #dc3545;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
}

.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #117a8b);
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
}

.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.25rem;
    top: 1rem;
    height: 100%;
    width: 2px;
    background: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    margin-left: 0.5rem;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.btn {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection
