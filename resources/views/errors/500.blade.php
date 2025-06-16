@extends('layouts.error')

@section('title', 'Erreur serveur')

@section('content')
    <div class="error-icon text-danger mb-4">
        <i class="bi bi-exclamation-octagon"></i>
    </div>

    <div class="error-code text-danger mb-3">500</div>

    <h1 class="h3 mb-3">Erreur interne du serveur</h1>

    <p class="lead text-muted mb-4">
        Une erreur s'est produite sur le serveur. Nous travaillons à résoudre ce problème.
    </p>

    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Erreur temporaire :</strong> Veuillez réessayer dans quelques minutes.
    </div>

    <div class="mt-3">
        <button onclick="location.reload()" class="btn btn-outline-primary">
            <i class="bi bi-arrow-clockwise me-2"></i>
            Actualiser la page
        </button>
    </div>
@endsection
