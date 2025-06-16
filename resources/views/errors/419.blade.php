@extends('layouts.error')

@section('title', 'Session expirée')

@section('content')
    <div class="error-icon text-warning mb-4">
        <i class="bi bi-clock-history"></i>
    </div>

    <div class="error-code text-warning mb-3">419</div>

    <h1 class="h3 mb-3">Session expirée</h1>

    <p class="lead text-muted mb-4">
        Votre session a expiré pour des raisons de sécurité. Veuillez actualiser la page et réessayer.
    </p>

    <div class="alert alert-warning" role="alert">
        <i class="bi bi-shield-exclamation me-2"></i>
        <strong>Token CSRF expiré :</strong> Cette erreur se produit lorsque vous restez inactif trop longtemps.
    </div>

    <div class="mt-3">
        <button onclick="location.reload()" class="btn btn-warning">
            <i class="bi bi-arrow-clockwise me-2"></i>
            Actualiser et réessayer
        </button>
    </div>
@endsection
