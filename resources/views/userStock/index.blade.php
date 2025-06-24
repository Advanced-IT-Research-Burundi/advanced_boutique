@extends('layouts.app')

@section('title', 'Gestion Utilisateurs-Stocks')

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
            <li class="breadcrumb-item active" aria-current="page">
                Associations Utilisateurs-Stocks
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-link-45deg me-2"></i>
                Associations Utilisateurs-Stocks
            </h1>
            <p class="text-muted mb-0">Gérer les associations entre utilisateurs et stocks</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('user-stocks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                Nouvelle Association
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                <i class="bi bi-collection me-2"></i>
                Associer en masse
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>
                Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="user_id" class="form-label">Utilisateur</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="stock_id" class="form-label">Stock</label>
                    <select name="stock_id" id="stock_id" class="form-select">
                        <option value="">Tous les stocks</option>
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->id }}" {{ request('stock_id') == $stock->id ? 'selected' : '' }}>
                                {{ $stock->code }} - {{ $stock->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="agency_id" class="form-label">Agence</label>
                    <select name="agency_id" id="agency_id" class="form-select">
                        <option value="">Toutes les agences</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                {{ $agency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('user-stocks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg me-2"></i>
                        Effacer
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des associations -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>
                Associations ({{ $userStocks->total() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if($userStocks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Stock</th>
                                <th>Agence</th>
                                <th>Créé par</th>
                                <th>Date création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userStocks as $userStock)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $userStock->user->first_name }} {{ $userStock->user->last_name }}</div>
                                                <small class="text-muted">{{ $userStock->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $userStock->stock->name }}</div>
                                            <small class="text-muted">Code: {{ $userStock->stock->code }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($userStock->agency)
                                            <span class="badge bg-info">{{ $userStock->agency->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($userStock->createdBy)
                                            {{ $userStock->createdBy->first_name }} {{ $userStock->createdBy->last_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $userStock->created_at->format('d/m/Y H:i') }}<br>
                                            <span class="text-muted">{{ $userStock->created_at->diffForHumans() }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('user-stocks.show', $userStock) }}"
                                               class="btn btn-outline-info" title="Détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="{{ route('user-stocks.destroy', $userStock) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Supprimer cette association ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Supprimer">
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
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-3">Aucune association trouvée</p>
                    <a href="{{ route('user-stocks.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>
                        Créer une association
                    </a>
                </div>
            @endif
        </div>

        @if($userStocks->hasPages())
            <div class="card-footer">
                {{ $userStocks->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Association en masse -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-collection me-2"></i>
                    Association en masse
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user-stocks.assign-multiple') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="bulk_user_id" class="form-label">Utilisateur *</label>
                            <select name="user_id" id="bulk_user_id" class="form-select" required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="bulk_agency_id" class="form-label">Agence</label>
                            <select name="agency_id" id="bulk_agency_id" class="form-select">
                                <option value="">Aucune agence</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Stocks à associer *</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            @foreach($stocks as $stock)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stock_ids[]"
                                           value="{{ $stock->id }}" id="stock_{{ $stock->id }}">
                                    <label class="form-check-label" for="stock_{{ $stock->id }}">
                                        <strong>{{ $stock->code }}</strong> - {{ $stock->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-2"></i>
                        Associer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endpush
