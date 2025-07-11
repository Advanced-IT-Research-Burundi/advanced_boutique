@extends('layouts.app')

@section('title', 'Modifier une caisse')

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
            <li class="breadcrumb-item">
                <a href="{{ route('cash-registers.show', $cashRegister) }}" class="text-decoration-none">
                    <i class="bi bi-eye"></i> Caisse #{{ $cashRegister->id }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil"></i> Modifier
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

    <!-- Main Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Modifier la caisse #{{ $cashRegister->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informations actuelles -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            Informations actuelles
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Utilisateur:</strong> {{ $cashRegister->user->name }}<br>
                                <strong>Statut:</strong>
                                @switch($cashRegister->status)
                                    @case('open')
                                        <span class="badge bg-success">Ouverte</span>
                                        @break
                                    @case('closed')
                                        <span class="badge bg-danger">Fermée</span>
                                        @break
                                    @case('suspended')
                                        <span class="badge bg-warning">Suspendue</span>
                                        @break
                                @endswitch
                            </div>
                            <div class="col-md-6">
                                <strong>Créée le:</strong> {{ $cashRegister->created_at->format('d/m/Y H:i') }}<br>
                                <strong>Par:</strong> {{ $cashRegister->createdBy->name ?? 'N/A' }}<br>
                                @if($cashRegister->agency)
                                    <strong>Agence:</strong> {{ $cashRegister->agency->name }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire -->
                    <form action="{{ route('cash-registers.update', $cashRegister) }}" method="POST" id="cashRegisterForm">
                        @csrf
                        @method('PUT')

                        @include('cashRegister._form')

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('cash-registers.show', $cashRegister) }}" class="btn btn-outline-info me-2">
                                            <i class="bi bi-eye me-2"></i>
                                            Voir détails
                                        </a>
                                        <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>
                                            Retour à la liste
                                        </a>
                                    </div>
                                    <div>
                                        <button type="reset" class="btn btn-outline-warning me-2">
                                            <i class="bi bi-arrow-clockwise me-2"></i>
                                            Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Mettre à jour
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
           </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.getElementById('cashRegisterForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let hasError = false;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                hasError = true;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });

    // Validation des dates
    const openedAt = document.getElementById('opened_at');
    const closedAt = document.getElementById('closed_at');

    if (openedAt && closedAt) {
        closedAt.addEventListener('change', function() {
            if (this.value && openedAt.value && new Date(this.value) <= new Date(openedAt.value)) {
                alert('La date de fermeture doit être postérieure à la date d\'ouverture.');
                this.value = '';
            }
        });
    }
});
</script>
@endsection
