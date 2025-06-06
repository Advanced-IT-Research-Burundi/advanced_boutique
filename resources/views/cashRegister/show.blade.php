@extends('layouts.app')

@section('title', 'Détails de la caisse')

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
                <a href="{{ route('cash-registers.index') }}" class="text-decoration-none">
                    <i class="bi bi-cash-coin"></i> Caisses
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-eye"></i> Caisse #{{ $cashRegister->id }}
            </li>
        </ol>
    </nav>

    <!-- Messages d'alerte -->
    {{-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif --}}

    <!-- En-tête avec actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="bi bi-cash-coin text-primary me-2"></i>
                Caisse #{{ $cashRegister->id }}
                @switch($cashRegister->status)
                    @case('open')
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-unlock me-1"></i>
                            Ouverte
                        </span>
                        @break
                    @case('closed')
                        <span class="badge bg-danger fs-6">
                            <i class="bi bi-lock me-1"></i>
                            Fermée
                        </span>
                        @break
                    @case('suspended')
                        <span class="badge bg-warning fs-6">
                            <i class="bi bi-pause me-1"></i>
                            Suspendue
                        </span>
                        @break
                @endswitch
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('cash-registers.edit', $cashRegister) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i>
                    Modifier
                </a>
                @if($cashRegister->status === 'open')
                    <form action="{{ route('cash-register.close', $cashRegister) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir fermer cette caisse ?')">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-lock me-1"></i>
                            Fermer
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations générales -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">
                                    <i class="bi bi-person-circle text-primary me-1"></i>
                                    Utilisateur:
                                </dt>
                                <dd class="col-sm-7">
                                    <strong>{{ $cashRegister->user->name }}</strong><br>
                                    <small class="text-muted">{{ $cashRegister->user->email }}</small>
                                </dd>

                                @if($cashRegister->stock)
                                    <dt class="col-sm-5">
                                        <i class="bi bi-box text-info me-1"></i>
                                        Stock:
                                    </dt>
                                    <dd class="col-sm-7">{{ $cashRegister->stock->name }}</dd>
                                @endif

                                <dt class="col-sm-5">
                                    <i class="bi bi-building text-info me-1"></i>
                                    Agence:
                                </dt>
                                <dd class="col-sm-7">
                                    {{ $cashRegister->agency->name ?? 'Non assigné' }}
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">
                                    <i class="bi bi-calendar-check text-success me-1"></i>
                                    Ouverture:
                                </dt>
                                <dd class="col-sm-7">{{ \Carbon\Carbon::parse($cashRegister->opened_at)->format('d/m/Y H:i') }}</dd>

                                @if($cashRegister->closed_at)
                                    <dt class="col-sm-5">
                                        <i class="bi bi-calendar-x text-danger me-1"></i>
                                        Fermeture:
                                    </dt>
                                    <dd class="col-sm-7">{{ \Carbon\Carbon::parse($cashRegister->closed_at)->format('d/m/Y H:i') }}</dd>
                                @endif

                                <dt class="col-sm-5">
                                    <i class="bi bi-person-plus text-success me-1"></i>
                                    Créé par:
                                </dt>
                                <dd class="col-sm-7">
                                    {{ $cashRegister->createdBy->name ?? 'N/A' }}<br>
                                    <small class="text-muted">{{ $cashRegister->created_at->format('d/m/Y H:i') }}</small>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    @if($cashRegister->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <dt>
                                    <i class="bi bi-chat-text text-info me-1"></i>
                                    Description:
                                </dt>
                                <dd class="mt-2">
                                    <div class="alert alert-info">
                                        {{ $cashRegister->description }}
                                    </div>
                                </dd>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transactions -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Transactions ({{ $transactions->total() }})
                    </h5>
                    @if($cashRegister->status === 'open')
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                            <i class="bi bi-plus-circle me-1"></i>
                            Ajouter
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Montant</th>
                                        <th>Description</th>
                                        <th>Référence</th>
                                        <th>Utilisateur</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="text-muted small">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                @if($transaction->type === 'in')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-arrow-down me-1"></i>
                                                        Entrée
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-arrow-up me-1"></i>
                                                        Sortie
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="{{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} Fbu
                                                </strong>
                                            </td>
                                            <td>
                                                <span data-bs-toggle="tooltip" title="{{ $transaction->description }}">
                                                    {{ Str::limit($transaction->description, 30) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->reference_id)
                                                    <span class="badge bg-info">#{{ $transaction->reference_id }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->createdBy->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#transactionModal{{ $transaction->id }}"
                                                            title="Voir détails">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    @if($cashRegister->status === 'open' && $transaction->created_at->diffInHours() < 24)
                                                        <button class="btn btn-outline-warning btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTransactionModal{{ $transaction->id }}"
                                                                title="Modifier">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($transactions->hasPages())
                            <div class="card-footer bg-light">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Aucune transaction</h5>
                            <p class="text-muted">Cette caisse n'a pas encore de transactions.</p>
                            @if($cashRegister->status === 'open')
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Ajouter une première transaction
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calculator me-2"></i>
                        Résumé financier
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-cash text-success me-2"></i>
                            <strong>Solde d'ouverture:</strong>
                        </div>
                        <span class="badge bg-success fs-6">
                            {{ number_format($cashRegister->opening_balance, 2) }} Fbu
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-arrow-down text-success me-2"></i>
                            <strong>Total entrées:</strong>
                        </div>
                        <span class="badge bg-success fs-6">
                            +{{ number_format($totalIn, 2) }} Fbu
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-arrow-up text-danger me-2"></i>
                            <strong>Total sorties:</strong>
                        </div>
                        <span class="badge bg-danger fs-6">
                            -{{ number_format($totalOut, 2) }} Fbu
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-cash-stack text-primary me-2"></i>
                            <strong>Solde actuel:</strong>
                        </div>
                        <span class="badge bg-primary fs-5">
                            {{ number_format($currentBalance, 2) }} Fbu
                        </span>
                    </div>

                    @if($cashRegister->closing_balance !== null)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-lock text-warning me-2"></i>
                                <strong>Solde de fermeture:</strong>
                            </div>
                            <span class="badge bg-warning fs-6">
                                {{ number_format($cashRegister->closing_balance, 2) }} Fbu
                            </span>
                        </div>

                        @php
                            $difference = $cashRegister->closing_balance - $currentBalance;
                        @endphp
                        @if($difference != 0)
                            <div class="alert alert-{{ $difference > 0 ? 'info' : 'warning' }} mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Écart:</strong>
                                {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 2) }} Fbu
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                        <a href="{{ route('cash-registers.edit', $cashRegister) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil me-2"></i>
                            Modifier
                        </a>
                        @if($cashRegister->status !== 'closed')
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                <i class="bi bi-plus-circle me-2"></i>
                                Nouvelle transaction
                            </button>
                        @endif
                        {{-- <button class="btn btn-outline-info" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>
                            Imprimer
                        </button>
                        <a href="{{ route('cash-registers.export', $cashRegister) }}" class="btn btn-outline-success">
                            <i class="bi bi-download me-2"></i>
                            Exporter PDF
                        </a> --}}
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-success">{{ $transactions->where('type', 'in')->count() }}</h4>
                                <small class="text-muted">Entrées</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-danger">{{ $transactions->where('type', 'out')->count() }}</h4>
                            <small class="text-muted">Sorties</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h5 class="text-primary">{{ $transactions->count() }}</h5>
                        <small class="text-muted">Total transactions</small>
                    </div>
                    @if($cashRegister->opened_at)
                        <hr>
                        <div class="text-center">
                            <h6 class="text-info">
                                {{ \Carbon\Carbon::parse($cashRegister->opened_at)->diffInDays(now()) === 0 ? 'Aujourd\'hui' : \Carbon\Carbon::parse($cashRegister->opened_at)->diffInDays(now()) + 1 }}
                            </h6>
                            <small class="text-muted">Jours d'activité</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Transaction -->
@if($cashRegister->status === 'open')
<div class="modal fade" id="addTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cash-transactions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="cash_register_id" value="{{ $cashRegister->id }}">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvelle transaction
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Sélectionner...</option>
                            <option value="in">Entrée</option>
                            <option value="out">Sortie</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant (Fbu)</label>
                        <input type="number" class="form-control" id="amount" name="amount"
                               step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="reference_id" class="form-label">Référence (optionnel)</label>
                        <input type="number" class="form-control" id="reference_id" name="reference_id">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modals pour les détails des transactions -->
@foreach($transactions as $transaction)
<div class="modal fade" id="transactionModal{{ $transaction->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>
                    Détails de la transaction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Date:</dt>
                    <dd class="col-sm-8">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</dd>

                    <dt class="col-sm-4">Type:</dt>
                    <dd class="col-sm-8">
                        @if($transaction->type === 'in')
                            <span class="badge bg-success">Entrée</span>
                        @else
                            <span class="badge bg-danger">Sortie</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Montant:</dt>
                    <dd class="col-sm-8">
                        <strong class="{{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} Fbu
                        </strong>
                    </dd>

                    <dt class="col-sm-4">Description:</dt>
                    <dd class="col-sm-8">{{ $transaction->description }}</dd>

                    @if($transaction->reference_id)
                        <dt class="col-sm-4">Référence:</dt>
                        <dd class="col-sm-8">#{{ $transaction->reference_id }}</dd>
                    @endif

                    <dt class="col-sm-4">Créé par:</dt>
                    <dd class="col-sm-8">{{ $transaction->createdBy->name ?? 'N/A' }}</dd>

                    @if($transaction->agency)
                        <dt class="col-sm-4">Agence:</dt>
                        <dd class="col-sm-8">{{ $transaction->agency->name }}</dd>
                    @endif
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Auto-refresh si la caisse est ouverte
    @if($cashRegister->status === 'open')
        setInterval(function() {
            // Recharger seulement si pas de modal ouvert
            if (!document.querySelector('.modal.show')) {
                location.reload();
            }
        }, 60000); // Refresh toutes les minutes
    @endif
</script>
@endpush

@push('styles')
<style>
    @media print {
        .btn, .breadcrumb, .alert, .modal {
            display: none !important;
        }
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
    }

    .badge {
        font-size: 0.75em;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endpush
