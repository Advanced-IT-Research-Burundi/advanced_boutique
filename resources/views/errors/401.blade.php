@extends('layouts.error')

@section('title', 'Non autorisé')

@section('content')
    <div class="error-icon text-warning mb-4">
        <i class="bi bi-person-x"></i>
    </div>

    <div class="error-code text-warning mb-3">401</div>

    <h1 class="h3 mb-3">Authentification requise</h1>

    <p class="lead text-muted mb-4">
        Vous devez vous connecter pour accéder à cette page.
    </p>

    <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Connexion requise :</strong> Veuillez vous identifier pour continuer.
    </div>

    <div class="mt-3">
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Se connecter
        </a>
    </div>
@endsection
