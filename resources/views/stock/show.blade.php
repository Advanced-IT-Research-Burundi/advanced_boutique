@extends('layouts.app')

@section('title', 'Détails du Stock')

@section('content')
    <div class="px-4 container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="justify-between mb-4 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                        <i class="bi bi-house"></i> Accueil
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('stocks.index') }}" class="text-decoration-none">
                        <i class="bi bi-boxes"></i> Stocks
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $stock->name }}
                </li>
            </ol>
            <div class="ms-auto">
                <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier
                </a>
            </div>
        </nav>

        <div class="row">
            <!-- Informations principales -->
            <div class="col-lg-8">
                <!-- Section des 5 derniers produits ajoutés -->
                <div class="mb-4 shadow-sm card">
                    <div class="text-white card-header bg-success d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 card-title">
                            <i class="bi bi-clock-history me-2"></i>
                            Derniers produits ajoutés
                        </h6>
                        <a href="{{ route('stocks.list', $stock) }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-boxes me-1"></i>
                            Voir tous les produits
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($recentProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produit</th>
                                            <th>Code</th>
                                            <th>Quantité</th>
                                            <th>Date d'ajout</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentProducts as $stockProduct)
                                            {{-- {{$stockProduct}} --}}
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($stockProduct->product->image)
                                                            <img src="{{ asset('storage/' . $stockProduct->product->image) }}"
                                                                alt="{{ $stockProduct->product->name }}"
                                                                class="rounded me-2"
                                                                style="width: 32px; height: 32px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded bg-secondary me-2 d-flex align-items-center justify-content-center"
                                                                style="width: 32px; height: 32px;">
                                                                <i class="text-white bi bi-box"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <strong>{{ $stockProduct->product->name ?? 'N/A' }}</strong>
                                                            <br>
                                                            <small
                                                                class="text-muted">{{ $stockProduct->product->unit ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $stockProduct->product->code ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-primary">{{ number_format($stockProduct->quantity, 2) }}</span>
                                                </td>
                                                <td>
                                                    <small>
                                                        <i class="bi bi-calendar me-1"></i>
                                                        {{ $stockProduct->created_at->format('d/m/Y') }}
                                                        <br>
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ $stockProduct->created_at->format('H:i') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('stocks.mouvement', $stockProduct->id) }}"
                                                            class="btn btn-outline-info" title="Voir les mouvements">
                                                            <i class="bi bi-arrows-angle-contract">Mouvement</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-4 text-center">
                                <i class="bi bi-inbox display-4 text-muted"></i>
                                <p class="mt-2 text-muted">Aucun produit dans ce stock</p>
                                <a href="{{ route('stocks.list', $stock) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Ajouter des produits
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-4 shadow-sm card">
                    <div class="text-white card-header bg-info">
                        <h5 class="mb-0 card-title">
                            <i class="bi bi-box-fill me-2"></i>
                            {{ $stock->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Informations générales</h6>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Nom:</label>
                                    <p class="mb-1">{{ $stock->name }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Localisation:</label>
                                    <p class="mb-1">
                                        @if ($stock->location)
                                            <i class="bi bi-geo-alt text-primary me-1"></i>
                                            {{ $stock->location }}
                                        @else
                                            <span class="text-muted">Non spécifiée</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Description:</label>
                                    <p class="mb-1">
                                        @if ($stock->description)
                                            {{ $stock->description }}
                                        @else
                                            <span class="text-muted">Aucune description</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Assignations</h6>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Agence:</label>
                                    <p class="mb-1">
                                        @if ($stock->agency)
                                            <i class="bi bi-building text-info me-1"></i>
                                            <a href="{{ route('agencies.show', $stock->agency) }}"
                                                class="text-decoration-none">
                                                {{ $stock->agency->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Utilisateurs associés:</label>
                                    <p class="mb-1">
                                        @if ($stock->users->count() > 0)
                                            <i class="bi bi-people text-success me-1"></i>
                                            <span class="badge bg-success">{{ $stock->users->count() }}
                                                utilisateur(s)</span>
                                            <br>
                                            <small class="text-muted">
                                                @foreach ($stock->users->take(3) as $user)
                                                    {{ $user->first_name }} {{ $user->last_name }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                                @if ($stock->users->count() > 3)
                                                    et {{ $stock->users->count() - 3 }} autre(s)...
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">Aucun utilisateur associé</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Créé par:</label>
                                    <p class="mb-1">
                                        <i class="bi bi-person-circle text-success me-1"></i>
                                        {{ $stock->createdBy->full_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations système et Actions -->
            <div class="col-lg-4">
                <div class="mb-4 shadow-sm card">
                    <div class="text-white card-header bg-secondary">
                        <h6 class="mb-0 card-title">
                            <i class="bi bi-info-circle me-2"></i>
                            Informations système
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">ID:</label>
                                    <p class="mb-1">
                                        <span class="badge bg-secondary">#{{ $stock->id }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Date de création:</label>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar text-primary me-1"></i>
                                        {{ $stock->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Proformas associés -->
                <div class="mb-4 shadow-sm card">
                    <div class="text-white card-header bg-info">
                        <h6 class="mb-0 card-title">
                            <i class="bi bi-receipt me-2"></i>
                            Proformas associés
                            <span class="badge bg-light text-dark ms-2">{{ $stock->proformas->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($stock->proformas->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($stock->proformas->take(5) as $proforma)
                                    @php
                                        $proforma->client = json_decode($proforma->client);
                                    @endphp
                                    <div class="list-group-item px-0 py-2 border-0 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex gap-4">
                                                    <h6 class="mb-1">
                                                    <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                                    #{{ $proforma->getFormattedNumber() }}
                                                </h6>
                                                <p class="mb-1 text-muted small">
                                                    <i class="bi bi-person me-1"></i>

                                                    {{ $proforma->client['name'] ?? 'Client non spécifié' }}
                                                </p>
                                                </div>
                                                <div class="d-flex gap-4">
                                                      <div class="d-flex align-items-center">
                                                    <span class="badge bg-success me-2">
                                                        {{ number_format($proforma->total_amount, 0, ',', ' ') }} FCFA
                                                    </span>
                                                    @if ($proforma->invoice_type)
                                                        <span class="badge bg-secondary">
                                                            {{ ucfirst($proforma->invoice_type) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                     <p class="mb-1 text-muted small">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $proforma->sale_date ? \Carbon\Carbon::parse($proforma->sale_date)->format('d/m/Y') : 'Date non définie' }}
                                                </p>
                                                </div>

                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('proformas.show', $proforma) }}">
                                                            <i class="bi bi-eye me-2"></i>Voir détails
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="event.preventDefault(); if(confirm('Supprimer cette proforma ?')) document.getElementById('delete-proforma-{{ $proforma->id }}').submit();">
                                                            <i class="bi bi-trash me-2"></i>Supprimer
                                                        </a>
                                                        <form id="delete-proforma-{{ $proforma->id }}"
                                                            action="{{ route('proformas.destroy', $proforma) }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        @if ($proforma->note)
                                            <small class="text-muted">
                                                <i class="bi bi-chat-left-text me-1"></i>
                                                {{ Str::limit($proforma->note, 50) }}
                                            </small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if ($stock->proformas->count() > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('proformas.index', ['stock_id' => $stock->id]) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-arrow-right me-1"></i>
                                        Voir tous les proformas ({{ $stock->proformas->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-receipt-cutoff text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">Aucune proforma associée</p>
                                <a href="{{ route('proformas.create', ['stock_id' => $stock->id]) }}"
                                    class="btn btn-sm btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Créer une proforma
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="shadow-sm card">
                    <div class="text-white card-header bg-dark">
                        <h6 class="mb-0 card-title">
                            <i class="bi bi-lightning me-2"></i>
                            Actions rapides
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="gap-2 d-grid">
                            <a href="{{ route('stocks.list', $stock) }}" class="btn btn-outline-primary">
                                <i class="bi bi-boxes me-2"></i>
                                Gérer les produits
                            </a>

                            @can('manageUserStocks', $stock)
                                <a href="{{ route('stocks.users.manage', $stock) }}" class="btn btn-outline-info">
                                    <i class="bi bi-people me-2"></i>
                                    Gérer les utilisateurs
                                </a>
                            @endcan

                            <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>
                                Modifier ce stock
                            </a>

                            <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Créer un nouveau stock
                            </a>

                            <hr>

                            <form action="{{ route('stocks.destroy', $stock) }}" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce stock ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trash me-2"></i>
                                    Supprimer ce stock
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Section des 5 premiers utilisateurs associés -->
        <div class="mb-4 shadow-sm card">
            <div class="text-white card-header bg-info d-flex justify-content-between align-items-center">
                <h6 class="mb-0 card-title">
                    <i class="bi bi-people me-2"></i>
                    Utilisateurs associés à ce stock
                </h6>
                <div class="btn-group">
                    <span class="badge bg-light text-dark me-2">
                        {{ $stock->users->count() }} utilisateur(s)
                    </span>
                    @if ($stock->users->count() > 5)
                        <a href="{{ route('stocks.users.manage', $stock) }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-people me-1"></i>
                            Voir tous les utilisateurs
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if ($stock->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Rôle</th>
                                    <th>Agence</th>
                                    <th>Date d'assignation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stock->users->take(5) as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($user->profile_photo)
                                                    <img src="{{ Storage::url($user->profile_photo) }}"
                                                        alt="{{ $user->full_name }}" class="rounded-circle me-2"
                                                        style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-primary me-2 d-flex align-items-center justify-content-center text-white"
                                                        style="width: 32px; height: 32px;">
                                                        {{ $user->initials }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($user->agency)
                                                <i class="bi bi-geo-alt text-warning me-1"></i>
                                                {{ $user->agency->name }}
                                            @else
                                                <span class="text-muted">Aucune agence</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ $user->pivot->created_at->format('d/m/Y') }}
                                                <br>
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $user->pivot->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('users.show', $user) }}"
                                                    class="btn btn-outline-primary" title="Voir le profil">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @can('detach', [App\Models\UserStock::class, $user->pivot])
                                                    <form action="{{ route('stocks.users.detach', [$stock, $user]) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir désassigner cet utilisateur ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger"
                                                            title="Désassigner">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($stock->users->count() > 10)
                        <div class="text-center mt-3">
                            <p class="text-muted mb-2">
                                Affichage de 10 utilisateurs sur {{ $stock->users->count() }}
                            </p>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-arrow-right me-1"></i>
                                Voir tous les {{ $stock->users->count() }} utilisateurs
                            </a>
                        </div>
                    @endif
                @else
                    <div class="py-4 text-center">
                        <i class="bi bi-people display-4 text-muted"></i>
                        <p class="mt-2 text-muted">Aucun utilisateur associé à ce stock</p>
                        @can('attach', [App\Models\UserStock::class, $stock])
                            <a href="{{ route('stocks.users.manage', $stock) }}" class="btn btn-primary">
                                <i class="bi bi-person-plus me-2"></i>
                                Assigner des utilisateurs
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Bouton retour -->
    <div class="mt-4 row">
        <div class="col-12">
            <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour à la liste des stocks
            </a>
        </div>
    </div>
    </div>
@endsection
