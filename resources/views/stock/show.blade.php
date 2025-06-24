@extends('layouts.app')

@section('title', 'Détails du Stock')

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
            <!-- Section des 5 derniers produits ajoutés -->
            <div class="mb-4 shadow-sm card">
                <div class="text-white card-header bg-success d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 card-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Derniers produits ajoutés
                    </h6>
                    <a href="{{ route('stocks.list', $stock) }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-boxes me-1"></i>
                        Voir tous les produits
                    </a>
                </div>
                <div class="card-body">
                    @if($recentProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>Date d'ajout</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProducts as $stockProduct)
                                    {{-- {{$stockProduct}} --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($stockProduct->product->image)
                                                        <img src="{{ asset('storage/' . $stockProduct->product->image) }}"
                                                             alt="{{ $stockProduct->product->name }}"
                                                             class="rounded me-2"
                                                             style="width: 32px; height: 32px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded bg-secondary me-2 d-flex align-items-center justify-content-center"
                                                             style="width: 32px; height: 32px;">
                                                            <i class="text-white bi bi-box"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $stockProduct->product->name ?? 'N/A' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $stockProduct->product->unit ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ number_format($stockProduct->quantity, 2) }}</span>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ $stockProduct->created_at->format('d/m/Y') }}
                                                    <br>
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $stockProduct->created_at->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('stocks.mouvement', $stockProduct->id) }}"
                                                       class="btn btn-outline-info"
                                                       title="Voir les mouvements">
                                                        <i class="bi bi-arrows-angle-contract">Mouvement</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-4 text-center">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="mt-2 text-muted">Aucun produit dans ce stock</p>
                            <a href="{{ route('stocks.list', $stock) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Ajouter des produits
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mb-4 shadow-sm card">
                <div class="text-white card-header bg-info">
                    <h5 class="mb-0 card-title">
                        <i class="bi bi-box-fill me-2"></i>
                        {{ $stock->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 text-muted">Informations générales</h6>

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
                            <h6 class="mb-3 text-muted">Assignations</h6>

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

        <!-- Informations système et Actions -->
        <div class="col-lg-4">
            <div class="mb-4 shadow-sm card">
                <div class="text-white card-header bg-secondary">
                    <h6 class="mb-0 card-title">
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

            <!-- Statistiques rapides -->
            <div class="mb-4 shadow-sm card">
                <div class="text-white card-header bg-primary">
                    <h6 class="mb-0 card-title">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center row">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="mb-0 text-primary">{{ $stockProducts->count() }}</h4>
                                <small class="text-muted">Produits</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 text-success">{{ number_format($stockProducts->sum('quantity'), 0) }}</h4>
                            <small class="text-muted">Quantité totale</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="shadow-sm card">
                <div class="text-white card-header bg-dark">
                    <h6 class="mb-0 card-title">
                        <i class="bi bi-lightning me-2"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="gap-2 d-grid">
                        <a href="{{ route('stocks.list', $stock) }}" class="btn btn-outline-primary">
                            <i class="bi bi-boxes me-2"></i>
                            Gérer les produits
                        </a>

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
    <div class="mt-4 row">
        <div class="col-12">
            <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour à la liste des stocks
            </a>
        </div>
    </div>
</div>
@endsection
