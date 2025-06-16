@extends('layouts.error')

@section('title', 'Service indisponible')

@section('content')
    <div class="error-icon text-warning mb-4">
        <i class="bi bi-tools"></i>
    </div>

    <div class="error-code text-warning mb-3">503</div>

    <h1 class="h3 mb-3">Service temporairement indisponible</h1>

    <p class="lead text-muted mb-4">
        Le site est actuellement en maintenance. Nous serons bientôt de retour !
    </p>

    <div class="alert alert-warning" role="alert">
        <i class="bi bi-wrench me-2"></i>
        <strong>Maintenance en cours :</strong> Merci de votre patience pendant que nous améliorons nos services.
    </div>

    <div class="mt-3">
        <button onclick="location.reload()" class="btn btn-outline-warning">
            <i class="bi bi-arrow-clockwise me-2"></i>
            Réessayer
        </button>
    </div>

    <div class="mt-4">
        <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
        <p class="mt-2 text-muted small">Vérification de la disponibilité...</p>
    </div>
@endsection
