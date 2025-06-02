@extends('layouts.app')

@section('title', 'Détails du Produit')

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
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <i class="bi bi-box-seam"></i> Produits
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $product->name }}
            </li>
        </ol>
    </nav>

    <!-- Product Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        {{ $product->name }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>
                            Modifier
                        </a>
                        <form action="{{ route('products.destroy', $product) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-2"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Image et informations principales -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>
                        Image du produit
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}"
                             alt="{{ $product->name }}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 300px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                             style="height: 200px;">
                            <div class="text-center">
                                <i class="bi bi-image display-1 text-muted"></i>
                                <p class="text-muted mt-2">Aucune image</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations de prix -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Informations de prix
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-success mb-1">{{ number_format($product->purchase_price, 0, ',', ' ') }}</h5>
                                <small class="text-muted">Prix d'achat (FBU)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-primary mb-1">{{ number_format($product->sale_price, 0, ',', ' ') }}</h5>
                            <small class="text-muted">Prix de vente (FBU)</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        @php
                            $margin = $product->purchase_price > 0 ? (($product->sale_price - $product->purchase_price) / $product->purchase_price * 100) : 0;
                        @endphp
                        <h6 class="mb-1 {{ $margin > 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($margin, 2) }}%
                        </h6>
                        <small class="text-muted">Marge bénéficiaire</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails du produit -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations détaillées
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nom du produit</label>
                            <p class="form-control-plaintext">{{ $product->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Catégorie</label>
                            <p class="form-control-plaintext">
                                @if($product->category)
                                    <span class="badge bg-secondary fs-6">{{ $product->category->name }}</span>
                                @else
                                    <span class="text-muted">Non catégorisé</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <p class="form-control-plaintext">
                                {{ $product->description ?: 'Aucune description disponible' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Unité de mesure</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info fs-6">{{ $product->unit }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Seuil d'alerte</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-warning text-dark fs-6">{{ $product->alert_quantity }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations d'attribution -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        Attribution et gestion
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Agence</label>
                            <p class="form-control-plaintext">
                                @if($product->agency)
                                    <i class="bi bi-building text-info me-2"></i>
                                    {{ $product->agency->name }}
                                @else
                                    <span class="text-muted">Non assigné à une agence</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Assigné à</label>
                            <p class="form-control-plaintext">
                                @if($product->user)
                                    <i class="bi bi-person-check text-warning me-2"></i>
                                    {{ $product->user->name }}
                                @else
                                    <span class="text-muted">Non assigné à un utilisateur</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Créé par</label>
                            <p class="form-control-plaintext">
                                <i class="bi bi-person-circle text-success me-2"></i>
                                {{ $product->createdBy->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de création</label>
                            <p class="form-control-plaintext">
                                <i class="bi bi-calendar text-muted me-2"></i>
                                {{ $product->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        @if($product->updated_at != $product->created_at)
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Dernière modification</label>
                                <p class="form-control-plaintext">
                                    <i class="bi bi-clock text-muted me-2"></i>
                                    {{ $product->updated_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Footer -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                        <div>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning me-2">
                                <i class="bi bi-pencil me-2"></i>
                                Modifier ce produit
                            </a>
                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Nouveau produit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
