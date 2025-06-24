@extends('layouts.app')

@section('title', 'Nouvelle Association Utilisateur-Stock')

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
                <a href="{{ route('user-stocks.index') }}" class="text-decoration-none">
                    <i class="bi bi-link-45deg"></i> Associations
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Nouvelle Association
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-plus-circle me-2"></i>
                Nouvelle Association
            </h1>
            <p class="text-muted mb-0">Associer un utilisateur à un stock</p>
        </div>
        <a href="{{ route('user-stocks.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <!-- Alerts -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulaire Principal -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-form me-2"></i>
                        Informations de l'Association
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('user-stocks.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="user_id" class="form-label">
                                <i class="bi bi-person me-1"></i>
                                Utilisateur *
                            </label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock_id" class="form-label">
                                <i class="bi bi-box me-1"></i>
                                Stock *
                            </label>
                            <select name="stock_id" id="stock_id" class="form-select @error('stock_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un stock</option>
                                @foreach($stocks as $stock)
                                    <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}
                                            data-code="{{ $stock->code }}" data-description="{{ $stock->description }}">
                                        {{ $stock->code }} - {{ $stock->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Aperçu du stock sélectionné -->
                            <div id="stock-preview" class="mt-2 p-3 bg-light border rounded d-none">
                                <h6 class="mb-2">Aperçu du stock :</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Code :</strong> <span id="preview-code"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Nom :</strong> <span id="preview-name"></span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Description :</strong><br>
                                    <span id="preview-description" class="text-muted"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="agency_id" class="form-label">
                                <i class="bi bi-building me-1"></i>
                                Agence (optionnel)
                            </label>
                            <select name="agency_id" id="agency_id" class="form-select @error('agency_id') is-invalid @enderror">
                                <option value="">Aucune agence spécifique</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('agency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Sélectionnez une agence si cette association est spécifique à une agence
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('user-stocks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>
                                Créer l'Association
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panneau d'aide -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Aide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="bi bi-lightbulb me-1"></i>
                            Conseils
                        </h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Vérifiez que l'utilisateur n'est pas déjà associé à ce stock
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                L'agence est optionnelle et permet de spécifier le contexte
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Utilisez l'association en masse pour plusieurs stocks
                            </li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Attention
                        </h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger me-1"></i>
                                Une même association ne peut pas être créée deux fois
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger me-1"></i>
                                Vérifiez les permissions de l'utilisateur sur le stock
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-light">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        <strong>Note :</strong> Cette association permettra à l'utilisateur d'accéder au stock sélectionné selon ses permissions.
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $users->count() }}</h4>
                                <small class="text-muted">Utilisateurs</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1">{{ $stocks->count() }}</h4>
                            <small class="text-muted">Stocks</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h4 class="text-info mb-1">{{ $agencies->count() }}</h4>
                        <small class="text-muted">Agences disponibles</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockSelect = document.getElementById('stock_id');
    const stockPreview = document.getElementById('stock-preview');
    const previewCode = document.getElementById('preview-code');
    const previewName = document.getElementById('preview-name');
    const previewDescription = document.getElementById('preview-description');

    stockSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            const code = selectedOption.dataset.code;
            const name = selectedOption.textContent.split(' - ')[1];
            const description = selectedOption.dataset.description || 'Aucune description disponible';

            previewCode.textContent = code;
            previewName.textContent = name;
            previewDescription.textContent = description;

            stockPreview.classList.remove('d-none');
        } else {
            stockPreview.classList.add('d-none');
        }
    });

    // Validation côté client pour éviter les doublons
    const userSelect = document.getElementById('user_id');
    const form = document.querySelector('form');

    form.addEventListener('submit', function(e) {
        if (!userSelect.value || !stockSelect.value) {
            e.preventDefault();
            alert('Veuillez sélectionner un utilisateur et un stock.');
            return false;
        }

        // Ici, vous pourriez ajouter une vérification AJAX pour les doublons
    });
});
</script>
@endpush
