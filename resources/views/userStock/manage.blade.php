@extends('layouts.app')

@section('title', 'Gestion des Stocks - ' . $user->first_name . ' ' . $user->last_name)

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
            <li class="breadcrumb-item active" aria-current="page">
                Gestion des Stocks
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-boxes me-2"></i>
                Gestion des Stocks
            </h1>
            <p class="text-muted mb-0">
                Utilisateur: <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour au Profil
            </a>
            <a href="{{ route('users.stocks.history', $user) }}" class="btn btn-success">
                <i class="bi bi-clock-history me-2"></i>
                Historiques
            </a>
        </div>
    </div>


    <div class="row">
        <!-- User Info Card -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Informations Utilisateur
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}"
                             alt="Photo de profil"
                             class="rounded-circle mb-3"
                             width="80" height="80"
                             style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mx-auto mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person" style="font-size: 2rem;"></i>
                        </div>
                    @endif

                    <h5 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-info">{{ ucfirst($user->role) }}</span>

                    @if($user->agency)
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $user->agency->name }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stock Statistics -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Stocks assignés:</span>
                        <span class="badge bg-success">{{ $assignedStocks->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Stocks disponibles:</span>
                        <span class="badge bg-secondary">{{ $availableStocks->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Management -->
        <div class="col-md-8">
            <!-- Assigned Stocks -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        Stocks Assignés ({{ $assignedStocks->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($assignedStocks->count() > 0)
                        <div class="row">
                            @foreach($assignedStocks as $stock)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $stock->name }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $stock->agency ? $stock->agency->name : 'Aucune agence' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                Assigné le {{ $stock->pivot->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                        <form action="{{ route('users.stocks.detach', [$user, $stock]) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir désassigner ce stock ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Désassigner">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Aucun stock assigné à cet utilisateur</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Stocks -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Stocks Disponibles ({{ $availableStocks->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($availableStocks->count() > 0)
                        <form action="{{ route('users.stocks.attach', $user) }}" method="POST">
                            @csrf
                            <div class="row">
                                @foreach($availableStocks as $stock)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="stock_ids[]"
                                                       value="{{ $stock->id }}"
                                                       id="stock_{{ $stock->id }}">
                                                <label class="form-check-label w-100" for="stock_{{ $stock->id }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">{{ $stock->name }}</h6>
                                                            <small class="text-muted">
                                                                <i class="bi bi-geo-alt me-1"></i>
                                                                {{ $stock->agency ? $stock->agency->name : 'Aucune agence' }}
                                                            </small>
                                                            @if($stock->description)
                                                                <br>
                                                                <small class="text-muted">{{ Str::limit($stock->description, 50) }}</small>
                                                            @endif
                                                        </div>
                                                        <span class="badge bg-primary">{{ $stock->agency->name ?? 'Standard' }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">
                                        <i class="bi bi-check-all me-1"></i>
                                        Tout sélectionner
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="selectNone()">
                                        <i class="bi bi-x-square me-1"></i>
                                        Tout désélectionner
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Assigner les stocks sélectionnés
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-check-all" style="font-size: 3rem;"></i>
                            <p class="mt-2">Tous les stocks sont déjà assignés à cet utilisateur</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('input[name="stock_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
}

function selectNone() {
    const checkboxes = document.querySelectorAll('input[name="stock_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}
</script>
@endsection
