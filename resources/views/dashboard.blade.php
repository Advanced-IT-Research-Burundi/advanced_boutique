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

    .progress-custom {
        height: 8px;
        border-radius: 5px;
    }

    .progress-bar-custom {
        background: linear-gradient(135deg, #2E7DB8 0%, #3498db 100%);
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3">
                    <!-- Filtre période -->
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2">
                        <input type="hidden" name="agency_id" value="{{ $agency_id }}">
                        <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="7" {{ $period == '7' ? 'selected' : '' }}>7 derniers jours</option>
                            <option value="30" {{ $period == '30' ? 'selected' : '' }}>30 derniers jours</option>
                            <option value="90" {{ $period == '90' ? 'selected' : '' }}>3 derniers mois</option>
                        </select>
                    </form>

                    <!-- Filtre agence -->
                    @if($agencies->count() > 1)
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2">
                        <input type="hidden" name="period" value="{{ $period }}">
                        <select name="agency_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Toutes les agences</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ $agency_id == $agency->id ? 'selected' : '' }}>
                                    {{ $agency->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <!-- Chiffre d'affaires -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Chiffre d'affaires</h6>
                            <h3 class="text-success mb-0">{{ number_format($stats['revenue']['amount'], 0, ',', ' ') }} FBU</h3>
                            <small class="{{ $stats['revenue']['is_positive'] ? 'text-success' : 'text-danger' }}">
                                <i class="bi bi-arrow-{{ $stats['revenue']['is_positive'] ? 'up' : 'down' }}"></i>
                                {{ $stats['revenue']['is_positive'] ? '+' : '' }}{{ $stats['revenue']['growth'] }}% ce mois
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventes du jour -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: var(--primary-blue);">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Ventes aujourd'hui</h6>
                            <h3 class="mb-0" style="color: var(--primary-blue);">{{ number_format($stats['today_sales']['amount'], 0, ',', ' ') }} FBU</h3>
                            <small class="text-info">
                                <i class="bi bi-graph-up"></i> {{ $stats['today_sales']['count'] }} transactions
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits en stock -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #6f42c1 0%, #8a63d2 100%);">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Produits en stock</h6>
                            <h3 class="text-purple mb-0">{{ number_format($stats['products']['total']) }}</h3>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i> {{ $stats['products']['low_stock'] }} en rupture
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients actifs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #fd7e14 0%, #ff8c42 100%);">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Clients actifs</h6>
                            <h3 class="text-orange mb-0">{{ $stats['clients']['active'] }}</h3>
                            <small class="text-info">
                                <i class="bi bi-person-plus"></i> +{{ $stats['clients']['new'] }} nouveaux
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des ventes et actions rapides -->
    <div class="row mb-4">
        <!-- Graphique des ventes -->
        <div class="col-xl-6 col-lg-7">
            <div class="chart-container" style="position: relative; height:400px;" >
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="color: var(--primary-blue);">
                        <i class="bi bi-graph-up me-2"></i>Évolution des ventes
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('dashboard', ['period' => '7', 'agency_id' => $agency_id]) }}"
                           class="btn btn-outline-primary {{ $period == '7' ? 'active' : '' }}">7J</a>
                        <a href="{{ route('dashboard', ['period' => '30', 'agency_id' => $agency_id]) }}"
                           class="btn btn-outline-primary {{ $period == '30' ? 'active' : '' }}">30J</a>
                        <a href="{{ route('dashboard', ['period' => '90', 'agency_id' => $agency_id]) }}"
                           class="btn btn-outline-primary {{ $period == '90' ? 'active' : '' }}">3M</a>
                    </div>
                </div>
                <canvas id="venteChart" ></canvas>
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
                        <a href="{{ route('sales.create') }}" class="quick-action-btn d-block text-center">
                            <i class="bi bi-plus-circle fs-2 d-block mb-2"></i>
                            Nouvelle vente
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('purchases.create') }}" class="quick-action-btn d-block text-center">
                            <i class="bi bi-cart-plus fs-2 d-block mb-2"></i>
                            Nouvel achat
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('products.create') }}" class="quick-action-btn d-block text-center">
                            <i class="bi bi-bag-plus fs-2 d-block mb-2"></i>
                            Ajouter produit
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('clients.create') }}" class="quick-action-btn d-block text-center">
                            <i class="bi bi-person-plus fs-2 d-block mb-2"></i>
                            Nouveau client
                        </a>
                    </div>
                </div>

                <!-- Mini rapport caisse -->
                @if($cashRegisterStatus)
                <div class="mt-4 p-3 rounded" style="background: linear-gradient(135deg, #e8f4f8 0%, #d4edda 100%);">
                    <h6 class="mb-2" style="color: var(--primary-blue);">
                        <i class="bi bi-cash-stack me-2"></i>État de la caisse
                        <small class="text-muted">(Ouverte le {{ \Carbon\Carbon::parse($cashRegisterStatus['opened_at'])->format('d/m/Y à H:i') }})</small>
                    </h6>
                    <div class="d-flex justify-content-between">
                        <span>Solde d'ouverture:</span>
                        <strong>{{ number_format($cashRegisterStatus['opening_balance'], 0, ',', ' ') }} FBU</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Recettes du jour:</span>
                        <strong class="text-success">+{{ number_format($cashRegisterStatus['today_revenue'], 0, ',', ' ') }} FBU</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dépenses du jour:</span>
                        <strong class="text-danger">-{{ number_format($cashRegisterStatus['today_expenses'], 0, ',', ' ') }} FBU</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span><strong>Solde actuel:</strong></span>
                        <strong style="color: var(--primary-blue);">{{ number_format($cashRegisterStatus['current_balance'], 0, ',', ' ') }} FBU</strong>
                    </div>
                </div>
                @else
                <div class="mt-4 p-3 rounded bg-light">
                    <h6 class="mb-2 text-muted">
                        <i class="bi bi-cash-stack me-2"></i>Caisse fermée
                    </h6>
                    <p class="text-muted mb-0">Ouvrez une caisse pour commencer les transactions.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Rapports détaillés -->
    <div class="row mb-4">
        <!-- Top produits -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-container">
                <h5 class="mb-3" style="color: var(--primary-blue);">
                    <i class="bi bi-star me-2"></i>Top 5 Produits vendus
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Revenus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle me-2 p-2">
                                            <i class="bi bi-box text-white"></i>
                                        </div>
                                        {{ $product->name }}
                                    </div>
                                </td>
                                <td><span class="badge bg-info">{{ $product->total_quantity }} unités</span></td>
                                <td><strong>{{ number_format($product->total_revenue, 0, ',', ' ') }} FBU</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aucune vente pour cette période</td>
                            </tr>
                            @endforelse
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
                    @forelse($recentActivities as $activity)
                    <div class="activity-item {{ $activity['type'] === 'low_stock' ? 'low-stock-alert' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 {{ $activity['type'] === 'low_stock' ? 'text-warning' : '' }}">
                                    @if($activity['type'] === 'low_stock')
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                    @endif
                                    {{ $activity['title'] }}
                                </h6>
                                <p class="text-muted mb-1">{{ $activity['description'] }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $activity['created_at']->diffForHumans() }}
                                </small>
                            </div>
                            @if($activity['amount'])
                                <span class="badge {{ $activity['badge_class'] }}">
                                    {{ number_format($activity['amount'], 0, ',', ' ') }} FBU
                                </span>
                            @else
                                <span class="badge {{ $activity['badge_class'] }}">
                                    {{ $activity['type'] === 'client' ? 'Nouveau' : 'Info' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">
                        <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                        Aucune activité récente
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Produits en rupture de stock -->
    @if($lowStockProducts->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container low-stock-alert">
                <h5 class="mb-3 text-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>Produits en rupture de stock
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Stock actuel</th>
                                <th>Seuil d'alerte</th>
                                <th>Dépôt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><span class="badge bg-danger">{{ $product->quantity }} unités</span></td>
                                <td>{{ $product->alert_quantity }} unités</td>
                                <td>{{ $product->stock_name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données du graphique depuis le contrôleur
    const chartData = @json($chartData);

    // Graphique des ventes
    const ctx = document.getElementById('venteChart').getContext('2d');
    const venteChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Ventes (FBU)',
                data: chartData.sales,
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
            }, {
                label: 'Achats (FBU)',
                data: chartData.purchases,
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointBackgroundColor: '#e74c3c',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' FBU';
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
});
</script>
@endpush
