@extends('layouts.app')

@section('title', 'Types de Dépense')

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
                <i class="bi bi-tags"></i> Types de Dépense
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('expense-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau Type
            </a>
        </div>
    </nav>

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
                    <form method="GET" action="{{ route('expense-types.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nom...">
                        </div>
                        <div class="col-md-4">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select class="form-select" id="agency_id" name="agency_id">
                                <option value="">Toutes</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('expense-types.index') }}" class="btn btn-outline-secondary">
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
                Liste des Types de Dépense ({{ $expenseTypes->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if($expenseTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Agence</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseTypes as $type)
                                <tr>
                                    <td>{{ $type->name }}</td>
                                    <td>{{ $type->description }}</td>
                                    <td>{{ $type->agency->label ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('expense-types.show', $type) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('expense-types.edit', $type) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('expense-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce type ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $expenseTypes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-tags display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucun type de dépense trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez un nouvel type de dépense.</p>
                    <a href="{{ route('expense-types.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer le premier type de dépense
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
