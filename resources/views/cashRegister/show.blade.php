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
    @if(session('success'))
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
    @endif

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
                    <form action="{{ route('cash-registers.close', $cashRegister) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir fermer cette caisse ?')">
                        @csrf
                        @method('PATCH')
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

                                <dt class="col-sm-5">
                                    <i class="bi bi-box text-info me-1"></i>
                                    Stock:
                                </dt>
                                <dd class="col-sm-7">{{ $cashRegister->stock->name }}</dd>

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
                                <dd class="col-sm-7">{{ $cashRegister->opened_at->format('d/m/Y H:i') }}</dd>

                                @if($cashRegister->closed_at)
                                    <dt class="col-sm-5">
                                        <i class="bi bi-calendar-x text-danger me-1"></i>
                                        Fermeture:
                                    </dt>
                                    <dd class="col-sm-7">{{ $cashRegister->closed_at->format('d/m/Y H:i') }}</dd>
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
                                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} €
                                                </strong>
                                            </td>
                                            <td>{{ Str::limit($transaction->description, 30) }}</td>
                                            <td>
                                                @if($transaction->reference_id)
                                                    <span class="badge bg-info">#{{ $transaction->reference_id }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->createdBy->name ?? 'N/A' }}</td>
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
                            {{ number_format($cashRegister->opening_balance, 2) }} €
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-arrow-down text-success me-2"></i>
                            <strong>Total entrées:</strong>
                        </div>
                        <span class="badge bg-success fs-6">
                            +{{ number_format($totalIn, 2) }} €
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-arrow-up text-danger me-2"></i>
                            <strong>Total sorties:</strong>
                        </div>
                        <span class="badge bg-danger fs-6">
                            -{{ number_format($totalOut, 2) }} €
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-cash-stack text-primary me-2"></i>
                            <strong>Solde actuel:</strong>
                        </div>
                        <span class="badge bg-primary fs-5">
                            {{ number_format($currentBalance, 2) }} €
                        </span>
                    </div>

                    @if($cashRegister->closing_balance)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-lock text-warning me-2"></i>
                                <strong>Solde de fermeture:</strong>
                            </div>
                            <span class="badge bg-warning fs-6">
                                {{ number_format($cashRegister->closing_balance, 2) }} €
                            </span>
                        </div>
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
                                <i class="bi bi-
