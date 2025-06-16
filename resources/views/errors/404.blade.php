@extends('layouts.error')

@section('title', 'Page non trouvée')

@section('content')
    <div class="error-icon text-warning mb-4">
        <i class="bi bi-exclamation-triangle"></i>
    </div>

    <div class="error-code text-warning mb-3">404</div>

    <h1 class="h3 mb-3">Page non trouvée</h1>

    <p class="lead text-muted mb-4">
        Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
    </p>

    <div class="alert alert-light" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        Vérifiez l'URL ou utilisez les liens de navigation pour continuer.
    </div>
@endsection
