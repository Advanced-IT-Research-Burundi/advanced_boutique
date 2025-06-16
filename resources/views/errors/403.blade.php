@extends('layouts.error')

@section('title', 'Accès interdit')

@section('content')
    <div class="error-icon text-danger mb-4">
        <i class="bi bi-shield-x"></i>
    </div>

    <div class="error-code text-danger mb-3">403</div>

    <h1 class="h3 mb-3">Accès interdit</h1>

    <p class="lead text-muted mb-4">
        Vous n'avez pas les permissions nécessaires pour accéder à cette page.
    </p>

    <div class="alert alert-danger" role="alert">
        <i class="bi bi-lock me-2"></i>
        <strong>Accès restreint :</strong> Cette ressource nécessite des privilèges spéciaux.
    </div>

    <div class="mt-3">
        <small class="text-muted">
            Contactez votre administrateur si vous pensez que c'est une erreur.
        </small>
    </div>
@endsection
