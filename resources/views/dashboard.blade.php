@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@push('styles')
<style>
    .stats-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-blue);
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        margin-bottom: 1rem;
    }

    .chart-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .quick-action-btn {
        border-radius: 8px;
        padding: 0.75rem;
        text-decoration: none;
        color: white;
        background: var(--primary-blue);
    }

    .recent-activity {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-item {
        border-left: 3px solid var(--primary-blue);
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        background: #f8f9fa;
        border-radius: 0 8px 8px 0;
    }

    .low-stock-alert {
        background: #fff3cd;
        border-left: 4px solid #f39c12;
    }

    .metric-card {
        text-align: center;
        padding: 1.25rem;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background: var(--primary-blue);
        color: white;
        border: none;
        padding: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Stats Cards Row -->
    <div class="mb-4 row">
        <!-- Chiffre d'affaires -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1 text-muted">Chiffre d'affaires</h6>
                            <h3 class="mb-0 text-success">{{ number_format(450000, 0, ',', ' ') }} FBU</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> +12,5% ce mois
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventes du jour -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: var(--primary-blue);">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1 text-muted">Ventes aujourd'hui</h6>
                            <h3 class="mb-0" style="color: var(--primary-blue);">{{ number_format(25000, 0, ',', ' ') }} FBU</h3>
                            <small class="text-info">
                                <i class="bi bi-graph-up"></i> 24 transactions
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits en stock -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #6f42c1 0%, #8a63d2 100%);">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1 text-muted">Produits en stock</h6>
                            <h3 class="mb-0 text-purple">1 247</h3>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i> 15 en rupture
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients actifs -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #fd7e14 0%, #ff8c42 100%);">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1 text-muted">Clients actifs</h6>
                            <h3 class="mb-0 text-orange">342</h3>
                            <small class="text-info">
                                <i class="bi bi-person-plus"></i> +8 nouveaux
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des ventes et actions rapides -->
    <div class="mb-4 row">
        <!-- Graphique des ventes -->
        <div class="col-xl-6 col-lg-7">
            <div class="chart-container">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="color: var(--primary-blue);">
                        <i class="bi bi-graph-up me-2"></i>Évolution des ventes
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active">7J</button>
                        <button class="btn btn-outline-primary">30J</button>
                        <button class="btn btn-outline-primary">3M</button>
                    </div>
                </div>
                <canvas id="venteChart" width="250" height="150"></canvas>

            </div>
        </div>

        <!-- Actions rapides -->
        <div class="col-xl-6 col-lg-5">
            <div class="chart-container">
                <h5 class="mb-3" style="color: var(--primary-blue);">
                    <i class="bi bi-lightning me-2"></i>Actions rapides
                </h5>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('sales.create') }}" class="text-center quick-action-btn d-block">
                            <i class="mb-2 bi bi-plus-circle fs-2 d-block"></i>
                            Nouvelle vente
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('purchases.create') }}" class="text-center quick-action-btn d-block">
                            <i class="mb-2 bi bi-cart-plus fs-2 d-block"></i>
                            Nouvel achat
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('products.create') }}" class="text-center quick-action-btn d-block">
                            <i class="mb-2 bi bi-bag-plus fs-2 d-block"></i>
                            Ajouter produit
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('clients.create') }}" class="text-center quick-action-btn d-block">
                            <i class="mb-2 bi bi-person-plus fs-2 d-block"></i>
                            Nouveau client
                        </a>
                    </div>
                </div>

                <!-- Mini rapport caisse -->
                <div class="p-3 mt-4 rounded" style="background: linear-gradient(135deg, #e8f4f8 0%, #d4edda 100%);">
                    <h6 class="mb-2" style="color: var(--primary-blue);">
                        <i class="bi bi-cash-stack me-2"></i>État de la caisse
                    </h6>
                    <div class="d-flex justify-content-between">
                        <span>Solde d'ouverture:</span>
                        <strong>50 000 FBU</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Recettes du jour:</span>
                        <strong class="text-success">+25 000 FBU</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dépenses du jour:</span>
                        <strong class="text-danger">-5 000 FBU</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span><strong>Solde actuel:</strong></span>
                        <strong style="color: var(--primary-blue);">70 000 FBU</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rapports détaillés -->
    <div class="mb-4 row">
        <!-- Top produits -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-container">
                <h5 class="mb-3" style="color: var(--primary-blue);">
                    <i class="bi bi-star me-2"></i>Top 5 Produits vendus
                </h5>
                <div class="table-responsive">
                    <table class="table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Revenus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 bg-primary rounded-circle me-2">
                                            <i class="text-white bi bi-phone"></i>
                                        </div>
                                        Samsung Galaxy A54
                                    </div>
                                </td>
                                <td><span class="badge bg-info">45 unités</span></td>
                                <td><strong>135 000 FBU</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 bg-success rounded-circle me-2">
                                            <i class="text-white bi bi-laptop"></i>
                                        </div>
                                        HP Pavilion 15
                                    </div>
                                </td>
                                <td><span class="badge bg-info">12 unités</span></td>
                                <td><strong>96 000 FBU</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 bg-warning rounded-circle me-2">
                                            <i class="text-white bi bi-headphones"></i>
                                        </div>
                                        AirPods Pro
                                    </div>
                                </td>
                                <td><span class="badge bg-info">28 unités</span></td>
                                <td><strong>84 000 FBU</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 bg-danger rounded-circle me-2">
                                            <i class="text-white bi bi-smartwatch"></i>
                                        </div>
                                        Apple Watch SE
                                    </div>
                                </td>
                                <td><span class="badge bg-info">18 unités</span></td>
                                <td><strong>72 000 FBU</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 bg-secondary rounded-circle me-2">
                                            <i class="text-white bi bi-mouse"></i>
                                        </div>
                                        Logitech MX Master
                                    </div>
                                </td>
                                <td><span class="badge bg-info">33 unités</span></td>
                                <td><strong>49 500 FBU</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activités récentes -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-container">
                <h5 class="mb-3" style="color: var(--primary-blue);">
                    <i class="bi bi-clock-history me-2"></i>Activités récentes
                </h5>
                <div class="recent-activity">
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Nouvelle vente</h6>
                                <p class="mb-1 text-muted">Vente #VS-2024-001 - Client: Marie Dubois</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>Il y a 5 minutes
                                </small>
                            </div>
                            <span class="badge bg-success">3 500 FBU</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Réapprovisionnement</h6>
                                <p class="mb-1 text-muted">Stock mis à jour - Produit: iPhone 15</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>Il y a 15 minutes
                                </small>
                            </div>
                            <span class="badge bg-info">+50 unités</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Nouveau client</h6>
                                <p class="mb-1 text-muted">Inscription: Jean Mbala</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>Il y a 1 heure
                                </small>
                            </div>
                            <span class="badge bg-primary">Nouveau</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Paiement reçu</h6>
                                <p class="mb-1 text-muted">Facture #FA-2024-045 - Client: Tech Solutions</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>Il y a 2 heures
                                </small>
                            </div>
                            <span class="badge bg-success">15 000 FBU</span>
                        </div>
                    </div>

                    <div class="activity-item low-stock-alert">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Stock faible
                                </h6>
                                <p class="mb-1 text-muted">Produit: MacBook Air M2 - Quantité: 3 restantes</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>Il y a 3 heures
                                </small>
                            </div>
                            <span class="badge bg-warning">Alerte</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs de performance -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="mb-4" style="color: var(--primary-blue);">
                    <i class="bi bi-speedometer2 me-2"></i>Indicateurs de performance
                </h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <h6 class="text-muted">Objectif mensuel</h6>
                            <div class="mb-2 progress progress-custom">
                                <div class="progress-bar progress-bar-custom" style="width: 75%"></div>
                            </div>
                            <p class="mb-1"><strong>750 000 / 1 000 000 FBU</strong></p>
                            <small class="text-success">75% atteint</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <h6 class="text-muted">Rotation stock</h6>
                            <div class="mb-2 progress progress-custom">
                                <div class="progress-bar progress-bar-custom" style="width: 60%"></div>
                            </div>
                            <p class="mb-1"><strong>2,4x / mois</strong></p>
                            <small class="text-info">Bon rythme</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <h6 class="text-muted">Satisfaction client</h6>
                            <div class="mb-2 progress progress-custom">
                                <div class="progress-bar progress-bar-custom" style="width: 92%"></div>
                            </div>
                            <p class="mb-1"><strong>4,6/5</strong></p>
                            <small class="text-success">Excellent</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <h6 class="text-muted">Temps moyen vente</h6>
                            <div class="mb-2 progress progress-custom">
                                <div class="progress-bar progress-bar-custom" style="width: 85%"></div>
                            </div>
                            <p class="mb-1"><strong>3,2 min</strong></p>
                            <small class="text-success">Rapide</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des ventes
    const ctx = document.getElementById('venteChart').getContext('2d');
    const venteChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Ventes (FBU)',
                data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                borderColor: '#2E7DB8',
                backgroundColor: 'rgba(46, 125, 184, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2E7DB8',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },


    });


});
</script>
@endpush
