@extends('layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-sliders"></i> Produits
                        </h5>
                    </div>

                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="section-title">Produit / {{ $product->name }}</h5>
                        <div>
                            <button type="button" class="btn btn-primary" id="save-settings">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Informations entreprise -->
                            <div class="tab-pane fade show active" id="company" role="tabpanel">
                                <form id="company-form" method="POST" action="{{ route('parametrage.company.update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row mb-4">
                                        <div class="col-md-3 text-center">
                                            <div class="position-relative mb-3">
                                                <div class="company-logo-container rounded bg-light d-flex align-items-center justify-content-center mb-2"
                                                    style="width: 150px; height: 150px; margin: 0 auto;">
                                                    @if ($company->tp_logo)
                                                        <img src="{{ asset('storage/' . $product->tp_logo) }}"
                                                            class="img-fluid" alt="Logo du produit" id="logo-preview">
                                                    @else
                                                        <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                                                    @endif
                                                </div>
                                                <div class="d-grid">
                                                    <label for="tp_logo" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-upload"></i> Changer le logo
                                                    </label>
                                                    <input type="file" class="form-control d-none" id="tp_logo"
                                                        name="tp_logo" accept="image/*">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="tp_name" class="form-label">Nom de l'entreprise <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="tp_name" name="tp_name"
                                                        value="{{ $company->tp_name }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="tp_type" class="form-label">Type d'entreprise</label>
                                                    <input type="text" class="form-control" id="tp_type" name="tp_type"
                                                        value="{{ $company->tp_type }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="tp_TIN" class="form-label">Numéro d'identification
                                                        fiscale (NIF)</label>
                                                    <input type="text" class="form-control" id="tp_TIN" name="tp_TIN"
                                                        value="{{ $company->tp_TIN }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="tp_trade_number" class="form-label">Numéro RCCM</label>
                                                    <input type="text" class="form-control" id="tp_trade_number"
                                                        name="tp_trade_number" value="{{ $company->tp_trade_number }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="tp_phone_number" class="form-label">Téléphone</label>
                                                    <input type="text" class="form-control" id="tp_phone_number"
                                                        name="tp_phone_number" value="{{ $company->tp_phone_number }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="tp_email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="tp_email"
                                                        name="tp_email" value="{{ $company->tp_email }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <ul class="nav nav-tabs mb-3" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="address-tab" data-bs-toggle="tab"
                                                data-bs-target="#address" type="button" role="tab">
                                                <i class="bi bi-geo-alt"></i> Adresse
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="fiscal-tab" data-bs-toggle="tab"
                                                data-bs-target="#fiscal" type="button" role="tab">
                                                <i class="bi bi-cash-coin"></i> Informations fiscales
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="social-tab" data-bs-toggle="tab"
                                                data-bs-target="#social" type="button" role="tab">
                                                <i class="bi bi-share"></i> Réseaux sociaux
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="banking-tab" data-bs-toggle="tab"
                                                data-bs-target="#banking" type="button" role="tab">
                                                <i class="bi bi-bank"></i> Informations bancaires
                                            </button>
                                        </li>
                                    </ul>

                                </form>
                            </div>

                            <!-- Produits & Services -->
                            <div class="tab-pane fade" id="products" role="tabpanel">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Catégories de produits</h5>
                                        <a class="btn btn-sm btn-success" href="{{ route('categories.index') }}">
                                            <i class="bi bi-plus-circle"></i> Gérer les catégories
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover border">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Description</th>
                                                    <th>Nombre de produits</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($categories as $category)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-tag me-2 text-primary"></i>
                                                                {{ $category->name }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $category->description ?? 'Aucune description' }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-info">{{ $category->products_count ?? 0 }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Gestion des stocks</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Seuil d'alerte stock minimum</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control"
                                                            id="default_stock_alert" value="10" min="1">
                                                        <span class="input-group-text">unités</span>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" id="auto_reorder"
                                                        checked>
                                                    <label class="form-check-label" for="auto_reorder">Réapprovisionnement
                                                        automatique</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Tarification</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Marge bénéficiaire par défaut (%)</label>
                                                    <input type="number" class="form-control" id="default_profit_margin"
                                                        value="25" min="0" step="0.1">
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="dynamic_pricing"
                                                        checked>
                                                    <label class="form-check-label" for="dynamic_pricing">Tarification
                                                        dynamique</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Paramètres de notification</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="emailNotif"
                                                            checked>
                                                        <label class="form-check-label" for="emailNotif">Notifications par
                                                            email</label>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="smsNotif"
                                                            checked>
                                                        <label class="form-check-label" for="smsNotif">Notifications par
                                                            SMS</label>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="stockAlerts"
                                                            checked>
                                                        <label class="form-check-label" for="stockAlerts">Alertes de
                                                            stock</label>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="salesTargetAlerts" checked>
                                                        <label class="form-check-label" for="salesTargetAlerts">Alertes
                                                            objectifs de vente</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Messages de notification par défaut</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="alert alert-info small mb-3">
                                                    <i class="bi bi-info-circle me-1"></i> Utilisez les balises suivantes:
                                                    [Nom], [Produit], [Quantité], [Prix], [Date], [Agence]
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Message confirmation de commande</label>
                                                    <textarea class="form-control" rows="3">Bonjour [Nom], votre commande de [Produit] (Qté: [Quantité]) d'un montant de [Prix] FC a été confirmée. Livraison prévue le [Date].</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Message alerte de stock</label>
                                                    <textarea class="form-control" rows="3">Alerte: Le stock de [Produit] dans l'agence [Agence] est critique ([Quantité] unités restantes).</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Utilisateurs -->
                            <div class="tab-pane fade" id="users" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Les utilisateurs</h5>
                                    <a type="button" class="btn btn-sm btn-success" href="{{ route('users.index') }}">
                                        <i class="bi bi-person-plus"></i> Gérer les utilisateurs
                                    </a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover border">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Rôle</th>
                                                <th>Statut</th>
                                                {{-- <th>Actions</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                                style="width: 32px; height: 32px;">
                                                                <i class="bi bi-person"></i>
                                                            </div>
                                                            {{ $user->name }}
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $user->role == 'admin' ? 'primary' : ($user->role == 'doctor' ? 'success' : 'secondary') }}">
                                                            {{ ucfirst($user->role) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input user-status" type="checkbox"
                                                                id="user-status-{{ $user->id }}"
                                                                data-id="{{ $user->id }}"
                                                                {{ $user->is_active ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    {{-- <td>
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user-id="{{ $user->id }}">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-user-id="{{ $user->id }}">
                          <i class="bi bi-key"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-user" data-user-id="{{ $user->id }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
