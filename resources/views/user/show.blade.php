@extends('layouts.app')

@section('title', 'Profil Utilisateur')

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
            <li class="breadcrumb-item active" aria-current="page">
                {{ $user->first_name }} {{ $user->last_name }}
            </li>
        </ol>
    </nav>

    <!-- Actions Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-person-circle me-2"></i>
                Profil Utilisateur
            </h1>
        </div>
        <div class="btn-group">
            <a href="{{ route('users.index', $user) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour
            </a>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>
                Modifier
            </a>
            @if($user->id !== auth()->id())
                <form action="{{ route('users.destroy', $user) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>
                        Supprimer
                    </button>
                </form>
            @endif
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

    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}"
                             alt="Photo de profil"
                             class="rounded-circle mb-3"
                             width="120" height="120"
                             style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mx-auto mb-3"
                             style="width: 120px; height: 120px;">
                            <i class="bi bi-person" style="font-size: 3rem;"></i>
                        </div>
                    @endif

                    <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                        @if($user->status === 'active')
                            <span class="badge bg-success">Actif</span>
                        @elseif($user->status === 'inactive')
                            <span class="badge bg-secondary">Inactif</span>
                        @else
                            <span class="badge bg-danger">Suspendu</span>
                        @endif
                    </div>

                    @if($user->phone)
                        <p class="mb-1">
                            <i class="bi bi-telephone text-muted me-1"></i>
                            {{ $user->phone }}
                        </p>
                    @endif

                    @if($user->last_login_at)
                        <small class="text-muted">
                            <i class="bi bi-clock text-muted me-1"></i>
                            Dernière connexion: {{ $user->last_login_at->diffForHumans() }}
                        </small>
                    @endif
                </div>
            </div>

            <!-- Security Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Sécurité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Email vérifié</span>
                        @if($user->email_verified_at)
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger"></i>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Authentification 2FA</span>
                        @if($user->two_factor_enabled)
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-muted"></i>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span>Changement de mot de passe requis</span>
                        @if($user->must_change_password)
                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        @else
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations détaillées
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informations personnelles</h6>

                            <div class="mb-3">
                                <strong>Nom complet:</strong><br>
                                {{ $user->first_name }} {{ $user->last_name }}
                            </div>

                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                {{ $user->email }}
                            </div>

                            @if($user->phone)
                                <div class="mb-3">
                                    <strong>Téléphone:</strong><br>
                                    {{ $user->phone }}
                                </div>
                            @endif

                            @if($user->date_of_birth)
                                <div class="mb-3">
                                    <strong>Date de naissance:</strong><br>
                                    {{ \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') }}
                                    ( {{ \Carbon\Carbon::parse($user->date_of_birth)->age }} ans)
                                </div>
                            @endif

                            @if($user->gender)
                                <div class="mb-3">
                                    <strong>Genre:</strong><br>
                                    {{ ucfirst($user->gender) }}
                                </div>
                            @endif

                            @if($user->address)
                                <div class="mb-3">
                                    <strong>Adresse:</strong><br>
                                    {{ $user->address }}
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informations professionnelles</h6>

                            <div class="mb-3">
                                <strong>Rôle:</strong><br>
                                <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                            </div>

                            <div class="mb-3">
                                <strong>Statut:</strong><br>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">Actif</span>
                                @elseif($user->status === 'inactive')
                                    <span class="badge bg-secondary">Inactif</span>
                                @else
                                    <span class="badge bg-danger">Suspendu</span>
                                @endif
                            </div>

                            @if($user->company)
                                <div class="mb-3">
                                    <strong>Entreprise:</strong><br>
                                    <i class="bi bi-building text-info me-1"></i>
                                    {{ $user->company->name }}
                                </div>
                            @endif

                            @if($user->agency)
                                <div class="mb-3">
                                    <strong>Agence:</strong><br>
                                    <i class="bi bi-geo-alt text-warning me-1"></i>
                                    {{ $user->agency->name }}
                                </div>
                            @endif

                            @if($user->createdBy)
                                <div class="mb-3">
                                    <strong>Créé par:</strong><br>
                                    <i class="bi bi-person-circle text-success me-1"></i>
                                    {{ $user->createdBy->first_name }} {{ $user->createdBy->last_name }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timestamps Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-clock me-2"></i>
                        Historique
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Créé le:</strong><br>
                            <small class="text-muted">
                                {{ $user->created_at->format('d/m/Y à H:i') }}
                                ({{ $user->created_at->diffForHumans() }})
                            </small>
                        </div>
                        <div class="col-md-6">
                            <strong>Dernière modification:</strong><br>
                            <small class="text-muted">
                                {{ $user->updated_at->format('d/m/Y à H:i') }}
                                ({{ $user->updated_at->diffForHumans() }})
                            </small>
                        </div>
                    </div>

                    @if($user->last_login_at)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <strong>Dernière connexion:</strong><br>
                                <small class="text-muted">
                                    {{ $user->last_login_at->format('d/m/Y à H:i') }}
                                    ({{ $user->last_login_at->diffForHumans() }})
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
