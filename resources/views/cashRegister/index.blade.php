@extends('layouts.app')

@section('title', 'Créer une caisse')

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
                <i class="bi bi-plus-circle"></i> Créer
            </li>
        </ol>
    </nav>

    <!-- Main Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer une nouvelle caisse
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire -->
                    <form action="{{ route('cash-registers.store') }}" method="POST" id="cashRegisterForm">
                        @csrf

                        @include('cashRegister._form')

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Retour à la liste
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-warning me-2">
                                            <i class="bi bi-arrow-clockwise me-2"></i>
                                            Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Créer la caisse
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
    // Définir la date d'ouverture par défaut à maintenant
    const openedAtInput = document.getElementById('opened_at');
    if (openedAtInput && !openedAtInput.value) {
        openedAtInput.value = new Date().toISOString().slice(0, 16);
    }

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
});
</script>
@endsection
