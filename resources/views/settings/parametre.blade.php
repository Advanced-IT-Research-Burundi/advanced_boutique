@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="col">
    <div class="col-md-12">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-sliders"></i> Paramétrage
        </h5>
        </div>
        <div class="card-body p-0">
        <ul class="nav nav-tabs px-3 pt-3" id="parametrage-menu" role="tablist">
            <li class="nav-item" role="presentation">
            <a class="nav-link active d-flex align-items-center" id="company-tab" data-bs-toggle="tab" href="#company" role="tab">
                <i class="bi bi-building me-2"></i> Informations entreprise
            </a>
            </li>
            <li class="nav-item" role="presentation">
            <a class="nav-link d-flex align-items-center" id="commercial-tab" data-bs-toggle="tab" href="#commercial" role="tab">
                <i class="bi bi-briefcase me-2"></i> Commercial
            </a>
            </li>
            <li class="nav-item" role="presentation">
            <a class="nav-link d-flex align-items-center" id="products-tab" data-bs-toggle="tab" href="#products" role="tab">
                <i class="bi bi-box-seam me-2"></i> Produits & Services
            </a>
            </li>
            <li class="nav-item" role="presentation">
            <a class="nav-link d-flex align-items-center" id="notifications-tab" data-bs-toggle="tab" href="#notifications" role="tab">
                <i class="bi bi-bell me-2"></i> Notifications
            </a>
            </li>
            <li class="nav-item" role="presentation">
            <a class="nav-link d-flex align-items-center" id="users-tab" data-bs-toggle="tab" href="#users" role="tab">
                <i class="bi bi-people me-2"></i> Utilisateurs
            </a>
            </li>
            <li class="nav-item" role="presentation">
            <a class="nav-link d-flex align-items-center text-danger" id="danger-zone-tab" data-bs-toggle="tab" href="#danger-zone" role="tab">
                <i class="bi bi-exclamation-triangle me-2"></i> Zone sensible
            </a>
            </li>
        </ul>
        </div>
    </div>
    </div>
    <div class="col-md-12">
      <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0" id="section-title">Paramètres de l'entreprise</h5>
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
              <form id="company-form" method="POST" action="{{ route('parametrage.company.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                  <div class="col-md-3 text-center">
                    <div class="position-relative mb-3">
                      <div class="company-logo-container rounded bg-light d-flex align-items-center justify-content-center mb-2" style="width: 150px; height: 150px; margin: 0 auto;">
                        <img
                            src="{{ $company->tp_logo ? asset('storage/'.$company->tp_logo) : '#' }}"
                            class="img-fluid {{ $company->tp_logo ? '' : 'd-none' }}"
                            alt="Logo entreprise"
                            id="logo-preview"
                            style="max-height: 100%; max-width: 100%;"
                        >
                        @if(!$company->tp_logo)
                            <i class="bi bi-building text-muted" style="font-size: 3rem;" id="logo-placeholder"></i>
                        @endif
                        </div>

                      <div class="d-grid">
                        <label for="tp_logo" class="btn btn-outline-primary btn-sm">
                          <i class="bi bi-upload"></i> Changer le logo
                        </label>
                        <input type="file" class="form-control d-none" id="tp_logo" name="tp_logo" accept="image/*">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-9">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_name" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tp_name" name="tp_name" value="{{ $company->tp_name }}" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_type" class="form-label">Type d'entreprise</label>
                        <input type="text" class="form-control" id="tp_type" name="tp_type" value="{{ $company->tp_type }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_TIN" class="form-label">Numéro d'identification fiscale (NIF)</label>
                        <input type="text" class="form-control" id="tp_TIN" name="tp_TIN" value="{{ $company->tp_TIN }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_trade_number" class="form-label">Numéro RCCM</label>
                        <input type="text" class="form-control" id="tp_trade_number" name="tp_trade_number" value="{{ $company->tp_trade_number }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_phone_number" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="tp_phone_number" name="tp_phone_number" value="{{ $company->tp_phone_number }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="tp_email" name="tp_email" value="{{ $company->tp_email }}">
                      </div>
                    </div>
                  </div>
                </div>

                <ul class="nav nav-tabs mb-3" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab">
                      <i class="bi bi-geo-alt"></i> Adresse
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fiscal-tab" data-bs-toggle="tab" data-bs-target="#fiscal" type="button" role="tab">
                      <i class="bi bi-cash-coin"></i> Informations fiscales
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                      <i class="bi bi-share"></i> Réseaux sociaux
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="banking-tab" data-bs-toggle="tab" data-bs-target="#banking" type="button" role="tab">
                      <i class="bi bi-bank"></i> Informations bancaires
                    </button>
                  </li>
                </ul>

                <div class="tab-content">
                  <!-- Adresse -->
                  <div class="tab-pane fade show active" id="address" role="tabpanel" aria-labelledby="address-tab">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_address_province" class="form-label">Province</label>
                        <input type="text" class="form-control" id="tp_address_province" name="tp_address_province" value="{{ $company->tp_address_privonce }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_address_commune" class="form-label">Commune</label>
                        <input type="text" class="form-control" id="tp_address_commune" name="tp_address_commune" value="{{ $company->tp_address_commune }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_address_quartier" class="form-label">Quartier</label>
                        <input type="text" class="form-control" id="tp_address_quartier" name="tp_address_quartier" value="{{ $company->tp_address_quartier }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_address_avenue" class="form-label">Avenue</label>
                        <input type="text" class="form-control" id="tp_address_avenue" name="tp_address_avenue" value="{{ $company->tp_address_avenue }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_address_rue" class="form-label">Rue</label>
                        <input type="text" class="form-control" id="tp_address_rue" name="tp_address_rue" value="{{ $company->tp_address_rue }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_address_number" class="form-label">Numéro</label>
                        <input type="text" class="form-control" id="tp_address_number" name="tp_address_number" value="{{ $company->tp_address_number }}">
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="tp_postal_number" class="form-label">Boîte postale</label>
                      <input type="text" class="form-control" id="tp_postal_number" name="tp_postal_number" value="{{ $company->tp_postal_number }}">
                    </div>
                  </div>

                  <!-- Informations fiscales -->
                  <div class="tab-pane fade" id="fiscal" role="tabpanel" aria-labelledby="fiscal-tab">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_fiscal_center" class="form-label">Centre fiscal</label>
                        <input type="text" class="form-control" id="tp_fiscal_center" name="tp_fiscal_center" value="{{ $company->tp_fiscal_center }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_activity_sector" class="form-label">Secteur d'activité</label>
                        <input type="text" class="form-control" id="tp_activity_sector" name="tp_activity_sector" value="{{ $company->tp_activity_sector }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_legal_form" class="form-label">Forme juridique</label>
                        <input type="text" class="form-control" id="tp_legal_form" name="tp_legal_form" value="{{ $company->tp_legal_form }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="payment_type" class="form-label">Type de paiement</label>
                        <select class="form-select" id="payment_type" name="payment_type">
                          <option value="cash" {{ $company->payment_type == 'cash' ? 'selected' : '' }}>Espèces</option>
                          <option value="bank" {{ $company->payment_type == 'bank' ? 'selected' : '' }}>Transfert bancaire</option>
                          <option value="mobile" {{ $company->payment_type == 'mobile' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4 mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="vat_taxpayer" name="vat_taxpayer" value="1" {{ $company->vat_taxpayer ? 'checked' : '' }}>
                          <label class="form-check-label" for="vat_taxpayer">Assujetti à la TVA</label>
                        </div>
                      </div>
                      <div class="col-md-4 mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="ct_taxpayer" name="ct_taxpayer" value="1" {{ $company->ct_taxpayer ? 'checked' : '' }}>
                          <label class="form-check-label" for="ct_taxpayer">Contribuable CT</label>
                        </div>
                      </div>
                      <div class="col-md-4 mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="tl_taxpayer" name="tl_taxpayer" value="1" {{ $company->tl_taxpayer ? 'checked' : '' }}>
                          <label class="form-check-label" for="tl_taxpayer">Contribuable TL</label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Réseaux sociaux -->
                  <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-globe me-1"></i> Site Web</label>
                        <div class="input-group">
                          <span class="input-group-text">https://</span>
                          <input type="text" class="form-control" id="tp_website" name="tp_website" value="{{ str_replace('https://', '', $company->tp_website ?? '') }}">
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-facebook me-1"></i> Facebook</label>
                        <input type="text" class="form-control" id="tp_facebook" name="tp_facebook" value="{{ $company->tp_facebook }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-twitter me-1"></i> Twitter</label>
                        <input type="text" class="form-control" id="tp_twitter" name="tp_twitter" value="{{ $company->tp_twitter }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-instagram me-1"></i> Instagram</label>
                        <input type="text" class="form-control" id="tp_instagram" name="tp_instagram" value="{{ $company->tp_instagram }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-youtube me-1"></i> YouTube</label>
                        <input type="text" class="form-control" id="tp_youtube" name="tp_youtube" value="{{ $company->tp_youtube }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-whatsapp me-1"></i> WhatsApp</label>
                        <input type="text" class="form-control" id="tp_whatsapp" name="tp_whatsapp" value="{{ $company->tp_whatsapp }}">
                      </div>
                    </div>
                  </div>

                  <!-- Informations bancaires -->
                  <div class="tab-pane fade" id="banking" role="tabpanel" aria-labelledby="banking-tab">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="tp_bank" class="form-label">Banque</label>
                        <input type="text" class="form-control" id="tp_bank" name="tp_bank" value="{{ $company->tp_bank }}">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="tp_account_number" class="form-label">Numéro de compte</label>
                        <input type="text" class="form-control" id="tp_account_number" name="tp_account_number" value="{{ $company->tp_account_number }}">
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <!-- Commercial -->
            <div class="tab-pane fade" id="commercial" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Agences commerciales</h5>
                <a type="button" class="btn btn-sm btn-success"
                href="{{ route('agencies.index') }}">
                  <i class="bi bi-plus-circle"></i> Gérer les agences
                </a>
              </div>

              <div class="table-responsive mb-4">
                <table class="table table-hover table-striped border">
                  <thead class="table-light">
                    <tr>
                      <th>Code</th>
                      <th>Nom de l'agence</th>
                      <th>Responsable</th>
                      <th>Adresse</th>
                      <th>Statut</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($agencies as $agency)
                    <tr>
                      <td>
                        <span class="badge bg-primary">{{ $agency->code }}</span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="bi bi-building me-2 text-primary"></i>
                          <div>
                            {{ $agency->name }}
                            @if($agency->is_main_office)
                              <span class="badge bg-warning text-dark ms-1">Siège</span>
                            @endif
                          </div>
                        </div>
                      </td>
                      <td>
                        @if($agency->manager)
                          {{ $agency->manager->first_name }} {{ $agency->manager->last_name }}
                        @else
                          <span class="text-muted">Non assigné</span>
                        @endif
                      </td>
                      <td>{{ $agency->adresse ?? 'Non renseignée' }}</td>
                      <td>
                        <div class="form-check form-switch">
                          <input class="form-check-input agency-status" type="checkbox" id="agency-status-{{ $agency->id }}" data-id="{{ $agency->id }}" {{ $agency->is_active ? 'checked' : '' }}>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header bg-light">
                      <h6 class="mb-0">Paramètres commerciaux</h6>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <label class="form-label">Durée de validité des devis (jours)</label>
                        <select class="form-select" id="quote_validity">
                          <option value="15">15 jours</option>
                          <option value="30" selected>30 jours</option>
                          <option value="45">45 jours</option>
                          <option value="60">60 jours</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Objectif de vente mensuel par défaut</label>
                        <div class="input-group">
                          <input type="number" class="form-control" id="default_sales_target" value="100000" min="0">
                          <span class="input-group-text">FC</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header bg-light">
                      <h6 class="mb-0">Commissions et remises</h6>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <label class="form-label">Taux de commission par défaut (%)</label>
                        <input type="number" class="form-control" id="default_commission_rate" value="5" min="0" max="100" step="0.1">
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Remise maximale autorisée (%)</label>
                        <input type="number" class="form-control" id="max_discount_rate" value="10" min="0" max="100" step="0.1">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
                      @foreach($categories as $category)
                      <tr>
                        <td>
                          <div class="d-flex align-items-center">
                            <i class="bi bi-tag me-2 text-primary"></i>
                            {{ $category->name }}
                          </div>
                        </td>
                        <td>{{ $category->description ?? 'Aucune description' }}</td>
                        <td>
                          <span class="badge bg-info">{{ $category->products_count ?? 0 }}</span>
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
                          <input type="number" class="form-control" id="default_stock_alert" value="10" min="1">
                          <span class="input-group-text">unités</span>
                        </div>
                      </div>
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="auto_reorder" checked>
                        <label class="form-check-label" for="auto_reorder">Réapprovisionnement automatique</label>
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
                        <input type="number" class="form-control" id="default_profit_margin" value="25" min="0" step="0.1">
                      </div>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="dynamic_pricing" checked>
                        <label class="form-check-label" for="dynamic_pricing">Tarification dynamique</label>
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
                          <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                          <label class="form-check-label" for="emailNotif">Notifications par email</label>
                        </div>
                      </div>
                      <div class="mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="smsNotif" checked>
                          <label class="form-check-label" for="smsNotif">Notifications par SMS</label>
                        </div>
                      </div>
                      <div class="mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="stockAlerts" checked>
                          <label class="form-check-label" for="stockAlerts">Alertes de stock</label>
                        </div>
                      </div>
                      <div class="mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="salesTargetAlerts" checked>
                          <label class="form-check-label" for="salesTargetAlerts">Alertes objectifs de vente</label>
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
                        <i class="bi bi-info-circle me-1"></i> Utilisez les balises suivantes: [Nom], [Produit], [Quantité], [Prix], [Date], [Agence]
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
                <a type="button" class="btn btn-sm btn-success"
                href="{{ route('users.index') }}">
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
                    @foreach($users as $user)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-person"></i>
                          </div>
                          {{ $user->name }}
                        </div>
                      </td>
                      <td>{{ $user->email }}</td>
                      <td>
                        <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : ($user->role == 'doctor' ? 'success' : 'secondary') }}">
                          {{ ucfirst($user->role) }}
                        </span>
                      </td>
                      <td>
                        <div class="form-check form-switch">
                          <input class="form-check-input user-status" type="checkbox" id="user-status-{{ $user->id }}" data-id="{{ $user->id }}" {{ $user->is_active ? 'checked' : '' }}>
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

            <!-- Zone sensible -->
            <div class="tab-pane fade" id="danger-zone" role="tabpanel">
              <div class="card border-danger mb-4">
                <div class="card-header bg-danger text-white">
                  <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i> Zone sensible - Actions irréversibles</h5>
                </div>
                <div class="card-body">
                  <div class="alert alert-warning">
                    <i class="bi bi-exclamation-circle me-2"></i> Attention: Les actions suivantes sont irréversibles. Assurez-vous de bien comprendre les conséquences avant de procéder.
                  </div>

                  <div class="mb-4">
                    <h6>Réinitialisation des données</h6>
                    <p>Cette action supprimera toutes les données transactionnelles mais conservera les paramètres de base.</p>
                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetDataModal">
                      Réinitialiser les données
                    </button>
                  </div>

                  <div class="mb-4">
                    <h6>Sauvegarde du système</h6>
                    <p>Téléchargez une sauvegarde complète de vos données.</p>
                    <button class="btn btn-primary">
                      <i class="bi bi-download me-1"></i> Télécharger la sauvegarde
                    </button>
                  </div>

                  <div>
                    <h6>Restauration du système</h6>
                    <p>Restaurez votre système à partir d'une sauvegarde.</p>
                    <div class="input-group">
                      <input type="file" class="form-control" id="backupFile">
                      <button class="btn btn-outline-primary" type="button">Restaurer</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestion de l'aperçu du logo
    document.addEventListener('DOMContentLoaded', function () {
        const logoInput = document.getElementById('tp_logo');
        const preview = document.getElementById('logo-preview');

        if (logoInput && preview) {
            logoInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                if (!file.type.startsWith('image/')) {
                alert('Veuillez sélectionner un fichier image.');
                return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                };
                reader.onerror = function () {
                alert("Erreur lors du chargement de l'image.");
                };
                reader.readAsDataURL(file);
            }
            });
        }
    });

    // Sauvegarde des paramètres
    document.getElementById('save-settings').addEventListener('click', function() {
      const activeTab = document.querySelector('.tab-pane.active');
      const activeTabId = activeTab.id;

      if (activeTabId === 'company') {
        document.getElementById('company-form').submit();
      } else {
        // Pour les autres onglets, on pourrait ajouter un submit spécifique
        const forms = activeTab.querySelectorAll('form');
        if (forms.length > 0) {
          forms[0].submit();
        }
      }
    });

</script>
@endpush
