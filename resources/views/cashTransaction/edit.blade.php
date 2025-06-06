@extends('layouts.app')

@section('title', 'Modifier la transaction')

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
                <a href="{{ route('cash-transactions.index') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left-right"></i> Transactions
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('cash-transactions.show', $cashTransaction) }}" class="text-decoration-none">
                    Transaction #{{ $cashTransaction->id }}
                </a>
            </li>
            <li class="breadcrumb-item active">
                Modifier
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Modifier la transaction #{{ $cashTransaction->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informations de la caisse -->
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <div>
                            <strong>Caisse:</strong> #{{ $cashTransaction->cashRegister->id }} -
                            {{ $cashTransaction->cashRegister->user->name }}
                            <br>
                            <small class="text-muted">
                                Statut: {{ ucfirst($cashTransaction->cashRegister->status) }} |
                                Solde d'ouverture: {{ number_format($cashTransaction->cashRegister->opening_balance, 2) }} Fbu
                            </small>
                        </div>
                    </div>

                    <form action="{{ route('cash-transactions.update', $cashTransaction) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Type de transaction -->
                                <div class="mb-4">
                                    <label for="type" class="form-label fw-bold">
                                        <i class="bi bi-arrow-left-right me-1"></i>
                                        Type de transaction <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror"
                                            id="type" name="type" required>
                                        <option value="">Sélectionner le type</option>
                                        <option value="in" {{ old('type', $cashTransaction->type) == 'in' ? 'selected' : '' }}>
                                            <i class="bi bi-arrow-down"></i> Entrée
                                        </option>
                                        <option value="out" {{ old('type', $cashTransaction->type) == 'out' ? 'selected' : '' }}>
                                            <i class="bi bi-arrow-up"></i> Sortie
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Montant -->
                                <div class="mb-4">
                                    <label for="amount" class="form-label fw-bold">
                                        <i class="bi bi-cash me-1"></i>
                                        Montant (Fbu) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Fbu</span>
                                        <input type="number"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               id="amount"
                                               name="amount"
                                               value="{{ old('amount', $cashTransaction->amount) }}"
                                               min="0.01"
                                               step="0.01"
                                               required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Référence (optionnelle) -->
                                <div class="mb-4">
                                    <label for="reference_id" class="form-label fw-bold">
                                        <i class="bi bi-tag me-1"></i>
                                        Référence
                                    </label>
                                    <input type="number"
                                           class="form-control @error('reference_id') is-invalid @enderror"
                                           id="reference_id"
                                           name="reference_id"
                                           value="{{ old('reference_id', $cashTransaction->reference_id) }}"
                                           placeholder="Numéro de référence (optionnel)">
                                    @error('reference_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Numéro de référence pour lier cette transaction à un document externe
                                    </div>
                                </div>

                                <!-- Agence (si applicable) -->
                                @if($cashTransaction->agency)
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-building me-1"></i>
                                            Agence
                                        </label>
                                        <div class="form-control-plaintext">
                                            {{ $cashTransaction->agency->name }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">
                                <i class="bi bi-card-text me-1"></i>
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      maxlength="1000"
                                      required
                                      placeholder="Description détaillée de la transaction">{{ old('description', $cashTransaction->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Maximum 1000 caractères
                            </div>
                        </div>

                        <!-- Aperçu du changement -->
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Attention:</strong> La modification de cette transaction affectera le solde de la caisse.
                                Assurez-vous que les informations sont correctes avant de confirmer.
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('cash-transactions.show', $cashTransaction) }}"
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Annuler
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Modifier la transaction
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations importantes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Règles de modification</h6>
                            <ul class="list-unstyled small text-muted">
                                <li><i class="bi bi-check-circle text-success me-1"></i> La caisse doit être ouverte</li>
                                <li><i class="bi bi-check-circle text-success me-1"></i> Transaction de moins de 24h</li>
                                <li><i class="bi bi-check-circle text-success me-1"></i> Permissions requises</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Détails originaux</h6>
                            <ul class="list-unstyled small text-muted">
                                <li><strong>Type:</strong> {{ $cashTransaction->type === 'in' ? 'Entrée' : 'Sortie' }}</li>
                                <li><strong>Montant:</strong> {{ number_format($cashTransaction->amount, 2) }} Fbu</li>
                                <li><strong>Créé le:</strong> {{ $cashTransaction->created_at->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus sur le premier champ
    document.getElementById('type').focus();

    // Validation côté client
    const form = document.querySelector('form');
    const typeSelect = document.getElementById('type');
    const amountInput = document.getElementById('amount');

    form.addEventListener('submit', function(e) {
        if (!typeSelect.value || !amountInput.value || parseFloat(amountInput.value) <= 0) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires avec des valeurs valides.');
        }
    });

    // Formatage automatique du montant
    amountInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
});
</script>
@endsection
