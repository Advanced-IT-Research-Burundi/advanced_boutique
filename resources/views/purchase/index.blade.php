@extends('layouts.app')

@section('title', 'Gestion des Achats')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-cart-plus me-2"></i>
                        Gestion des Achats
                    </h4>
                    <a href="{{ route('purchases.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nouvel Achat
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="paid">Payé</option>
                                <option value="partial">Partiellement payé</option>
                                <option value="unpaid">Non payé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" id="resetFilters">
                                <i class="bi bi-arrow-clockwise"></i>
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Tableau des achats -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="purchasesTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fournisseur</th>
                                    <th>Stock</th>
                                    <th>Date d'achat</th>
                                    <th>Montant total</th>
                                    <th>Montant payé</th>
                                    <th>Reste à payer</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#{{ $purchase->id }}</span>
                                        </td>
                                        <td>
                                            <i class="bi bi-building me-1"></i>
                                            {{ $purchase->supplier->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <i class="bi bi-box me-1"></i>
                                            {{ $purchase->stock->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                {{ number_format($purchase->total_amount, 2) }} €
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="text-info">
                                                {{ number_format($purchase->paid_amount, 2) }} €
                                            </span>
                                        </td>
                                        <td>
                                            @if($purchase->due_amount > 0)
                                                <span class="text-danger">
                                                    {{ number_format($purchase->due_amount, 2) }} €
                                                </span>
                                            @else
                                                <span class="text-success">0 €</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($purchase->due_amount == 0)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Payé
                                                </span>
                                            @elseif($purchase->paid_amount > 0)
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock me-1"></i>Partiel
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Impayé
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('purchases.show', $purchase) }}">
                                                            <i class="bi bi-eye me-2"></i>Voir
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('purchases.edit', $purchase) }}">
                                                            <i class="bi bi-pencil me-2"></i>Modifier
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('purchases.destroy', $purchase) }}"
                                                              method="POST" class="delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash me-2"></i>Supprimer
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bi bi-cart-x display-1 text-muted"></i>
                                            <p class="text-muted mt-2">Aucun achat trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $purchases->links() }}
                    </div>

                    <!-- Statistiques -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-cart-plus display-4"></i>
                                    <h4>{{ $purchases->total() }}</h4>
                                    <p class="mb-0">Total Achats</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-currency-euro display-4"></i>
                                    <h4>{{ number_format($purchases->sum('total_amount'), 2) }}€</h4>
                                    <p class="mb-0">Montant Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle display-4"></i>
                                    <h4>{{ $purchases->where('due_amount', 0)->count() }}</h4>
                                    <p class="mb-0">Payés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock display-4"></i>
                                    <h4>{{ $purchases->where('due_amount', '>', 0)->count() }}</h4>
                                    <p class="mb-0">En Attente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const resetFilters = document.getElementById('resetFilters');
    const table = document.getElementById('purchasesTable');
    const rows = table.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const dateValue = dateFilter.value;

        rows.forEach(row => {
            let show = true;

            // Recherche textuelle
            if (searchTerm && !row.textContent.toLowerCase().includes(searchTerm)) {
                show = false;
            }

            // Filtre par statut
            if (statusValue && show) {
                const statusBadge = row.querySelector('.badge');
                if (statusBadge) {
                    const statusText = statusBadge.textContent.toLowerCase();
                    if (statusValue === 'paid' && !statusText.includes('payé')) show = false;
                    if (statusValue === 'partial' && !statusText.includes('partiel')) show = false;
                    if (statusValue === 'unpaid' && !statusText.includes('impayé')) show = false;
                }
            }

            row.style.display = show ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    dateFilter.addEventListener('change', filterTable);

    resetFilters.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        dateFilter.value = '';
        filterTable();
    });

    // Confirmation de suppression
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cet achat ?')) {
                this.submit();
            }
        });
    });

    // Animation des cartes statistiques
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out';
            }
        });
    });

    cards.forEach(card => observer.observe(card));
});
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.1) !important;
}

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endpush
@endsection
