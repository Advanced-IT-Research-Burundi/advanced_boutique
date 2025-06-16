@extends('layouts.error')

@section('title', 'Erreur')

@section('content')
    <div class="error-icon text-secondary mb-4">
        <i class="bi bi-exclamation-circle"></i>
    </div>

    <div class="error-code text-secondary mb-3">
        {{ $exception->getStatusCode() ?? '???' }}
    </div>

    <h1 class="h3 mb-3">Une erreur s'est produite</h1>

    <p class="lead text-muted mb-4">
        {{ $exception->getMessage() ?? 'Une erreur inattendue s\'est produite.' }}
    </p>

    <div class="alert alert-secondary" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Erreur système :</strong> Nos équipes ont été notifiées de ce problème.
    </div>

    @if(app()->hasDebugModeEnabled() && isset($exception))
        <div class="mt-4">
            <details class="text-start">
                <summary class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-bug me-2"></i>
                    Détails techniques (Debug)
                </summary>
                <div class="mt-3 p-3 bg-light rounded">
                    <small>
                        <strong>Fichier :</strong> {{ $exception->getFile() }}<br>
                        <strong>Ligne :</strong> {{ $exception->getLine() }}<br>
                        <strong>Message :</strong> {{ $exception->getMessage() }}
                    </small>
                </div>
            </details>
        </div>
    @endif
@endsection
