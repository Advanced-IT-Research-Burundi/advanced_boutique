@extends('layouts.app')

@section('title', 'Modifier l\'Agence')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-between ">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('agencies.index') }}" class="text-decoration-none">
                    <i class="bi bi-building"></i> Agences
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('agencies.show', $agency) }}" class="text-decoration-none">
                    <i class="bi bi-eye"></i> {{ $agency->name }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil-square"></i> Modifier
            </li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('agencies.show', $agency) }}" class="btn btn-outline-info me-2">
                <i class="bi bi-eye me-2"></i>
                Voir l'agence
            </a>
        </div>
    </nav>

    <!-- Alert for last modification -->
    <div class="alert alert-light border-start border-warning border-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-clock-history text-warning me-3 fs-5"></i>
            <div>
                <strong>Dernière modification :</strong>
                {{ $agency->updated_at->format('d/m/Y à H:i') }}
                @if($agency->updated_at->diffInDays(now()) === 0)
                    <span class="badge bg-success ms-2">Aujourd'hui</span>
                @elseif($agency->updated_at->diffInDays(now()) === 1)
                    <span class="badge bg-info ms-2">Hier</span>
                @else
                    <span class="badge bg-secondary ms-2">{{ $agency->updated_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill text-danger me-3 fs-5 mt-1"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-2">Erreurs de validation détectées :</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('agencies.update', $agency) }}" method="POST" novalidate>
        @csrf
        @method('PUT')
        @include('agency._form')
    </form>

    <!-- Danger Zone -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Zone de Danger
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-danger">Supprimer cette agence</h6>
                            <p class="text-muted mb-0">
                                Cette action est irréversible. Toutes les données associées à cette agence seront perdues.
                                Assurez-vous que cette agence n'est utilisée nulle part avant de la supprimer.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button"
                                    class="btn btn-outline-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                <i class="bi bi-trash me-2"></i>
                                Supprimer l'agence
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-trash display-1 text-danger"></i>
                </div>
                <p class="text-center">
                    Êtes-vous absolument sûr de vouloir supprimer l'agence
                    <strong>"{{ $agency->name }}"</strong> ?
                </p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible et supprimera définitivement :
                    <ul class="mb-0 mt-2">
                        <li>Toutes les informations de l'agence</li>
                        <li>Les associations avec les utilisateurs</li>
                        <li>L'historique des modifications</li>
                    </ul>
                </div>
                <p class="text-muted mb-0">
                    Pour confirmer, tapez <strong>{{ $agency->code }}</strong> dans le champ ci-dessous :
                </p>
                <input type="text"
                       class="form-control mt-2"
                       id="confirmCode"
                       placeholder="Tapez {{ $agency->code }} pour confirmer">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Annuler
                </button>
                <form action="{{ route('agencies.destroy', $agency) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn btn-danger"
                            id="confirmDeleteBtn"
                            disabled>
                        <i class="bi bi-trash me-2"></i>
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation de suppression
    const confirmInput = document.getElementById('confirmCode');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const expectedCode = '{{ $agency->code }}';

    if (confirmInput && confirmBtn) {
        confirmInput.addEventListener('input', function() {
            if (this.value === expectedCode) {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('btn-danger');
                confirmBtn.classList.add('btn-success');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.remove('btn-success');
                confirmBtn.classList.add('btn-danger');
            }
        });
    }
});
</script>
@endpush
@endsection
