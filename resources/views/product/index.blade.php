@extends('layouts.app')

@section('title', 'Gestion des Produits')

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
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <i class="bi bi-box-seam"></i> Produits
                </a>
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau Produit
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
                    <form method="GET" action="{{ route('products.index') }}" class="row g-3">
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
                                    placeholder="Nom, description ou unité...">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label for="category_id" class="form-label">Catégorie</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Toutes</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
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

                        {{-- <div class="col-md-2">
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
                        </div> --}}

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
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
                Liste des Produits ({{ $products->total() }} résultats)
            </h5>
        </div>
        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix d'Achat</th>
                                <th>Prix de Vente</th>
                                <th>Unité</th>
                                <th>Seuil d'Alerte</th>
                                <th>Agence</th>
                                <th>Créé par</th>
                                {{-- <th>Assigné à</th> --}}
                                <th>Créé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="rounded"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->code }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-box-seam text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                        @else
                                            <span class="text-muted">Non catégorisé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">{{ number_format($product->purchase_price, 0, ',', ' ') }} FBU</span>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold">{{ number_format($product->sale_price, 0, ',', ' ') }} FBU</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->unit }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ $product->alert_quantity }}</span>
                                    </td>
                                    <td>
                                        @if($product->agency)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-info me-1"></i>
                                                {{ $product->agency->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-success me-1"></i>
                                            {{ $product->createdBy->last_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    {{-- <td>
                                        @if($product->user)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-check text-warning me-1"></i>
                                                {{ $product->user->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td> --}}
                                    <td class="text-muted">
                                        <small>{{ $product->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.show', $product) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
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
                @if($products->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $products->firstItem() }} à {{ $products->lastItem() }}
                                sur {{ $products->total() }} résultats
                            </div>
                            {{ $products->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box-seam display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucun produit trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez un nouveau produit.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer le premier produit
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
