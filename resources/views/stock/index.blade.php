@extends('layouts.app')
@section('title', 'Stock')
@section('page-title', 'Stock')

@section('breadcrumb')
<li class="breadcrumb-item active">Stock</li>
@endsection
@section('content')
<div class="container">
    <div class="mb-4 row justify-content-between">
        <div class="col-md-8">
            <h2>Liste des Stocks</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Stock
            </a>
        </div>
    </div>

    <!-- Barre de recherche -->
    <div class="mb-4 card">
        <div class="card-body">
            <form action="{{ route('stocks.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, emplacement ou description..." value="{{ $search ?? '' }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des stocks -->
    <div class="card">
        <div class="p-0 card-body">
            <div class="table-responsive">
                <table class="table mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Emplacement</th>
                            <th>Agence</th>
                            <th>Créé par</th>
                            <th>Responsable</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <td>{{ $stock->name }}</td>
                                <td>{{ $stock->location ?? 'N/A' }}</td>
                                <td>{{ $stock->agency->name ?? 'N/A' }}</td>
                                <td>{{ $stock->creator->name ?? 'N/A' }}</td>
                                <td>{{ $stock->user->name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('stocks.show', $stock->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('stocks.edit', $stock->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Bouton de suppression avec confirmation -->
                                    <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce stock ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun stock trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        @if($stocks->hasPages())
            <div class="card-footer">
                {{ $stocks->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Styles pour la pagination -->
<style>
    .pagination {
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .page-link {
        color: #0d6efd;
    }
</style>
@endsection
