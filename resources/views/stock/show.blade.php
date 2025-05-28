@extends('layouts.app')

@section('title', 'Détails du Stock')

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
                <a href="{{ route('stocks.index') }}" class="text-decoration-none">
                    <i class="bi bi-boxes"></i> Stocks
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $stock->name }}
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>
                Modifier
            </a>
        </div>
    </nav>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-box-fill me-2"></i>
                        {{ $stock->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informations générales</h6>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Nom:</label>
                                <p class="mb-1">{{ $stock->name }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Localisation:</label>
                                <p class="mb-1">
                                    @if($stock->location)
                                        <i class="bi bi-geo-alt text-primary me-1"></i>
                                        {{ $stock->location }}
                                    @else
                                        <span class="text-muted">Non spécifiée</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Description:</label>
                                <p class="mb-1">
                                    @if($stock->description)
                                        {{ $stock->description }}
                                    @else
                                        <span class="text-muted">Aucune description</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Assignations</h6>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Agence:</label>
                                <p class="mb-1">
                                    @if($stock->agency)
                                        <i class="bi bi-building text-info me-1"></i>
                                        <a href="{{ route('agencies.show', $stock->agency) }}" class="text-decoration-none">
                                            {{ $stock->agency->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Assigné à:</label>
                                <p class="mb-1">
                                    @if($stock->user)
                                        <i class="bi bi-person-check text-warning me-1"></i>
                                        {{ $stock->user->name }}
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Créé par:</label>
                                <p class="mb-1">
                                    <i class="bi bi-person-circle text-success me-1"></i>
                                    {{ $stock->createdBy->full_name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations système -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">ID:</label>
                        <p class="mb-1">
                            <span class="badge bg-secondary">#{{ $stock->id }}</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Date de création:</label>
                        <p class="mb-1">
                            <i class="bi bi-calendar text-primary me-1"></i>
                            {{ $stock->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Dernière modification:</label>
                        <p class="mb-1">
                            <i class="bi bi-clock text-warning me-1"></i>
                            {{ $stock->updated_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    @if($stock->deleted_at)
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Supprimé le:</label>
                            <p class="mb-1">
                                <i class="bi bi-trash text-danger me-1"></i>
                                {{ $stock->deleted_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>
                            Modifier ce stock
                        </a>

                        <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Créer un nouveau stock
                        </a>

                        <hr>

                        <form action="{{ route('stocks.destroy', $stock) }}"
                              method="POST"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce stock ? Cette action est irréversible.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash me-2"></i>
                                Supprimer ce stock
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton retour -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour à la liste des stocks
            </a>
        </div>
    </div>
</div>
@endsection
