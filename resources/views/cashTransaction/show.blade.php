@extends('layouts.app')

@section('title', 'Détails de la transaction')

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
            <li class="breadcrumb-item active">
                Transaction #{{ $cashTransaction->id }}
            </li>
        </ol>
    </nav>

    <div class="row">
        <!-- Détails de la transaction -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Détails de la transaction
                    </h5>
                    <div class="btn-group">
                        @if($cashTransaction->cashRegister->status === 'open')
                            <a href="{{ route('cash-transactions.edit', $cashTransaction) }}"
                               class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil me-1"></i>
                                Modifier
                            </a>
                            <form action="{{ route('cash-transactions.cancel', $cashTransaction) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette transaction ?')">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Annuler
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('cash-transactions.destroy', $cashTransaction) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informations générales</h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ID Transaction</label>
                                <div class="text-muted">#{{ $cashTransaction->id }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Type</label>
                                <div>
                                    @if($cashTransaction->type === 'in')
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-arrow-down me-1"></i>
                                            Entrée
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-arrow-up me-1"></i>
                                            Sortie
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant</label>
                                <div class="fs-4 fw-bold {{ $cashTransaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $cashTransaction->type === 'in' ? '+' : '-' }}{{ number_format($cashTransaction->amount, 2) }} Fbu
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <div class="text-muted">{{ $cashTransaction->description }}</div>
                            </div>

                            @if($cashTransaction->reference_id)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Référence</label>
                                    <div class="text-muted">#{{ $cashTransaction->reference_id }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informations de caisse</h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Caisse</label>
                                <div>
                                    <a href="{{ route('cash-registers.show', $cashTransaction->cashRegister) }}"
                                       class="text-decoration-none">
                                        <i class="bi bi-cash-coin text-primary me-1"></i>
                                        Caisse #{{ $cashTransaction->cashRegister->id }}
                                    </a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Utilisateur de la caisse</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    <div>
                                        <div>{{ $cashTransaction->cashRegister->user->name }}</div>
                                        <small class="text-muted">{{ $cashTransaction->cashRegister->user->email }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Solde après transaction</label>
                                <div class="fs-5 fw-bold text-primary">
                                    {{ number_format($balanceAtTransaction, 2) }} Fbu
                                </div>
                            </div>

                            @if($cashTransaction->agency)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Agence</label>
                                    <div>
                                        <i class="bi bi-building text-info me-1"></i>
                                        {{ $cashTransaction->agency->name }}
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold">Créé par</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle text-success me-2"></i>
                                    {{ $cashTransaction->createdBy->name ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Date et heure</label>
                                <div class="text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $cashTransaction->created_at->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation et actions -->
        <div class="col-lg-4">
            <!-- Navigation -->
            @if($previousTransaction || $nextTransaction)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-arrow-left-right me-2"></i>
                            Navigation
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($previousTransaction)
                            <div class="mb-2">
                                <a href="{{ route('cash-transactions.show', $previousTransaction) }}"
                                   class="btn btn-sm btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Transaction précédente
                                </a>
                            </div>
                        @endif
                        @if($nextTransaction)
                            <div>
                                <a href="{{ route('cash-transactions.show', $nextTransaction) }}"
                                   class="btn btn-sm btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-right me-1"></i>
                                    Transaction suivante
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions rapides -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('cash-transactions.create', ['cash_register_id' => $cashTransaction->cash_register_id]) }}"
                           class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Nouvelle transaction
                        </a>
                        <a href="{{ route('cash-registers.show', $cashTransaction->cashRegister) }}"
                           class="btn btn-outline-info">
                            <i class="bi bi-cash-coin me-1"></i>
                            Voir la caisse
                        </a>
                        <a href="{{ route('cash-transactions.index', ['cash_register_id' => $cashTransaction->cash_register_id]) }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-list me-1"></i>
                            Toutes les transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
