@extends('layouts.app')

@section('title', 'Nouvelle Vente')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="bi bi-plus-circle-fill me-2"></i>Nouvelle Vente
                    </h2>
                    <p class="text-muted mb-0">Créer une nouvelle transaction de vente</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                <i class="bi bi-house"></i> Accueil
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sales.index') }}" class="text-decoration-none">Ventes</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Nouvelle</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill text-primary display-6"></i>
                    <h5 class="mt-2 mb-1">{{ $clients->count() }}</h5>
                    <small class="text-muted">Clients disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam-fill text-success display-6"></i>
                    <h5 class="mt-2 mb-1">{{ $products->count() }}</h5>
                    <small class="text-muted">Produits en stock</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning display-6"></i>
                    <h5 class="mt-2 mb-1">{{ $products->where('stocks', function($query) { $query->where('quantity', '<=', 'alert_quantity'); })->count() }}</h5>
                    <small class="text-muted">Alertes stock</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-day-fill text-info display-6"></i>
                    <h5 class="mt-2 mb-1">{{ now()->format('d/m') }}</h5>
                    <small class="text-muted">Aujourd'hui</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <h6 class="mb-1">Erreurs de validation</h6>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Success Messages -->
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    </div>
    @endif

    <!-- Main Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-cart-plus-fill text-primary me-2"></i>
                    <h5 class="mb-0">Détails de la vente</h5>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickAddClientModal">
                        <i class="bi bi-person-plus me-1"></i>Nouveau client
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @include('sale._form')
        </div>
    </div>
</div>

<!-- Quick Add Client Modal -->
<div class="modal fade" id="quickAddClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Ajouter un client rapidement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickClientForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="quick_client_name" class="form-label">Nom du client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quick_client_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="quick_client_phone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="quick_client_phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label for="quick_client_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="quick_client_email" name="email">
                        </div>
                        <div class="col-12">
                            <label for="quick_client_address" class="form-label">Adresse</label>
                            <textarea class="form-control" id="quick_client_address" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Ajouter le client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Low Stock Warning Modal -->
@if($products->where('stocks', function($query) { $query->where('quantity', '<=', 'alert_quantity'); })->count() > 0)
<div class="modal fade" id="lowStockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Alertes de stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Les produits suivants ont un stock faible :</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Stock actuel</th>
                                <th>Seuil d'alerte</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @if($product->stocks->sum('quantity') <= $product->alert_quantity && $product->alert_quantity > 0)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $product->stocks->sum('quantity') }} {{ $product->unit }}</span>
                                    </td>
                                    <td>{{ $product->alert_quantity }} {{ $product->unit }}</td>
                                    <td>
                                        @if($product->stocks->sum('quantity') == 0)
                                            <span class="badge bg-danger">Rupture</span>
                                        @else
                                            <span class="badge bg-warning">Stock faible</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Compris</button>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="bi bi-box-seam me-1"></i>Gérer les stocks
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show low stock modal if there are alerts
    @if($products->where('stocks', function($query) { $query->where('quantity', '<=', 'alert_quantity'); })->count() > 0)
    const lowStockModal = new bootstrap.Modal(document.getElementById('lowStockModal'));
    // Show after a delay to ensure page is loaded
    setTimeout(() => {
        lowStockModal.show();
    }, 1000);
    @endif

    // Quick add client functionality
    const quickClientForm = document.getElementById('quickClientForm');
    const quickAddModal = document.getElementById('quickAddClientModal');
    const clientSelect = document.getElementById('client_id');

    if (quickClientForm) {
        quickClientForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Ajout en cours...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('{{ route("clients.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const client = await response.json();

                    // Add new client to select dropdown
                    const option = new Option(client.name, client.id, true, true);
                    option.dataset.phone = client.phone || '';
                    clientSelect.add(option);

                    // Trigger change event to update client info
                    clientSelect.dispatchEvent(new Event('change'));

                    // Close modal and reset form
                    bootstrap.Modal.getInstance(quickAddModal).hide();
                    quickClientForm.reset();

                    // Show success message
                    showAlert('success', 'Client ajouté avec succès !');
                } else {
                    const errors = await response.json();
                    showAlert('danger', 'Erreur lors de l\'ajout du client.');
                }
            } catch (error) {
                showAlert('danger', 'Erreur de connexion. Veuillez réessayer.');
            } finally {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Alert helper function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert at the top of the container
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Auto-save draft functionality (optional)
    let draftTimer;
    const formInputs = document.querySelectorAll('#saleForm input, #saleForm select');

    formInputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(draftTimer);
            draftTimer = setTimeout(saveDraft, 2000); // Save draft after 2 seconds of inactivity
        });
    });

    function saveDraft() {
        const formData = new FormData(document.getElementById('saleForm'));
        const draftData = {};

        for (let [key, value] of formData.entries()) {
            draftData[key] = value;
        }

        // Save to sessionStorage (since localStorage is not available)
        try {
            sessionStorage.setItem('sale_draft', JSON.stringify(draftData));
        } catch (e) {
            // Ignore if sessionStorage is not available
        }
    }

    // Load draft on page load
    function loadDraft() {
        try {
            const draftData = sessionStorage.getItem('sale_draft');
            if (draftData) {
                const data = JSON.parse(draftData);

                // Only load draft if form is empty
                const clientSelect = document.getElementById('client_id');
                if (!clientSelect.value && data.client_id) {
                    Object.keys(data).forEach(key => {
                        const input = document.querySelector(`[name="${key}"]`);
                        if (input && input.value === '') {
                            input.value = data[key];
                        }
                    });

                    // Show draft loaded message
                    showAlert('info', 'Brouillon restauré automatiquement.');
                }
            }
        } catch (e) {
            // Ignore errors
        }
    }

    // Load draft after a short delay
    setTimeout(loadDraft, 500);

    // Clear draft when form is successfully submitted
    document.getElementById('saleForm').addEventListener('submit', function() {
        try {
            sessionStorage.removeItem('sale_draft');
        } catch (e) {
            // Ignore errors
        }
    });
});
</script>
@endpush
