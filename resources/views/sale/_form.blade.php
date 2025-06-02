@php
    $isEdit = isset($sale) && $sale->id;
    $formAction = $isEdit ? route('sales.update', $sale) : route('sales.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<form id="saleForm" action="{{ $formAction }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Client Selection -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-person-circle me-2 text-primary"></i>Informations Client
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror"
                                    id="client_id"
                                    name="client_id"
                                    required>
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                            data-phone="{{ $client->phone }}"
                                            {{ (old('client_id', $sale->client_id ?? '') == $client->id) ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="sale_date" class="form-label">Date de vente <span class="text-danger">*</span></label>
                            <input type="datetime-local"
                                   class="form-control @error('sale_date') is-invalid @enderror"
                                   id="sale_date"
                                   name="sale_date"
                                   value="{{ old('sale_date', $isEdit ? $sale->sale_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div id="clientInfo" class="mt-3" style="display: none;">
                        <div class="alert alert-info border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    <strong id="clientName"></strong>
                                    <span id="clientPhone" class="text-muted ms-2"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-box-seam me-2 text-primary"></i>Produits
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm" id="addProductBtn">
                            <i class="bi bi-plus-circle me-1"></i>Ajouter un produit
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="productsContainer">
                        @if($isEdit && $sale->saleItems->count() > 0)
                            @foreach($sale->saleItems as $index => $item)
                                <div class="product-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Produit <span class="text-danger">*</span></label>
                                            <select class="form-select product-select" name="items[{{ $index }}][product_id]" required>
                                                <option value="">Sélectionner un produit</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                            data-price="{{ $product->sale_price }}"
                                                            data-unit="{{ $product->unit }}"
                                                            data-stock="{{ $product->stocks->sum('quantity') }}"
                                                            {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantité</label>
                                            <input type="number"
                                                   class="form-control quantity-input"
                                                   name="items[{{ $index }}][quantity]"
                                                   value="{{ $item->quantity }}"
                                                   min="0.01"
                                                   step="0.01"
                                                   required>
                                            <small class="text-muted unit-display">{{ $item->product->unit }}</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Prix unitaire</label>
                                            <input type="number"
                                                   class="form-control price-input"
                                                   name="items[{{ $index }}][sale_price]"
                                                   value="{{ $item->sale_price }}"
                                                   min="0"
                                                   step="0.01"
                                                   required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Remise (%)</label>
                                            <input type="number"
                                                   class="form-control discount-input"
                                                   name="items[{{ $index }}][discount]"
                                                   value="{{ $item->discount }}"
                                                   min="0"
                                                   max="100"
                                                   step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Sous-total</label>
                                            <input type="text" class="form-control subtotal-display" readonly>
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-1 remove-product">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="stock-info mt-2" style="display: none;">
                                        <small class="text-info">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Stock disponible: <span class="stock-quantity">0</span>
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-muted" id="noProductsMessage">
                                <i class="bi bi-box display-4 mb-3"></i>
                                <p>Aucun produit ajouté</p>
                                <p class="small">Cliquez sur "Ajouter un produit" pour commencer</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Summary -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 1rem;">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calculator me-2"></i>Résumé de la vente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total:</span>
                            <span id="subtotalAmount">0 F</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Remise totale:</span>
                            <span id="totalDiscount" class="text-success">0 F</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="totalAmount" class="text-primary fs-5">0 F</strong>
                        </div>

                        <div class="mb-3">
                            <label for="paid_amount" class="form-label">Montant payé</label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control @error('paid_amount') is-invalid @enderror"
                                       id="paid_amount"
                                       name="paid_amount"
                                       value="{{ old('paid_amount', $sale->paid_amount ?? 0) }}"
                                       min="0"
                                       step="0.01"
                                       required>
                                <span class="input-group-text">F</span>
                            </div>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Reste à payer:</span>
                            <span id="dueAmount" class="fw-bold">0 F</span>
                        </div>

                        <div id="paymentStatus" class="alert d-none" role="alert"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ $isEdit ? 'Modifier la vente' : 'Enregistrer la vente' }}
                            </button>
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row border rounded p-3 mb-3" data-index="">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Produit <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                data-price="{{ $product->sale_price }}"
                                data-unit="{{ $product->unit }}"
                                data-stock="{{ $product->stocks->sum('quantity') }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantité</label>
                <input type="number" class="form-control quantity-input" name="" min="0.01" step="0.01" required>
                <small class="text-muted unit-display"></small>
            </div>
            <div class="col-md-2">
                <label class="form-label">Prix unitaire</label>
                <input type="number" class="form-control price-input" name="" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Remise (%)</label>
                <input type="number" class="form-control discount-input" name="" min="0" max="100" step="0.01">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sous-total</label>
                <input type="text" class="form-control subtotal-display" readonly>
                <button type="button" class="btn btn-outline-danger btn-sm mt-1 remove-product">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="stock-info mt-2" style="display: none;">
            <small class="text-info">
                <i class="bi bi-info-circle me-1"></i>
                Stock disponible: <span class="stock-quantity">0</span>
            </small>
        </div>
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = {{ $isEdit ? $sale->saleItems->count() : 0 }};

    // Initialize form validation
    const form = document.getElementById('saleForm');

    // Client selection handler
    const clientSelect = document.getElementById('client_id');
    const clientInfo = document.getElementById('clientInfo');
    const clientName = document.getElementById('clientName');
    const clientPhone = document.getElementById('clientPhone');

    clientSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            clientName.textContent = selectedOption.text;
            clientPhone.textContent = selectedOption.dataset.phone || '';
            clientInfo.style.display = 'block';
        } else {
            clientInfo.style.display = 'none';
        }
    });

    // Trigger client info display on page load if client is pre-selected
    if (clientSelect.value) {
        clientSelect.dispatchEvent(new Event('change'));
    }

    // Add product functionality
    const addProductBtn = document.getElementById('addProductBtn');
    const productsContainer = document.getElementById('productsContainer');
    const noProductsMessage = document.getElementById('noProductsMessage');
    const productTemplate = document.getElementById('productRowTemplate');

    addProductBtn.addEventListener('click', function() {
        const template = productTemplate.content.cloneNode(true);
        const productRow = template.querySelector('.product-row');

        productRow.dataset.index = productIndex;

        // Update form field names
        const select = productRow.querySelector('.product-select');
        const quantityInput = productRow.querySelector('.quantity-input');
        const priceInput = productRow.querySelector('.price-input');
        const discountInput = productRow.querySelector('.discount-input');

        select.name = `items[${productIndex}][product_id]`;
        quantityInput.name = `items[${productIndex}][quantity]`;
        priceInput.name = `items[${productIndex}][sale_price]`;
        discountInput.name = `items[${productIndex}][discount]`;

        // Hide no products message
        if (noProductsMessage) {
            noProductsMessage.style.display = 'none';
        }

        // Add to container
        productsContainer.appendChild(template);

        // Initialize event listeners for new row
        initializeProductRow(productRow);

        productIndex++;
        updateTotals();
    });

    // Initialize existing product rows
    document.querySelectorAll('.product-row').forEach(row => {
        initializeProductRow(row);
    });

    function initializeProductRow(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const discountInput = row.querySelector('.discount-input');
        const subtotalDisplay = row.querySelector('.subtotal-display');
        const removeBtn = row.querySelector('.remove-product');
        const unitDisplay = row.querySelector('.unit-display');
        const stockInfo = row.querySelector('.stock-info');
        const stockQuantity = row.querySelector('.stock-quantity');

        // Product selection handler
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                priceInput.value = selectedOption.dataset.price || 0;
                unitDisplay.textContent = selectedOption.dataset.unit || '';
                stockQuantity.textContent = selectedOption.dataset.stock || 0;
                stockInfo.style.display = 'block';

                // Validate stock
                const availableStock = parseFloat(selectedOption.dataset.stock || 0);
                quantityInput.max = availableStock;

                if (quantityInput.value > availableStock) {
                    quantityInput.value = availableStock;
                }
            } else {
                priceInput.value = 0;
                unitDisplay.textContent = '';
                stockInfo.style.display = 'none';
            }
            calculateRowSubtotal(row);
        });

        // Calculation handlers
        [quantityInput, priceInput, discountInput].forEach(input => {
            input.addEventListener('input', () => calculateRowSubtotal(row));
        });

        // Remove product handler
        removeBtn.addEventListener('click', function() {
            row.remove();
            updateTotals();

            // Show no products message if no products left
            if (productsContainer.children.length === 0 ||
                (productsContainer.children.length === 1 && noProductsMessage)) {
                if (noProductsMessage) {
                    noProductsMessage.style.display = 'block';
                }
            }
        });

        // Initial calculation
        if (productSelect.value) {
            productSelect.dispatchEvent(new Event('change'));
        }
    }

    function calculateRowSubtotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
        const subtotalDisplay = row.querySelector('.subtotal-display');

        const subtotal = (quantity * price) * (1 - discount / 100);
        subtotalDisplay.value = formatCurrency(subtotal);

        // Validate stock
        const productSelect = row.querySelector('.product-select');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (selectedOption.value) {
            const availableStock = parseFloat(selectedOption.dataset.stock || 0);
            const quantityInput = row.querySelector('.quantity-input');

            if (quantity > availableStock) {
                quantityInput.classList.add('is-invalid');
                row.querySelector('.stock-info').innerHTML =
                    `<small class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Quantité supérieure au stock disponible (${availableStock})</small>`;
            } else {
                quantityInput.classList.remove('is-invalid');
                row.querySelector('.stock-info').innerHTML =
                    `<small class="text-info"><i class="bi bi-info-circle me-1"></i>Stock disponible: <span class="stock-quantity">${availableStock}</span></small>`;
            }
        }

        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        let totalDiscountAmount = 0;

        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

            const rowSubtotal = quantity * price;
            const discountAmount = rowSubtotal * (discount / 100);

            subtotal += rowSubtotal;
            totalDiscountAmount += discountAmount;
        });

        const total = subtotal - totalDiscountAmount;
        const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        const dueAmount = Math.max(0, total - paidAmount);

        // Update display
        document.getElementById('subtotalAmount').textContent = formatCurrency(subtotal);
        document.getElementById('totalDiscount').textContent = formatCurrency(totalDiscountAmount);
        document.getElementById('totalAmount').textContent = formatCurrency(total);
        document.getElementById('dueAmount').textContent = formatCurrency(dueAmount);

        // Update payment status
        updatePaymentStatus(total, paidAmount, dueAmount);
    }

    function updatePaymentStatus(total, paid, due) {
        const statusDiv = document.getElementById('paymentStatus');

        if (total === 0) {
            statusDiv.className = 'alert d-none';
            return;
        }

        if (due === 0) {
            statusDiv.className = 'alert alert-success';
            statusDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i>Vente entièrement payée';
        } else if (paid > 0) {
            statusDiv.className = 'alert alert-warning';
            statusDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Paiement partiel';
        } else {
            statusDiv.className = 'alert alert-danger';
            statusDiv.innerHTML = '<i class="bi bi-x-circle me-2"></i>Vente non payée';
        }
    }

    // Paid amount handler
    document.getElementById('paid_amount').addEventListener('input', updateTotals);

    // Format currency function
    function formatCurrency(amount) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' F';
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Check if at least one product is added
        const productRows = document.querySelectorAll('.product-row');
        if (productRows.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un produit à la vente.');
            return;
        }

        // Check stock validation
        let hasStockError = false;
        productRows.forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            if (quantityInput.classList.contains('is-invalid')) {
                hasStockError = true;
            }
        });

        if (hasStockError) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs de stock avant de continuer.');
            return;
        }

        form.classList.add('was-validated');
    });

    // Initial totals calculation
    updateTotals();
});
</script>
@endpush
