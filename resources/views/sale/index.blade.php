@extends('layouts.app')

@section('title', 'Gestion des Ventes')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="bi bi-cart-check-fill me-2"></i>Gestion des Ventes
                    </h2>
                    <p class="text-muted mb-0">{{ $sales->total() }} vente(s) au total</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="refreshBtn">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                    </button>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Nouvelle Vente
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-currency-dollar text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Fbu Total</h6>
                            <h4 class="mb-0">{{ number_format($totalRevenue ?? 0, 0, ',', ' ') }} F</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Ventes Payées</h6>
                            <h5 class="mb-0">{{ $paidSales ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Créances</h6>
                            <h4 class="mb-0">{{ number_format($totalDue ?? 0, 0, ',', ' ') }} Fbu</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-calendar-day text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Aujourd'hui</h6>
                            <h6 class="mb-0">{{ $todaySales ?? 0 }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Rechercher une vente..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select" name="status">
                        <option value="">Tous</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payé</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partiel</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Impayé</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="px-4 py-3 border-0">Vente #</th>
                            <th class="px-4 py-3 border-0">Client</th>
                            <th class="px-4 py-3 border-0">Date</th>
                            <th class="px-4 py-3 border-0">Montant Total</th>
                            <th class="px-4 py-3 border-0">Payé</th>
                            <th class="px-4 py-3 border-0">Reste</th>
                            <th class="px-4 py-3 border-0">Statut</th>
                            <th class="px-4 py-3 border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr class="sale-row" data-id="{{ $sale->id }}">
                            <td class="px-4">
                                <input type="checkbox" class="form-check-input sale-checkbox" value="{{ $sale->id }}">
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                                        <i class="bi bi-receipt text-primary"></i>
                                    </div>
                                    <div>
                                        <strong class="text-primary">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $sale->created_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">
                                <div>
                                    <strong>{{ $sale->client->name ?? 'Client supprimé' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $sale->client->phone ?? '' }}</small>
                                </div>
                            </td>
                            <td class="px-4">
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td class="px-4">
                                <strong class="text-dark">{{ number_format($sale->total_amount, 0, ',', ' ') }} F</strong>
                            </td>
                            <td class="px-4">
                                <span class="text-success">{{ number_format($sale->paid_amount, 0, ',', ' ') }} Fbu</span>
                            </td>
                            <td class="px-4">
                                @if($sale->due_amount > 0)
                                    <span class="text-warning">{{ number_format($sale->due_amount, 0, ',', ' ') }} F</span>
                                @else
                                    <span class="text-success">0 F</span>
                                @endif
                            </td>
                            <td class="px-4">
                                @if($sale->due_amount == 0)
                                    <span class="text-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i>Payé
                                    </span>
                                @elseif($sale->paid_amount > 0)
                                    <span class="text-warning px-3 py-2">
                                        <i class="bi bi-clock me-1"></i>Partiel
                                    </span>
                                @else
                                    <span class=" text-danger px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i>Impayé
                                    </span>
                                @endif
                            </td>
                            <td class="px-4">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sales.show', $sale) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    {{-- <a href="{{ route('sales.edit', $sale) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       data-bs-toggle="tooltip" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a> --}}
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $sale->id }}"
                                            data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    <h5>Aucune vente trouvée</h5>
                                    <p class="mb-0">Commencez par créer votre première vente</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($sales->hasPages())
        <div class="card-footer bg-transparent border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de {{ $sales->firstItem() }} à {{ $sales->lastItem() }} sur {{ $sales->total() }} résultats
                </div>
                {{ $sales->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette vente ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" id="deleteForm" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const saleCheckboxes = document.querySelectorAll('.sale-checkbox');

    selectAllCheckbox?.addEventListener('change', function() {
        saleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Delete functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const saleId = this.dataset.id;
            deleteForm.action = `/sales/${saleId}`;
            deleteModal.show();
        });
    });

    // Refresh button
    document.getElementById('refreshBtn')?.addEventListener('click', function() {
        this.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Actualisation...';
        window.location.reload();
    });

    // Auto-submit filters with debounce
    let filterTimeout;
    const searchInput = document.querySelector('input[name="search"]');

    searchInput?.addEventListener('input', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });

    // Row click to show details
    document.querySelectorAll('.sale-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('input, button, a')) {
                const saleId = this.dataset.id;
                window.location.href = `/sales/${saleId}`;
            }
        });
    });

    // Animate numbers on load
    const animateValue = (element, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const current = Math.floor(progress * (end - start) + start);
            element.textContent = new Intl.NumberFormat('fr-FR').format(current) + ' F';
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    // Animate stats on page load
    setTimeout(() => {
        document.querySelectorAll('.card h4').forEach(element => {
            const value = parseInt(element.textContent.replace(/\D/g, ''));
            if (value > 0) {
                element.textContent = '0 F';
                animateValue(element, 0, value, 1000);
            }
        });
    }, 100);
});
</script>
@endpush
@endsection
