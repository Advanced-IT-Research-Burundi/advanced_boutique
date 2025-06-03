@extends('layouts.app')

@section('title', 'Nouvelle Vente')

@section('content')
<div class="container-fluid">
        <div class="card-body">
            @include('sale._form')
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
