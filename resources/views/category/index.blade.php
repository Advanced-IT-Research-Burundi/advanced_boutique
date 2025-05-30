@extends('layouts.app')

@section('title', 'Gestion des categories')

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
                <a href="{{ route('categories.index') }}" class="text-decoration-none">
                    <i class="bi bi-boxes"></i> Categories
                </a>
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau category
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
                    <form method="GET" action="{{ route('categories.index') }}" class="row g-3">
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
                                    placeholder="Nom ou description...">
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


                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
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
                Liste des categories ({{ $categories->total() }} résultats)
            </h5>
        </div>
        <div class="card-body p-0">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Agence</th>
                                <th>Créé par</th>
                                <th>Créé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-box-fill text-primary me-2"></i>
                                            <strong>{{ $category->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($category->description)
                                            <span title="{{ $category->description }}">
                                                {{ Str::limit($category->description, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Aucune description</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($category->agency)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-info me-1"></i>
                                                {{ $category->agency->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-success me-1"></i>
                                            {{ $category->createdBy->full_name ?? 'N/A' }}
                                        </div>
                                    </td>

                                    <td class="text-muted">
                                        <small>{{ $category->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('categories.show', $category) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('categories.edit', $category) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce category ?')">
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
                @if($categories->hasPages())
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $categories->firstItem() }} à {{ $categories->lastItem() }}
                                sur {{ $categories->total() }} résultats
                            </div>
                            {{ $categories->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucun category trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez un nouveau category.</p>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer le premier category
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
