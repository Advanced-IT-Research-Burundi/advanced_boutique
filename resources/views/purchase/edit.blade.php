@extends('layouts.app')

@section('title', 'Modifier l\'Achat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Modifier l'Achat #{{ $purchase->id }}
                    </h4>
                    <div>
                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-dark btn-sm me-2">
                            <i class="bi bi-eye me-1"></i>
                            Voir
                        </a>
                        <a href="{{ route('purchases.index') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>
                            Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations actuelles -->
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle me-3"></i>
                        <div>
                            <strong>Achat créé le :</strong> {{ $purchase->created_at->format('d/m/Y à H:i') }}<br>
                            <strong>Dernier statut :</strong>
                            @if($purchase->due_amount == 0)
                                <span class="badge bg-success">Payé</span>
                            @elseif($purchase->paid_amount > 0)
                                <span class="badge bg-warning">Partiellement payé</span>
                            @else
                                <span class="badge bg-danger">Non payé</span>
                            @endif
                        </div>
                    </div>

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

                    <!-- Alerte de modification -->
                    <div class="alert alert-warning d-flex align-items-center mb-4">
                        <i class="bi bi-exclamation-triangle me-3"></i>
                        <div>
                            <strong>Attention :</strong> La modification de cet achat peut avoir des impacts sur les stocks et la comptabilité.
                            Assurez-vous de vérifier les données avant de sauvegarder.
                        </div>
                    </div>

                    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseEditForm">
                        @csrf
                        @method('PUT')

                        @include('purchase._form')

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Annuler
                                        </a>
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-info">
                                            <i class="bi bi-eye me-1"></i>
                                            Voir détails
                                        </a>
                                    </div>
                                    <div>
                                        {{-- <button type="button" class="btn btn-outline-primary me-2" id="compareBtn">
                                            <i class="bi bi-arrow-left-right me-1"></i>
                                            Comparer
                                        </button> --}}
                                        <button type="button" class="btn btn-outline-warning me-2" id="previewBtn">
                                            <i class="bi bi-eye me-1"></i>
                                            Aperçu
                                        </button>
                                        <button type="submit" class="btn btn-success" id="updateBtn">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <span class="spinner-border spinner-border-sm d-none me-1" id="updateSpinner"></span>
                                            Mettre à jour
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

<!-- Modal de comparaison -->
<div class="modal fade" id="compareModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-left-right me-2"></i>
                    Comparaison des modifications
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="compareContent">
                <!-- Contenu généré dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aperçu -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>
                    Aperçu des modifications
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Contenu généré dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Fermer
                </button>
                <button type="button" class="btn btn-success" id="confirmUpdateBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Confirmer la mise à jour
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('purchaseEditForm');
    const updateBtn = document.getElementById('updateBtn');
    const updateSpinner = document.getElementById('updateSpinner');
    const previewBtn = document.getElementById('previewBtn');
    // const compareBtn = document.getElementById('compareBtn');
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    const compareModal = new bootstrap.Modal(document.getElementById('compareModal'));
    const confirmUpdateBtn = document.getElementById('confirmUpdateBtn');

    // Données originales pour la comparaison
    const originalData = {
        supplier_id: {{ $purchase->supplier_id }},
        stock_id: {{ $purchase->stock_id }},
        agency_id: {{ $purchase->agency_id ?? 'null' }},
        purchase_date: '{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') }}',
        paid_amount: {{ $purchase->paid_amount }},
        total_amount: {{ $purchase->total_amount }},
        items: [],
    };

    // Validation en temps réel
    function validateForm() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        let hasChanges = false;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });

        // Vérifier les changements
        hasChanges = detectChanges();

        // Vérifier qu'il y a au moins un produit
        const productItems = document.querySelectorAll('.product-item');
        if (productItems.length === 0) {
            isValid = false;
        }

        updateBtn.disabled = !isValid || !hasChanges;
        previewBtn.disabled = !isValid;

        // Mettre à jour le bouton selon l'état
        if (!hasChanges && isValid) {
            updateBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Aucune modification';
            updateBtn.classList.remove('btn-success');
            updateBtn.classList.add('btn-outline-secondary');
        } else {
           updateBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i><span class="spinner-border spinner-border-sm d-none me-1" id="updateSpinner"></span>Mettre à jour';
            updateBtn.classList.remove('btn-outline-secondary');
            updateBtn.classList.add('btn-success');
        }
        return isValid;
    }

    // Détection des changements pour activer le bouton Mettre à jour
    function detectChanges() {
        const formData = new FormData(form);
        // Vérifie les champs principaux
        if (
            parseInt(formData.get('supplier_id')) !== originalData.supplier_id ||
            parseInt(formData.get('stock_id')) !== originalData.stock_id ||
            (formData.get('agency_id') ? parseInt(formData.get('agency_id')) : null) !== originalData.agency_id ||
            formData.get('purchase_date') !== originalData.purchase_date ||
            parseFloat(formData.get('paid_amount')) !== originalData.paid_amount
        ) {
            return true;
        }
        // Vérifie les produits
        const items = [];
        document.querySelectorAll('.product-item').forEach(item => {
            items.push({
                product_id: parseInt(item.querySelector('.product-select').value),
                quantity: parseFloat(item.querySelector('.quantity-input').value),
                purchase_price: parseFloat(item.querySelector('.price-input').value),
                subtotal: parseFloat(item.querySelector('.subtotal-display').value)
            });
        });
        return JSON.stringify(items) !== JSON.stringify(originalData.items);
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
        updateBtn.disabled = true;
        updateSpinner.classList.remove('d-none');
        setTimeout(() => {
            this.submit();
        }, 500);
    });

    // Aperçu des modifications
    previewBtn.addEventListener('click', function() {
        if (!validateForm()) {
            alert('Veuillez remplir tous les champs requis avant l\'aperçu.');
            return;
        }
        generatePreview();
        previewModal.show();
    });

    // Comparaison des modifications
    // compareBtn.addEventListener('click', function() {
    //     generateComparison();
    //     compareModal.show();
    // });

    // Confirmer la mise à jour depuis l'aperçu
    confirmUpdateBtn.addEventListener('click', function() {
        previewModal.hide();
        form.submit();
    });

    // Générer l'aperçu (reprendre la logique de create.blade.php)
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
                        <tr><td><strong>Montant total:</strong></td><td class="text-success"><strong>${totalAmount.toFixed(2)} Fbu</strong></td></tr>
                        <tr><td><strong>Montant payé:</strong></td><td class="text-info">FBU{parseFloat(paidAmount).toFixed(2)} Fbu</td></tr>
                        <tr><td><strong>Reste à payer:</strong></td><td class="${dueAmount > 0 ? 'text-danger' : 'text-success'}"><strong>${dueAmount.toFixed(2)} Fbu</strong></td></tr>
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
                    <td>${product.price.toFixed(2)} Fbu</td>
                    <td class="text-success"><strong>${product.subtotal.toFixed(2)} Fbu</strong></td>
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

    // Générer la comparaison (à adapter selon besoin)
    // function generateComparison() {
    //     // ... (logique pour comparer originalData et les valeurs actuelles du formulaire)
    //     document.getElementById('compareContent').innerHTML = '<div class="alert alert-info">Comparaison à compléter...</div>';
    // }

    // Initialisation
    validateForm();
});
</script>
@endpush
@endsection
