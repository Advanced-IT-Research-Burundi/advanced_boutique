@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Détails du client
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                            <li class="breadcrumb-item active">{{ $client->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> Modifier
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-1"></i>
                            Supprimer
                        </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-person-badge me-1"></i>
                                Type de patient
                            </label>
                            <div class="fw-bold">
                                @if($client->patient_type === 'physique')
                                    <span class="badge bg-success">
                                        <i class="bi bi-person me-1"></i>
                                        Personne physique
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-building me-1"></i>
                                        Personne morale
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($client->nif)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-card-text me-1"></i>
                                NIF
                            </label>
                            <div class="fw-bold">{{ $client->nif }}</div>
                        </div>
                        @endif

                        @if($client->societe)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-building me-1"></i>
                                Société
                            </label>
                            <div class="fw-bold">{{ $client->societe }}</div>
                        </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-person me-1"></i>
                                Nom
                            </label>
                            <div class="fw-bold">{{ $client->name }}</div>
                        </div>

                        @if($client->first_name)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-person me-1"></i>
                                Prénom
                            </label>
                            <div class="fw-bold">{{ $client->first_name }}</div>
                        </div>
                        @endif

                        @if($client->last_name)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-person me-1"></i>
                                Nom de famille
                            </label>
                            <div class="fw-bold">{{ $client->last_name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations de contact -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-telephone me-2"></i>
                        Informations de contact
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($client->phone)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-telephone me-1"></i>
                                Téléphone
                            </label>
                            <div class="fw-bold">
                                <a href="tel:{{ $client->phone }}" class="text-decoration-none">
                                    {{ $client->phone }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($client->email)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-envelope me-1"></i>
                                Email
                            </label>
                            <div class="fw-bold">
                                <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                    {{ $client->email }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($client->address)
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">
                                <i class="bi bi-geo-alt me-1"></i>
                                Adresse
                            </label>
                            <div class="fw-bold">{{ $client->address }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations financières et système -->
        <div class="col-lg-4">
            <!-- Solde -->
            {{-- <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-wallet2 me-2"></i>
                        Solde
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-6 fw-bold
                        @if($client->balance > 0) text-success
                        @elseif($client->balance < 0) text-danger
                        @else text-muted @endif">
                        {{ number_format($client->balance, 2) }} FR
                    </div>
                    @if($client->balance > 0)
                        <small class="text-success">
                            <i class="bi bi-arrow-up-circle me-1"></i>
                            Crédit client
                        </small>
                    @elseif($client->balance < 0)
                        <small class="text-danger">
                            <i class="bi bi-arrow-down-circle me-1"></i>
                            Dette client
                        </small>
                    @else
                        <small class="text-muted">
                            <i class="bi bi-dash-circle me-1"></i>
                            Solde neutre
                        </small>
                    @endif
                </div>
            </div> --}}

            <!-- Informations système -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Informations système
                    </h5>
                </div>
                <div class="card-body">
                    @if($client->agency)
                    <div class="mb-3">
                        <label class="form-label text-muted">
                            <i class="bi bi-building me-1"></i>
                            Agence
                        </label>
                        <div class="fw-bold">{{ $client->agency->name ?? 'N/A' }}</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted">
                            <i class="bi bi-person-plus me-1"></i>
                            Créé par
                        </label>
                        <div class="fw-bold">{{ $client->creator->name ?? 'N/A' }}</div>
                    </div>

                    @if($client->user)
                    <div class="mb-3">
                        <label class="form-label text-muted">
                            <i class="bi bi-person-check me-1"></i>
                            Utilisateur assigné
                        </label>
                        <div class="fw-bold">{{ $client->user->name }}</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Date de création
                        </label>
                        <div class="fw-bold">{{ $client->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">
                            <i class="bi bi-calendar-event me-1"></i>
                            Dernière modification
                        </label>
                        <div class="fw-bold">{{ $client->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce client ?</p>
                <p class="text-muted">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>
                    Annuler
                </button>
                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
