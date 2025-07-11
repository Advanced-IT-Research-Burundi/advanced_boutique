@extends('layouts.app')

@section('title', 'Détails de la catégorie - ' . $category->name)

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
                <a href="{{ route('categories.index') }}" class="text-decoration-none">
                    <i class="bi bi-boxes"></i> Catégories
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $category->name }}
            </li>
        </ol>
    </nav>

    <!-- Category Details Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-box-fill me-2"></i>
                        {{ $category->name }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil me-1"></i> Modifier
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash me-1"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-3">
                                {{ $category->description ?: 'Aucune description disponible' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h6 class="text-muted mb-2">Agence</h6>
                                    <p class="mb-3">
                                        @if($category->agency)
                                            <i class="bi bi-building text-info me-1"></i>
                                            {{ $category->agency->name }}
                                        @else
                                            <span class="text-muted">Non assignée</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <h6 class="text-muted mb-2">Créé par</h6>
                                    <p class="mb-3">
                                        <i class="bi bi-person-circle text-success me-1"></i>
                                        {{ $category->createdBy->full_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <h6 class="text-muted mb-2">Date de création</h6>
                                    <p class="mb-0">
                                        <i class="bi bi-calendar3 text-warning me-1"></i>
                                        {{ $category->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <h6 class="text-muted mb-2">Dernière modification</h6>
                                    <p class="mb-0">
                                        <i class="bi bi-clock text-info me-1"></i>
                                        {{ $category->updated_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Produits</h6>
                            <h3 class="mb-0">{{ $totalProducts }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box2 fs-1"></i>
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
                            <h6 class="card-title">En Stock</h6>
                            <h3 class="mb-0">{{ $productsInStock }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Stock Faible</h6>
                            <h3 class="mb-0">{{ $productsLowStock }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
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
                            <h6 class="card-title">Rupture Stock</h6>
                            <h3 class="mb-0">{{ $productsOutOfStock }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Produits de la catégorie ({{ $products->total() }} produits)
            </h5>
            <a href="{{ route('products.create', ['category_id' => $category->id]) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>
                Ajouter un produit
            </a>
        </div>
        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Prix d'achat</th>
                                <th>Prix de vente HT</th>
                                <th>Prix de vente TTC</th>
                                <th>Unité</th>
                                <th class="text-center">Stock Total</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <span class="badge {{ $product->total_stock > 0 ? 'bg-secondary' : 'bg-light text-dark' }}">
                                            {{ $product->code }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                     alt="{{ $product->name }}"
                                                     class="me-2 rounded"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->description)
                                            <span title="{{ $product->description }}">
                                                {{ Str::limit($product->description, 40) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->purchase_price)
                                            <span class="fw-bold">{{ number_format($product->purchase_price, 0, ',', ' ') }} Fbu</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->sale_price_ht)
                                            <span class="fw-bold text-success">{{ number_format($product->sale_price_ht, 0, ',', ' ') }} Fbu</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->sale_price_ttc)
                                            <span class="fw-bold text-primary">{{ number_format($product->sale_price_ttc, 0, ',', ' ') }} Fbu</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->unit ?: 'Unité' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $product->total_stock > $product->alert_quantity ? 'bg-success' : ($product->total_stock > 0 ? 'bg-warning' : 'bg-danger') }} fs-6">
                                            {{ number_format($product->total_stock, 0, ',', ' ') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($product->total_stock <= 0)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Rupture
                                            </span>
                                        @elseif($product->total_stock <= $product->alert_quantity)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Faible
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Disponible
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.show', $product) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir le produit">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Détails stock"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#stockModal{{ $product->id }}">
                                                <i class="bi bi-graph-up"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $products->firstItem() }} à {{ $products->lastItem() }}
                                sur {{ $products->total() }} produits
                            </div>
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucun produit dans cette catégorie</h4>
                    <p class="text-muted">Commencez par ajouter des produits à cette catégorie.</p>
                    <a href="{{ route('products.create', ['category_id' => $category->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Ajouter le premier produit
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Stock Details Modals -->
@foreach($products as $product)
    <div class="modal fade" id="stockModal{{ $product->id }}" tabindex="-1" aria-labelledby="stockModalLabel{{ $product->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockModalLabel{{ $product->id }}">
                        <i class="bi bi-graph-up me-2"></i>
                        Détails du stock - {{ $product->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($product->stockProducts && $product->stockProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Stock</th>
                                        <th>Quantité</th>
                                        <th>Agence</th>
                                        <th>Utilisateur</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->stockProducts as $stockProduct)
                                        <tr>
                                            <td>{{ $stockProduct->stock->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ number_format($stockProduct->quantity, 0, ',', ' ') }}</span>
                                            </td>
                                            <td>{{ $stockProduct->agency->name ?? 'N/A' }}</td>
                                            <td>{{ $stockProduct->user->full_name ?? 'N/A' }}</td>
                                            <td>{{ $stockProduct->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>Total en stock :</strong> {{ number_format($product->total_stock, 0, ',', ' ') }} {{ $product->unit ?: 'unités' }}
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="text-muted mt-2">Aucun mouvement de stock enregistré</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
