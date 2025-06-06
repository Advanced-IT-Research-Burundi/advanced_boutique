@extends('layouts.app')

@section('title', 'Nouvelle transaction de caisse')

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
            <li class="breadcrumb-item active" aria-current="page">
                Nouvelle transaction
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvelle transaction de caisse
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cash-transactions.store') }}" method="POST">
                        @csrf

                        @if($cashRegister)
                            <input type="hidden" name="cash_register_id" value="{{ $cashRegister->id }}">
                            <input type="hidden" name="redirect_to_register" value="1">

                            <!-- Caisse sélectionnée -->
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Transaction pour la <strong>Caisse #{{ $cashRegister->id }}</strong>
                                de <strong>{{ $cashRegister->user->name }}</strong>
                            </div>
                        @else
                            <!-- Sélection de la caisse -->
                            <div class="mb-3">
                                <label for="cash_register_id" class="form-label">
                                    <i class="bi bi-cash-coin me-1"></i>
                                    Caisse <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('cash_register_id') is-invalid @enderror"
                                        id="cash_register_id"
                                        name="cash_register_id"
                                        required>
                                    <option value="">Sélectionnez une caisse</option>
                                    @foreach($cashRegisters as $register)
                                        <option value="{{ $register->id }}" {{ old('cash_register_id') == $register->id ? 'selected' : '' }}>
                                            Caisse #{{ $register->id }} - {{ $register->user->name }}
                                            ({{ $register->status === 'open' ? 'Ouverte' : 'Fermée' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('cash_register_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="row">
                            <!-- Type de transaction -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">
                                        <i class="bi bi-arrow-left-right me-1"></i>
                                        Type de transaction <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror"
                                            id="type"
                                            name="type"
                                            required>
                                        <option value="">Sélectionnez le type</option>
                                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>
                                            <i class="bi bi-arrow-down-circle"></i> Entrée
                                        </option>
                                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>
                                            <i class="bi bi-arrow-up-circle"></i> Sortie
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Montant -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">
                                        <i class="bi bi-cash me-1"></i>
                                        Montant (Fbu) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           id="amount"
                                           name="amount"
                                           value="{{ old('amount') }}"
                                           step="0.01"
                                           min="0.01"
                                           placeholder="0.00"
                                           required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-text-paragraph me-1"></i>
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Décrivez la transaction..."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Référence -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_id" class="form-label">
                                        <i class="bi bi-hash me-1"></i>
                                        Référence
                                    </label>
                                    <input type="number"
                                           class="form-control @error('reference_id') is-invalid @enderror"
                                           id="reference_id"
                                           name="reference_id"
                                           value="{{ old('reference_id') }}"
                                           placeholder="Numéro de référence (optionnel)">
                                    @error('reference_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Agence -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="agency_id" class="form-label">
                                        <i class="bi bi-building me-1"></i>
                                        Agence
                                    </label>
                                    <select class="form-select @error('agency_id') is-invalid @enderror"
                                            id="agency_id"
                                            name="agency_id">
                                        <option value="">Sélectionnez une agence (optionnel)</option>
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                                                {{ $agency->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('agency_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ $cashRegister ? route('cash-registers.show', $cashRegister) : route('cash-transactions.index') }}"
                               class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer la transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide contextuelle -->
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Aide
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Entrée :</strong> Ajout d'argent dans la caisse (vente, dépôt, etc.)</li>
                        <li><strong>Sortie :</strong> Retrait d'argent de la caisse (achat, retrait, etc.)</li>
                        <li><strong>Référence :</strong> Numéro de facture, reçu ou document associé</li>
                        <li><strong>Description :</strong> Détails de la transaction (obligatoire)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const amountInput = document.getElementById('amount');

    // Changer la couleur en fonction du type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'in') {
            amountInput.classList.remove('border-danger');
            amountInput.classList.add('border-success');
        } else if (this.value === 'out') {
            amountInput.classList.remove('border-success');
            amountInput.classList.add('border-danger');
        } else {
            amountInput.classList.remove('border-success', 'border-danger');
        }
    });
});
</script>
@endsection
