@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle me-2 fs-4"></i>
                        <h4 class="mb-0">Profil Utilisateur</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Section Photo de Profil -->
                        <div class="col-md-4 col-lg-3">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                             alt="Photo de profil"
                                             class="rounded-circle border border-3 border-primary shadow"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle border border-3 border-secondary bg-light d-flex align-items-center justify-content-center shadow"
                                             style="width: 150px; height: 150px;">
                                            <i class="bi bi-person-fill text-secondary" style="font-size: 4rem;"></i>
                                        </div>
                                    @endif
                                    <button class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle shadow"
                                            style="width: 35px; height: 35px;">
                                        <i class="bi bi-camera-fill"></i>
                                    </button>
                                </div>
                                <h5 class="mt-3 mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                <p class="text-muted">{{ ucfirst($user->role) }}</p>
                                <div class="mb-3">
                                    @if($user->status === 'active')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Actif
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Inactif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Section Informations Personnelles -->
                        <div class="col-md-8 col-lg-9">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="bi bi-person-vcard me-2"></i>
                                                Informations Personnelles
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-person me-1"></i>Prénom
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->first_name }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-person me-1"></i>Nom
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->last_name }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-envelope me-1"></i>Email
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->email }}
                                                        @if($user->email_verified_at)
                                                            <i class="bi bi-patch-check-fill text-success ms-1"
                                                               title="Email vérifié"></i>
                                                        @else
                                                            <i class="bi bi-exclamation-triangle-fill text-warning ms-1"
                                                               title="Email non vérifié"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-telephone me-1"></i>Téléphone
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->phone ?? 'Non renseigné' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-calendar-event me-1"></i>Date de naissance
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : 'Non renseignée' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-gender-ambiguous me-1"></i>Genre
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        @if($user->gender === 'male')
                                                            <i class="bi bi-gender-male text-primary me-1"></i>Masculin
                                                        @elseif($user->gender === 'female')
                                                            <i class="bi bi-gender-female text-danger me-1"></i>Féminin
                                                        @else
                                                            Non spécifié
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-geo-alt me-1"></i>Adresse
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->address ?? 'Non renseignée' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Sécurité -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="bi bi-shield-lock me-2"></i>
                                                Sécurité
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-shield-check me-1"></i>Authentification à deux facteurs
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        @if($user->two_factor_enabled)
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle me-1"></i>Activée
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="bi bi-exclamation-triangle me-1"></i>Désactivée
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-key me-1"></i>Changement de mot de passe requis
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        @if($user->must_change_password)
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-exclamation-triangle me-1"></i>Oui
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle me-1"></i>Non
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-clock-history me-1"></i>Dernière connexion
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : 'Jamais connecté' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Permissions et Rôles -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="bi bi-award me-2"></i>
                                                Rôles et Permissions
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-person-badge me-1"></i>Rôle
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        <span class="badge bg-primary fs-6">
                                                            {{ ucfirst($user->role) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-key-fill me-1"></i>Permissions
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        @if($user->permissions)
                                                            @php
                                                                $permissions = is_string($user->permissions) ? json_decode($user->permissions, true) : $user->permissions;
                                                            @endphp
                                                            @if(is_array($permissions))
                                                                @foreach($permissions as $permission)
                                                                    <span class="badge bg-secondary me-1 mb-1">{{ $permission }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">Aucune permission spécifique</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Aucune permission spécifique</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Organisation -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="bi bi-building me-2"></i>
                                                Organisation
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-building me-1"></i>Entreprise
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->company_id ? 'ID: ' . $user->company_id : 'Non assigné' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-shop me-1"></i>Agence
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->agency_id ? 'ID: ' . $user->agency_id : 'Non assigné' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-person-plus me-1"></i>Créé par
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->created_by ? 'ID: ' . $user->created_by : 'Auto-inscription' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small">
                                                        <i class="bi bi-calendar-plus me-1"></i>Date de création
                                                    </label>
                                                    <div class="form-control-plaintext fw-semibold">
                                                        {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : 'Non disponible' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'actions -->
                                {{-- <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex flex-wrap gap-2">
                                            <button class="btn btn-primary">
                                                <i class="bi bi-pencil-square me-1"></i>
                                                Modifier le profil
                                            </button>
                                            <button class="btn btn-outline-secondary">
                                                <i class="bi bi-key me-1"></i>
                                                Changer le mot de passe
                                            </button>
                                            <button class="btn btn-outline-info">
                                                <i class="bi bi-shield-lock me-1"></i>
                                                Configurer 2FA
                                            </button>
                                            @if(auth()->user()->role === 'admin')
                                                <button class="btn btn-outline-warning">
                                                    <i class="bi bi-person-gear me-1"></i>
                                                    Gérer les permissions
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour la modification de photo -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-camera me-2"></i>
                    Modifier la photo de profil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Choisir une nouvelle photo</label>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille maximale : 2MB</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('styles')
    <style>
    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .form-control-plaintext {
        padding: 0.375rem 0;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0.5rem;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .position-relative .btn {
        border: 2px solid white;
    }
</style>

@endpush

@push('scripts')
    <script>
    // Gestion du clic sur le bouton de modification de photo
    document.querySelector('.btn-primary.position-absolute').addEventListener('click', function() {
        var photoModal = new bootstrap.Modal(document.getElementById('photoModal'));
        photoModal.show();
    });

    // Prévisualisation de l'image
    document.getElementById('profile_photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Ici vous pouvez ajouter une prévisualisation si nécessaire
                console.log('Photo sélectionnée:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
