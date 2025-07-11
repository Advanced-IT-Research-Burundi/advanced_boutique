<div class="row">

    <!-- Code du produit -->
    <div class="col-md-6 mb-3">
        <label for="code" class="form-label">
            <i class="bi bi-upc-scan me-1"></i>
            Code du produit <span class="text-danger">*</span>
        </label>
        <input type="text"
               class="form-control @error('code') is-invalid @enderror"
               id="code"
               name="code"
               value="{{ old('code', $product->code ?? '') }}"
               placeholder="Ex: PRD-001"
               required>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <!-- Nom du produit -->
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">
            <i class="bi bi-box-seam me-1"></i>
            Nom du produit <span class="text-danger">*</span>
        </label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name', $product->name ?? '') }}"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Catégorie -->
    <div class="col-md-12 mb-3">
        <label for="category_id" class="form-label">
            <i class="bi bi-tags me-1"></i>
            Catégorie <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('category_id') is-invalid @enderror"
        id="category_id"
        name="category_id"
        required>
            <option value="">Sélectionner une catégorie</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $product->category_id ?? $selectedCategoryId ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Description -->
    <div class="col-12 mb-3">
        <label for="description" class="form-label">
            <i class="bi bi-text-paragraph me-1"></i>
            Description
        </label>
        <textarea class="form-control @error('description') is-invalid @enderror"
                  id="description"
                  name="description"
                  rows="3"
                  placeholder="Description détaillée du produit...">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Prix d'achat et de vente -->
    <div class="col-md-6 mb-3">
        <label for="purchase_price" class="form-label">
            <i class="bi bi-currency-dollar me-1"></i>
            Prix d'achat (FBU) <span class="text-danger">*</span>
        </label>
        <input type="number"
               class="form-control @error('purchase_price') is-invalid @enderror"
               id="purchase_price"
               name="purchase_price"
               value="{{ old('purchase_price', $product->purchase_price ?? '') }}"
               step="0.01"
               min="0"
               required>
        @error('purchase_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="sale_price" class="form-label">
            <i class="bi bi-cash-stack me-1"></i>
            Prix de vente (FBU) <span class="text-danger">*</span>
        </label>
        <input type="number"
               class="form-control @error('sale_price') is-invalid @enderror"
               id="sale_price"
               name="sale_price"
               value="{{ old('sale_price', $product->sale_price ?? '') }}"
               step="0.01"
               min="0"
               required>
        @error('sale_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Unité et seuil d'alerte -->
    <div class="col-md-6 mb-3">
        <label for="unit" class="form-label">
            <i class="bi bi-rulers me-1"></i>
            Unité de mesure <span class="text-danger">*</span>
        </label>
        <input type="text"
               class="form-control @error('unit') is-invalid @enderror"
               id="unit"
               name="unit"
               value="{{ old('unit', $product->unit ?? '') }}"
               placeholder="Ex: kg, pièce, litre, boîte..."
               required>
        @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="alert_quantity" class="form-label">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Seuil d'alerte <span class="text-danger">*</span>
        </label>
        <input type="number"
               class="form-control @error('alert_quantity') is-invalid @enderror"
               id="alert_quantity"
               name="alert_quantity"
               value="{{ old('alert_quantity', $product->alert_quantity ?? '') }}"
               step="0.01"
               min="0"
               required>
        @error('alert_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Quantité minimale en stock avant alerte</div>
    </div>

    <!-- Image -->
    <div class="col-md-6 mb-3">
        <label for="image" class="form-label">
            <i class="bi bi-image me-1"></i>
            Image du produit
        </label>
        <input type="file"
               class="form-control @error('image') is-invalid @enderror"
               id="image"
               name="image"
               accept="image/*">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if(isset($product) && $product->image)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $product->image) }}"
                     alt="{{ $product->name }}"
                     class="img-thumbnail"
                     style="max-width: 100px; max-height: 100px;">
                <small class="text-muted d-block">Image actuelle</small>
            </div>
        @endif
    </div>

    <!-- Stock et Quantité -->
    <div class="col-md-6 mb-3">
        <label for="stock_id" class="form-label">
            <i class="bi bi-archive me-1"></i>
            Stock <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('stock_id') is-invalid @enderror"
                id="stock_id"
                name="stock_id"
                required>
            <option value="">Sélectionner un stock</option>
            @foreach($stocks as $stock)
                <option value="{{ $stock->id }}"
                        {{ old('stock_id', $selectedStockId ?? '') == $stock->id ? 'selected' : '' }}>
                    {{ $stock->name }} - {{ $stock->location }}
                </option>
            @endforeach
        </select>
        @error('stock_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- <div class="col-md-12 mb-3">
        <label for="quantity" class="form-label">
            <i class="bi bi-box-seam me-1"></i>
            Quantité initiale <span class="text-danger">*</span>
        </label>
        <input type="number"
               class="form-control @error('quantity') is-invalid @enderror"
               id="quantity"
               name="quantity"
               value="{{ old('quantity', $currentQuantity ?? 0) }}"
               step="0.01"
               min="0"
               required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Quantité disponible dans le stock sélectionné</div>
    </div> --}}

    <!-- Affichage de la marge -->
    <div class="col-12 mb-3">
        <div id="margin-display" class="badge bg-secondary fs-6"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul automatique de la marge
    const purchasePrice = document.getElementById('purchase_price');
    const salePrice = document.getElementById('sale_price');

    function calculateMargin() {
        const purchase = parseFloat(purchasePrice.value) || 0;
        const sale = parseFloat(salePrice.value) || 0;

        if (purchase > 0 && sale > 0) {
            const margin = ((sale - purchase) / purchase * 100).toFixed(2);
            const marginElement = document.getElementById('margin-display');
            if (marginElement) {
                marginElement.textContent = `Marge: ${margin}%`;
                marginElement.className = margin > 0 ? 'badge bg-success fs-6' : 'badge bg-danger fs-6';
            }
        }
    }

    purchasePrice.addEventListener('input', calculateMargin);
    salePrice.addEventListener('input', calculateMargin);

    // Calcul initial si les valeurs existent
    calculateMargin();

    // Preview de l'image
    const imageInput = document.getElementById('image');
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.getElementById('image-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'image-preview';
                    preview.className = 'mt-2';
                    imageInput.parentNode.appendChild(preview);
                }
                preview.innerHTML = `
                    <img src="${e.target.result}"
                         class="img-thumbnail"
                         style="max-width: 100px; max-height: 100px;">
                    <small class="text-muted d-block">Aperçu de la nouvelle image</small>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
