@extends('layouts.error')

@section('title', 'Trop de requêtes')

@section('content')
    <div class="error-icon text-danger mb-4">
        <i class="bi bi-speedometer2"></i>
    </div>

    <div class="error-code text-danger mb-3">429</div>

    <h1 class="h3 mb-3">Trop de requêtes</h1>

    <p class="lead text-muted mb-4">
        Vous avez effectué trop de requêtes en peu de temps. Veuillez patienter avant de réessayer.
    </p>

    <div class="alert alert-warning" role="alert">
        <i class="bi bi-hourglass-split me-2"></i>
        <strong>Limite de débit atteinte :</strong> Attendez quelques minutes avant de continuer.
    </div>

    <div class="mt-3">
        <div class="progress mb-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated"
                 role="progressbar"
                 style="width: 100%"
                 aria-valuenow="100"
                 aria-valuemin="0"
                 aria-valuemax="100">
                Limitation en cours...
            </div>
        </div>

        <button onclick="setTimeout(() => location.reload(), 30000)" class="btn btn-outline-danger">
            <i class="bi bi-clock me-2"></i>
            Réessayer dans 30 secondes
        </button>
    </div>
@endsection
