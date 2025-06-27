@extends('layouts.app')

@section('title', 'Gestion des Proformas')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 text-primary">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Gestion des Proformas
                    </h2>
                    <p class="mb-0 text-muted">{{ $proformas->total() }} proforma(s) au total</p>
                </div>
                <div class="gap-2 d-flex">
                    <button class="btn btn-outline-primary" id="refreshBtn">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                    </button>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Nouveau Proforma
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="mb-4 row">
        <div class="mb-3 col-xl-3 col-md-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-opacity-10 bg-primary rounded-circle">
                                <i class="bi bi-currency-dollar text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Montant Total</h6>
                            <h4 class="mb-0">{{ number_format($totalRevenue ?? 0, 0, ',', ' ') }} Fbu</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-3 col-md-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-opacity-10 bg-success rounded-circle">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Proformas Payés</h6>
                            <h5 class="mb-0">{{ $paidProformas ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-3 col-md-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-opacity-10 bg-warning rounded-circle">
                                <i class="bi bi-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Créances</h6>
                            <h4 class="mb-0">{{ number_format($totalDue ?? 0, 0, ',', ' ') }} Fbu</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-3 col-md-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-opacity-10 bg-info rounded-circle">
                                <i class="bi bi-calendar-day text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted">Aujourd'hui</h6>
                            <h6 class="mb-0">{{ $todayProformas ?? 0 }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-4 border-0 shadow-sm card">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Rechercher un proforma..."
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
                <div class="gap-2 col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('proformas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Proformas Table -->
    <div class="border-0 shadow-sm card">
        <div class="p-0 card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="px-4 py-3 border-0">Proforma #</th>
                            <th class="px-4 py-3 border-0">Client</th>
                            <th class="px-4 py-3 border-0">Date</th>
                            <th class="px-4 py-3 border-0">Montant Total</th>
                            <th class="px-4 py-3 border-0">Reste</th>
                            <th class="px-4 py-3 border-0">Statut</th>
                            <th class="px-4 py-3 border-0">Agence</th>
                            <th class="px-4 py-3 border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proformas as $proforma)
                        @php
                            $client = json_decode($proforma->client, true) ?? [];
                        @endphp
                        <tr class="proforma-row" data-id="{{ $proforma->id }}">
                            <td class="px-4">
                                <input type="checkbox" class="form-check-input proforma-checkbox" value="{{ $proforma->id }}">
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-opacity-10 rounded bg-primary me-2">
                                        <i class="bi bi-file-earmark-text text-primary"></i>
                                    </div>
                                    <div>
                                        <strong class="text-primary">PRO-{{ str_pad($proforma->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $proforma->created_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">
                                <div>
                                    <strong>{{ $client['name'] ?? 'Client non spécifié' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $client['phone'] ?? '' }}</small>
                                </div>
                            </td>
                            <td class="px-4">
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($proforma->sale_date)->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($proforma->sale_date)->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td class="px-4">
                                <strong class="text-dark">{{ number_format($proforma->total_amount, 0, ',', ' ') }} Fbu</strong>
                            </td>
                            <td class="px-4">
                                @if($proforma->due_amount > 0)
                                    <span class="text-warning">{{ number_format($proforma->due_amount, 0, ',', ' ') }} Fbu</span>
                                @else
                                    <span class="text-success">0 Fbu</span>
                                @endif
                            </td>
                            <td class="px-4">
                                @if($proforma->due_amount == 0)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Payé
                                    </span>
                                @elseif($proforma->due_amount < $proforma->total_amount)
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock me-1"></i>Partiel
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Impayé
                                    </span>
                                @endif
                            </td>
                            <td class="px-4">
                                <small class="text-muted">{{ $proforma->agency->name ?? 'Non spécifiée' }}</small>
                            </td>
                            <td class="px-4">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('proformas.show', $proforma) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-info print-btn"
                                            data-id="{{ $proforma->id }}"
                                            data-bs-toggle="tooltip" title="Imprimer">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $proforma->id }}"
                                            data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center">
                                <div class="text-muted">
                                    <i class="mb-3 bi bi-inbox display-4 d-block"></i>
                                    <h5>Aucun proforma trouvé</h5>
                                    <p class="mb-0">Commencez par créer votre premier proforma</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($proformas->hasPages())
        <div class="bg-transparent border-0 card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de {{ $proformas->firstItem() }} à {{ $proformas->lastItem() }} sur {{ $proformas->total() }} résultats
                </div>
                {{ $proformas->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="text-white modal-header bg-danger">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce proforma ? Cette action est irréversible.</p>
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
    const proformaCheckboxes = document.querySelectorAll('.proforma-checkbox');

    selectAllCheckbox?.addEventListener('change', function() {
        proformaCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Delete functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const proformaId = this.dataset.id;
            deleteForm.action = `/proformas/${proformaId}`;
            deleteModal.show();
        });
    });

    // Print functionality
    const printButtons = document.querySelectorAll('.print-btn');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            const proformaId = this.dataset.id;
            window.open(`/proformas/${proformaId}/print`, '_blank');
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
    document.querySelectorAll('.proforma-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('input, button, a')) {
                const proformaId = this.dataset.id;
                window.location.href = `/proformas/${proformaId}`;
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
            element.textContent = new Intl.NumberFormat('fr-FR').format(current) + ' Fbu';
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
                element.textContent = '0 Fbu';
                animateValue(element, 0, value, 1000);
            }
        });
    }, 100);
});
</script>
@endpush
@endsection
