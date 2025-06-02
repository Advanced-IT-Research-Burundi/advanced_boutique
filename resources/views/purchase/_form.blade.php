<div id="purchaseForm">
    <!-- Informations générales -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="supplier_id" class="form-label">
                    <i class="bi bi-building me-1"></i>
                    Fournisseur <span class="text-danger">*</span>
                </label>
                <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                    <option value="">Sélectionner un fournisseur</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                                {{ (old('supplier_id', $purchase->supplier_id ?? '') == $supplier->id) ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="stock_id" class="form-label">
                    <i class="bi bi-box me-1"></i>
                    Stock <span class="text-danger">*</span>
                </label>
                <select name="stock_id" id="stock_id" class="form-select @error('stock_id') is-invalid @enderror" required>
                    <option value="">Sélectionner un stock</option>
                    @foreach($stocks as $stock)
                        <option value="{{ $stock->id }}"
                                {{ (old('stock_id', $purchase->stock_id ?? '') == $stock->id) ? 'selected' : '' }}>
                            {{ $stock->name }}
                        </option>
                    @endforeach
                </select>
                @error('stock_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="purchase_date" class="form-label">
                    <i class="bi bi-calendar3 me-1"></i>
                    Date d'achat <span class="text-danger">*</span>
                </label>
                <input type="date" name="purchase_date" id="purchase_date"
                       class="form-control @error('purchase_date') is-invalid @enderror"
                       value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : date('Y-m-d')) }}"
                       required>
                @error('purchase_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="agency_id" class="form-label">
                    <i class="bi bi-geo-alt me-1"></i>
                    Agence
                </label>
                <select name="agency_id" id="agency_id" class="form-select">
                    <option value="">Aucune agence</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}"
                                {{ (old('agency_id', $purchase->agency_id ?? '') == $agency->id) ? 'selected' : '' }}>
                            {{ $agency->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Produits -->
    <div class="card mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-basket3 me-2"></i>
                Produits à acheter
            </h5>
            <button type="button" class="btn btn-primary btn-sm" id="addProductBtn">
                <i class="bi bi-plus-circle me-1"></i>
                Ajouter produit
            </button>
        </div>
        <div class="card-body">
            <div id="products-container">
                @if(isset($purchase) && $purchase->purchaseItems->count() > 0)
                    @foreach($purchase->purchaseItems as $index => $item)
                        <div class="product-item row mb-3 p-3 border rounded" data-index="{{ $index }}">
                            <div class="col-md-4">
                                <label class="form-label">Produit <span class="text-danger">*</span></label>
                                <select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
                                    <option value="">Sélectionner un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-price="{{ $product->purchase_price }}"
                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->category->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                <input type="number" name="items[{{ $index }}][quantity]"
                                       class="form-control quantity-input"
                                       value="{{ $item->quantity }}" min="1" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Prix unitaire <span class="text-danger">*</span></label>
                                <input type="number" name="items[{{ $index }}][purchase_price]"
                                       class="form-control price-input"
                                       value="{{ $item->purchase_price }}" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sous-total</label>
                                <input type="text" class="form-control subtotal-display"
                                       value="{{ number_format($item->subtotal, 2) }}" readonly>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-product">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="product-item row mb-3 p-3 border rounded" data-index="0">
                        <div class="col-md-4">
                            <label class="form-label">Produit <span class="text-danger">*</span></label>
                            <select name="items[0][product_id]" class="form-select product-select" required>
                                <option value="">Sélectionner un produit</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                        {{ $product->name }} ({{ $product->category->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantité <span class="text-danger">*</span></label>
                            <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                   value="1" min="1" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Prix unitaire <span class="text-danger">*</span></label>
                            <input type="number" name="items[0][purchase_price]" class="form-control price-input"
                                   value="0" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sous-total</label>
                            <input type="text" class="form-control subtotal-display" value="0.00" readonly>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-product">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Résumé financier -->
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-calculator me-2"></i>
                Résumé financier
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant total</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" name="paid_amount" id="paid_amount"
                                   class="form-control" min="0" step="0.01"
                                   value="{{ old('paid_amount', $purchase->paid_amount ?? 0) }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reste à payer</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="text" id="dueAmount" class="form-control bg-light" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = {{ isset($purchase) ? $purchase->purchaseItems->count() : 1 }};

    // Ajouter un nouveau produit
    document.getElementById('addProductBtn').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const newProductHtml = `
            <div class="product-item row mb-3 p-3 border rounded" data-index="${productIndex}">
                <div class="col-md-4">
                    <label class="form-label">Produit <span class="text-danger">*</span></label>
                    <select name="items[${productIndex}][product_id]" class="form-select product-select" required>
                        <option value="">Sélectionner un produit</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                {{ $product->name }} ({{ $product->category->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantité <span class="text-danger">*</span></label>
                    <input type="number" name="items[${productIndex}][quantity]"
                           class="form-control quantity-input" value="1" min="1" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Prix unitaire <span class="text-danger">*</span></label>
                    <input type="number" name="items[${productIndex}][purchase_price]"
                           class="form-control price-input" value="0" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sous-total</label>
                    <input type="text" class="form-control subtotal-display" value="0.00" readonly>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-product">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', newProductHtml);
        productIndex++;

        // Réattacher les événements
        attachProductEvents();
        calculateTotals();
    });

    // Supprimer un produit
    function attachRemoveEvents() {
        document.querySelectorAll('.remove-product').forEach(btn => {
            btn.addEventListener('click', function() {
                if (document.querySelectorAll('.product-item').length > 1) {
                    this.closest('.product-item').remove();
                    calculateTotals();
                } else {
                    alert('Vous devez avoir au moins un produit.');
                }
            });
        });
    }

    // Événements pour les produits
    function attachProductEvents() {
        // Changement de produit - auto-remplir le prix
        document.querySelectorAll('.product-select').forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                const priceInput = this.closest('.product-item').querySelector('.price-input');
                priceInput.value = price;
                calculateSubtotal(this.closest('.product-item'));
            });
        });

        // Changement de quantité ou prix
        document.querySelectorAll('.quantity-input, .price-input').forEach(input => {
            input.addEventListener('input', function() {
                calculateSubtotal(this.closest('.product-item'));
            });
        });

        attachRemoveEvents();
    }

    // Calculer le sous-total d'un produit
    function calculateSubtotal(productItem) {
        const quantity = parseFloat(productItem.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(productItem.querySelector('.price-input').value) || 0;
        const subtotal = quantity * price;

        productItem.querySelector('.subtotal-display').value = subtotal.toFixed(2);
        calculateTotals();
    }

    // Calculer les totaux
    function calculateTotals() {
        let total = 0;

        document.querySelectorAll('.subtotal-display').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        document.getElementById('totalAmount').value = total.toFixed(2);

        const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        const dueAmount = total - paidAmount;

        document.getElementById('dueAmount').value = dueAmount.toFixed(2);

        // Colorier le reste à payer
        const dueAmountInput = document.getElementById('dueAmount');
        if (dueAmount > 0) {
            dueAmountInput.classList.remove('text-success');
            dueAmountInput.classList.add('text-danger');
        } else {
            dueAmountInput.classList.remove('text-danger');
            dueAmountInput.classList.add('text-success');
        }
    }

    // Calculer les totaux quand le montant payé change
    document.getElementById('paid_amount').addEventListener('input', calculateTotals);

    // Initialiser les événements
    attachProductEvents();
    calculateTotals();

    // Animation des champs requis
    document.querySelectorAll('input[required], select[required]').forEach(field => {
        field.addEventListener('invalid', function() {
            this.style.animation = 'shake 0.5s';
            setTimeout(() => this.style.animation = '', 500);
        });
    });
});
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.product-item {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.product-item:hover {
    background-color: #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.remove-product:hover {
    transform: scale(1.1);
}

.subtotal-display {
    font-weight: bold;
    color: #28a745;
}

#totalAmount, #dueAmount {
    font-weight: bold;
    font-size: 1.1em;
}

.text-danger {
    color: #dc3545 !important;
}

.text-success {
    color: #28a745 !important;
}
</style>
