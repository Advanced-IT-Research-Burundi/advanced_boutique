@extends('layouts.app')

@section('title', 'Modifier un Utilisateur')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('users.index') }}" class="text-decoration-none">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                    {{ $user->first_name }} {{ $user->last_name }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil"></i> Modifier
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-person-gear me-2"></i>
                Modifier l'utilisateur
            </h1>
            <p class="text-muted mb-0">
                Modification des informations de <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>
                Voir le profil
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Erreurs de validation :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- User Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}"
                             alt="Photo de profil"
                             class="rounded-circle"
                             width="60" height="60"
                             style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                             style="width: 60px; height: 60px;">
                            <i class="bi bi-person fs-4"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <h5 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-muted mb-1">{{ $user->email }}</p>
                    <div class="d-flex gap-2">
                        <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                        @if($user->status === 'active')
                            <span class="badge bg-success">Actif</span>
                        @elseif($user->status === 'inactive')
                            <span class="badge bg-secondary">Inactif</span>
                        @else
                            <span class="badge bg-danger">Suspendu</span>
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Créé le {{ $user->created_at->format('d/m/Y') }}
                        @if($user->last_login_at)
                            <br>Dernière connexion: {{ $user->last_login_at->diffForHumans() }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('user._form')
    </form>
</div>

@push('scripts')
<script>
    // Preview de l'image sélectionnée
    document.getElementById('profile_photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Créer ou mettre à jour l'aperçu de l'image
                let preview = document.getElementById('photo-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'photo-preview';
                    preview.className = 'mt-2';
                    document.getElementById('profile_photo').parentNode.appendChild(preview);
                }
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Aperçu" class="img-thumbnail" width="100">
                    <small class="text-muted d-block">Aperçu de la nouvelle photo</small>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    // Validation côté client
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return false;
        }

        if (password && password.length < 8) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 8 caractères.');
            return false;
        }
    });

    // Confirmation avant suppression (si le bouton existe)
    const deleteButton = document.querySelector('[data-action="delete"]');
    if (deleteButton) {
        deleteButton.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    }
</script>
@endpush
@endsection
