@extends('layouts.app')

@section('title', 'Nouveau Achat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-cart-plus me-2"></i>
                        Nouveau Achat
                    </h4>
                    <a href="{{ route('purchases.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Erreurs de validation :</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseCreateForm">
                        @csrf

                        @include('purchase._form')

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Annuler
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                            <i class="bi bi-eye me-1"></i>
                                            Aperçu
                                        </button>
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <span class="spinner-border spinner-border-sm d-none me-1" id="submitSpinner"></span>
                                            Créer l'achat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aperçu -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>
                    Aperçu de l'achat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Contenu généré dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Fermer
                </button>
                <button type="button" class="btn btn-success" id="confirmCreateBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Confirmer la création
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('purchaseCreateForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const previewBtn = document.getElementById('previewBtn');
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    const confirmCreateBtn = document.getElementById('confirmCreateBtn');

    // Validation en temps réel
    function validateForm() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });

        // Vérifier qu'il y a au moins un produit
        const productItems = document.querySelectorAll('.product-item');
        if (productItems.length === 0) {
            isValid = false;
        }

        submitBtn.disabled = !isValid;
        previewBtn.disabled = !isValid;

        return isValid;
    }

    // Validation lors de la saisie
    form.addEventListener('input', validateForm);
    form.addEventListener('change', validateForm);

    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            alert('Veuillez remplir tous les champs requis.');
            return;
        }

        // Animation de chargement
        submitBtn.disabled = true;
        submitSpinner.classList.remove('d-none');

        // Soumettre après un court délai pour l'UX
        setTimeout(() => {
            this.submit();
        }, 500);
    });

    // Aperçu
    previewBtn.addEventListener('click', function() {
        if (!validateForm()) {
            alert('Veuillez remplir tous les champs requis avant l\'aperçu.');
            return;
        }

        generatePreview();
        previewModal.show();
    });

    // Confirmer la création depuis l'aperçu
    confirmCreateBtn.addEventListener('click', function() {
        previewModal.hide();
        form.submit();
    });

    function generatePreview() {
        const formData = new FormData(form);

        // Informations générales
        const supplier = document.querySelector('#supplier_id option:checked').textContent;
        const stock = document.querySelector('#stock_id option:checked').textContent;
        const agency = document.querySelector('#agency_id option:checked').textContent;
        const purchaseDate = formData.get('purchase_date');
        const paidAmount = formData.get('paid_amount') || 0;

        // Produits
        const products = [];
        let totalAmount = 0;

        document.querySelectorAll('.product-item').forEach(item => {
            const productSelect = item.querySelector('.product-select');
            const productName = productSelect.options[productSelect.selectedIndex].textContent;
            const quantity = parseFloat(item.querySelector('.quantity-input').value);
            const price = parseFloat(item.querySelector('.price-input').value);
            const subtotal = quantity * price;

            products.push({
                name: productName,
                quantity: quantity,
                price: price,
                subtotal: subtotal
            });

            totalAmount += subtotal;
        });

        const dueAmount = totalAmount - parseFloat(paidAmount);

        // Générer le HTML de l'aperçu
        let previewHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Fournisseur:</strong></td><td>${supplier}</td></tr>
                        <tr><td><strong>Stock:</strong></td><td>${stock}</td></tr>
                        <tr><td><strong>Agence:</strong></td><td>${agency}</td></tr>
                        <tr><td><strong>Date d'achat:</strong></td><td>${new Date(purchaseDate).toLocaleDateString('fr-FR')}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-calculator me-2"></i>Résumé financier</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Montant total:</strong></td><td class="text-success"><strong>${totalAmount.toFixed(2)} €</strong></td></tr>
                        <tr><td><strong>Montant payé:</strong></td><td class="text-info">${parseFloat(paidAmount).toFixed(2)} €</td></tr>
                        <tr><td><strong>Reste à payer:</strong></td><td class="${dueAmount > 0 ? 'text-danger' : 'text-success'}"><strong>${dueAmount.toFixed(2)} €</strong></td></tr>
                    </table>
                </div>
            </div>

            <h6 class="mt-4"><i class="bi bi-basket3 me-2"></i>Produits (${products.length})</h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        products.forEach(product => {
            previewHtml += `
                <tr>
                    <td>${product.name}</td>
                    <td>${product.quantity}</td>
                    <td>${product.price.toFixed(2)} €</td>
                    <td class="text-success"><strong>${product.subtotal.toFixed(2)} €</strong></td>
                </tr>
            `;
        });

        previewHtml += `
                    </tbody>
                </table>
            </div>
        `;

        document.getElementById('previewContent').innerHTML = previewHtml;
    }

    // Validation initiale
    validateForm();

    // Auto-sauvegarde en localStorage (optionnel)
    const autoSaveInterval = setInterval(() => {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        // Note: localStorage n'est pas disponible dans les artifacts Claude
        // localStorage.setItem('purchase_draft', JSON.stringify(data));
    }, 30000); // Sauvegarde toutes les 30 secondes

    // Nettoyer l'intervalle quand on quitte la page
    window.addEventListener('beforeunload', () => {
        clearInterval(autoSaveInterval);
    });
});
</script>

<style>
.is-valid {
    border-color: #28a745;
}

.is-invalid {
    border-color: #dc3545;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

#submitBtn:disabled {
    transform: none;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

.modal-content {
    border: none;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

@media (max-width: 768px) {
    .card-header h4 {
        font-size: 1.1rem;
    }

    .btn-sm {
        font-size: 0.75rem;
    }
}
</style>
@endpush
@endsection
