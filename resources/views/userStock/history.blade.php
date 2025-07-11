@extends('layouts.app')

@section('title', 'Historique des Stocks - ' . $user->first_name . ' ' . $user->last_name)

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('users.index') }}" class="text-decoration-none">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                    {{ $user->first_name }} {{ $user->last_name }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('users.stocks.manage', $user) }}" class="text-decoration-none">
                    Gestion des Stocks
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Historique
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-clock-history me-2"></i>
                Historique des Stocks
            </h1>
            <p class="text-muted mb-0">
                Utilisateur: <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('users.stocks.manage', $user) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour à la Gestion
            </a>
            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                <i class="bi bi-person me-2"></i>
                Voir le Profil
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Assignations</h6>
                            <h3>{{ $stockHistory->total() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Stocks Actifs</h6>
                            <h3>{{ $stockHistory->where('deleted_at', null)->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Stocks Désassignés</h6>
                            <h3>{{ $stockHistory->where('deleted_at', '!=', null)->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Dernière Action</h6>
                            <h6>{{ $stockHistory->first() ? $stockHistory->first()->created_at->diffForHumans() : 'Aucune' }}</h6>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Historique Détaillé
            </h6>
        </div>
        <div class="card-body">
            @if($stockHistory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Stock</th>
                                <th>Agence</th>
                                <th>Action</th>
                                <th>Créé par</th>
                                <th>Date d'assignation</th>
                                <th>Date de désassignation</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockHistory as $assignment)
                                <tr class="{{ $assignment->deleted_at ? 'table-secondary' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-box text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $assignment->stock ? $assignment->stock->name : 'Stock supprimé' }}</strong>
                                                @if($assignment->stock && $assignment->stock->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($assignment->stock->description, 30) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($assignment->agency)
                                            <i class="bi bi-geo-alt text-warning me-1"></i>
                                            {{ $assignment->agency->name }}
                                        @else
                                            <span class="text-muted">Aucune agence</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->deleted_at)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-dash-circle me-1"></i>
                                                Désassigné
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                Assigné
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->createdBy)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle text-info me-1"></i>
                                                <div>
                                                    <small>{{ $assignment->createdBy->first_name }} {{ $assignment->createdBy->last_name }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $assignment->createdBy->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Utilisateur supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->created_at->format('d/m/Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assignment->created_at->format('H:i') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $assignment->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($assignment->deleted_at)
                                            <div>
                                                <strong>{{ $assignment->deleted_at->format('d/m/Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $assignment->deleted_at->format('H:i') }}</small>
                                                <br>
                                                <small class="text-muted">{{ $assignment->deleted_at->diffForHumans() }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->deleted_at)
                                            <span class="badge bg-outline-secondary">Inactif</span>
                                        @else
                                            <span class="badge bg-outline-success">Actif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <small class="text-muted">
                            Affichage de {{ $stockHistory->firstItem() }} à {{ $stockHistory->lastItem() }}
                            sur {{ $stockHistory->total() }} résultats
                        </small>
                    </div>
                    <div>
                        {{ $stockHistory->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clock-history text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Aucun historique</h4>
                    <p class="text-muted">Cet utilisateur n'a jamais eu de stock assigné.</p>
                    <a href="{{ route('users.stocks.manage', $user) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Assigner des stocks
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Export Options -->
    @if($stockHistory->count() > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-download me-2"></i>
                    Options d'Export
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        {{-- <p class="text-muted mb-3">Exportez l'historique des stocks pour cet utilisateur</p>
                        <div class="btn-group">
                            <a href="{{ route('users.stocks.history', ['user' => $user, 'export' => 'pdf']) }}"
                               class="btn btn-outline-danger">
                                <i class="bi bi-file-earmark-pdf me-2"></i>
                                Export PDF
                            </a>
                            <a href="{{ route('users.stocks.history', ['user' => $user, 'export' => 'excel']) }}"
                               class="btn btn-outline-success">
                                <i class="bi bi-file-earmark-excel me-2"></i>
                                Export Excel
                            </a>
                            <a href="{{ route('users.stocks.history', ['user' => $user, 'export' => 'csv']) }}"
                               class="btn btn-outline-info">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Export CSV
                            </a>
                        </div> --}}
                    </div>
                    <div class="col-md-6">
                        <div class="border-start ps-4">
                            <h6 class="text-muted">Filtres disponibles</h6>
                            <form method="GET" class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Date de début</label>
                                    <input type="date" class="form-control form-control-sm"
                                           name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de fin</label>
                                    <input type="date" class="form-control form-control-sm"
                                           name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select form-select-sm" name="status">
                                        <option value="">Tous les statuts</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                            Actifs seulement
                                        </option>
                                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>
                                            Désassignés seulement
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm me-2">
                                        <i class="bi bi-funnel me-1"></i>
                                        Filtrer
                                    </button>
                                    <a href="{{ route('users.stocks.history', $user) }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-clockwise me-1"></i>
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh every 30 seconds if there are active assignments
    @if($stockHistory->where('deleted_at', null)->count() > 0)
    setInterval(function() {
        // Check if page is visible to avoid unnecessary requests
        if (!document.hidden) {
            window.location.reload();
        }
    }, 30000);
    @endif

    // Tooltip initialization for better UX
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
