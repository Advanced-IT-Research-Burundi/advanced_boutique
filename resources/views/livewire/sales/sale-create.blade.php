@push('styles')
    @include('livewire.sales.style')
@endpush
<div class="container-fluid">
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Colonne principale gauche -->

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <select class="mb-2 form-select" wire:model="selectedStock" wire:change="currentSelectStock">
                            <option value="">Selection stocks</option>
                            @if ($availablestocks)
                                @foreach ($availablestocks as $stock)
                                    <option value="{{ $stock->id }}">
                                        {{ $stock->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-6">
                        <select class="mb-2 form-select" wire:model="invoiceTye" wire:change="invoiceTye">
                            <option value="FACTURE">FACTURE</option>
                            <option value="PROFORMA">PROFORMA</option>
                            <option value="BON">BON</option>
                        </select>
                    </div>
                </div>


                <!-- Section Client -->
                <div class="mb-4 border-0 shadow-sm card hover-lift">
                    <div class="text-white border-0 card-header bg-gradient-primary rounded-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle me-2"></i>
                                Informations Client
                            </h5>
                            <a type="button" class="btn btn-outline-light btn-sm" href="{{ route('clients.create') }}">
                                <i class="bi bi-person-plus me-1"></i>
                                Nouveau client
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-0">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <label for="client_search" class="form-label fw-semibold me-2">
                                        Client <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('client_id') is-invalid @enderror"
                                        wire:model="client_search"
                                        wire:keydown.enter="searchClients"
                                        placeholder="Rechercher un client par nom ou téléphone..."
                                        autocomplete="off">
                                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                        <div wire:loading wire:target="searchClients"
                                            class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Recherche...</span>
                                        </div>
                                        <i wire:loading.remove wire:target="searchClients"
                                            class="bi bi-search text-muted"></i>
                                    </div>

                                    <!-- Bouton de recherche -->
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary position-absolute top-50 end-0 translate-middle-y me-5"
                                        wire:click="searchClients" style="z-index: 10;">
                                        <i class="bi bi-search"></i>
                                    </button>

                                    <!-- Dropdown des clients -->

                                        @if ($show_client_search && count($filtered_clients) > 0)
                                        <div  class="mt-4 border-0 shadow-lg dropdown-menu show w-100"
                                            style="margin-top: 50px !important; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                            @foreach ($filtered_clients as $client)
                                                <a href="#"
                                                    class="px-3 py-2 dropdown-item d-flex align-items-center"
                                                    wire:click="selectClient({{ $client->id }})">
                                                    <div
                                                        class="bg-opacity-10 avatar-sm bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-person text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold">{{ $client->name }}</div>
                                                        @if ($client->phone)
                                                            <small class="text-muted">
                                                                <i class="bi bi-telephone me-1"></i>{{ $client->phone }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Loading state pour les clients -->
                                    @if ($client_search_loading)
                                        <div class="mt-1 border-0 shadow-lg dropdown-menu show w-100"
                                            style="z-index: 1000;">
                                            <div class="px-3 py-2 text-center">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"
                                                    role="status"></div>
                                                <span class="text-muted">Recherche en cours...</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <label for="sale_date" class="form-label fw-semibold me-2">
                                        Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control @error('sale_date') is-invalid @enderror"
                                        wire:model="sale_date" id="sale_date">
                                </div>
                                @error('sale_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if ($selected_client)
                            <div class="mt-3 bg-opacity-10 border-0 alert alert-info bg-info rounded-3">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="p-2 bg-opacity-20 avatar bg-info rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person fs-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $selected_client->name }}</h6>
                                        <div class="flex-wrap gap-3 d-flex">
                                            @if ($selected_client->phone)
                                                <span class="text-muted small">
                                                    <i class="bi bi-telephone me-1"></i>{{ $selected_client->phone }}
                                                </span>
                                            @endif
                                            @if ($selected_client->email)
                                                <span class="text-muted small">
                                                    <i class="bi bi-envelope me-1"></i>{{ $selected_client->email }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        wire:click="clearClient">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section Recherche de Produits -->
                <div class="border-0 shadow-sm card hover-lift">
                    <div class="text-white border-0 card-header bg-gradient-primary rounded-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-search me-2"></i>
                                Rechercher des Produits
                                @if ($selected_category_id && $categories)
                                    @php
                                        $selectedCategory = $categories->firstWhere('id', $selected_category_id);
                                    @endphp
                                    @if ($selectedCategory)
                                        <span class="badge bg-light text-primary ms-2">
                                            <i class="bi bi-tag me-1"></i>{{ $selectedCategory->name }}
                                        </span>
                                    @endif
                                @endif
                            </h5>

                            @if ($show_product_search)
                                <div class="flex-fill mx-3" style="max-width: 400px;">
                                    <div class="position-relative">
                                        <input type="text"
                                            class="form-control form-control-sm bg-white border-0 shadow-sm"
                                            wire:model.live.debounce.300ms="product_search"
                                            placeholder="Rechercher un produit par nom..." autocomplete="off"
                                            style="padding-right: 40px;">
                                        <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                            <i class="bi bi-search text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="gap-2 d-flex">
                                <button type="button" class="btn btn-outline-light btn-sm"
                                    wire:click="toggleProductSearch">
                                    <span wire:loading.remove wire:target="toggleProductSearch">
                                        <i class="bi bi-search me-1"></i>
                                        {{ $show_product_search ? 'Fermer' : 'Rechercher' }}
                                    </span>
                                    <span wire:loading wire:target="toggleProductSearch">
                                        <i class="spinner-border spinner-border-sm me-1"></i>
                                        Chargement...
                                    </span>
                                </button>
                                @if (count($items) > 0)
                                    <button type="button" class="btn btn-outline-light btn-sm"
                                        wire:click="clearCart"
                                        wire:confirm="Êtes-vous sûr de vouloir vider le panier ?">
                                        <i class="bi bi-trash me-1"></i>
                                        Vider
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-4 card-body">
                        @if ($show_product_search)
                            <div class=" border-0 card bg-light">
                                <div class="card-body">
                                    <!-- Loading pour les catégories -->
                                    <div wire:loading wire:target="loadCategories" class="py-3 text-center">
                                        <div class="spinner-border text-primary me-2" role="status"></div>
                                        <span class="text-muted">Chargement des catégories...</span>
                                    </div>

                                    <div wire:loading.remove wire:target="loadCategories">

                                        <!-- Filtres par catégorie -->
                                        @if (!$product_search && !$selected_category_id && $categories)
                                            <div>
                                                <div class="flex-wrap gap-2 d-flex">
                                                    <button type="button"
                                                        class="btn btn-sm {{ !$selected_category_id ? 'btn-primary' : 'btn-outline-primary' }}"
                                                        wire:click="showAllProducts">
                                                        <i class="bi bi-grid me-1"></i>
                                                        Toutes les catégories
                                                    </button>
                                                    @foreach ($listeCategories as $id => $category)
                                                        <button type="button"
                                                            class="btn btn-sm {{ $selected_category_id == $id ? 'btn-primary' : 'btn-outline-primary' }}"
                                                            wire:click="selectCategory({{ $id }})">
                                                            {{ $category }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Loading pour les produits seulement -->
                                        <div wire:loading
                                            wire:target="selectCategory,loadProductsByCategory,searchProducts"
                                            class="py-5 mt-4 text-center">
                                            <div class="spinner-border text-primary me-2" role="status"></div>
                                            <span class="text-muted">Chargement des produits...</span>
                                        </div>

                                        <!-- Affichage des produits filtrés -->
                                        <div wire:loading.remove
                                            wire:target="selectCategory,loadProductsByCategory,searchProducts">
                                            @if (count($filtered_products) > 0)
                                                <div>
                                                    @if ($selected_category_id || $product_search)
                                                        <div class="mb-3">
                                                            <div
                                                                class="p-3 d-flex align-items-center justify-content-between bg-light rounded-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-funnel text-primary me-2"></i>
                                                                    <span class="fw-semibold">
                                                                        @if ($product_search)
                                                                            Résultats pour "{{ $product_search }}"
                                                                        @elseif($selected_category_id && $categories)
                                                                            @php
                                                                                $selectedCategory = $categories->firstWhere(
                                                                                    'id',
                                                                                    $selected_category_id,
                                                                                );
                                                                            @endphp
                                                                            Produits dans
                                                                            {{ $selectedCategory->name ?? 'cette catégorie' }}
                                                                        @endif
                                                                    </span>
                                                                    <span
                                                                        class="badge bg-primary ms-2">{{ count($filtered_products) }}
                                                                        produit(s)</span>
                                                                </div>
                                                                @if ($selected_category_id || $product_search)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-secondary"
                                                                        wire:click="showAllProducts">
                                                                        <i class="bi bi-arrow-clockwise me-1"></i>
                                                                        Réinitialiser
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row g-2" style="max-height: 500px; overflow-y: auto;">
                                                        @foreach ($filtered_products as $product)
                                                            <div class="col-md-6 col-lg-4"
                                                                wire:key="product-{{ $product['id'] }}">
                                                                <div class="border shadow-sm card h-100 product-card"
                                                                    style="cursor: pointer;"
                                                                    wire:click="addProductToSale({{ $product['id'] }})">
                                                                    <div class="p-3 card-body">
                                                                        <div class="d-flex align-items-start">
                                                                            @if ($product['image'])
                                                                                <img src="{{ asset('storage/' . $product['image']) }}"
                                                                                    alt="{{ $product['name'] }}"
                                                                                    class="rounded me-3"
                                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                                            @else
                                                                                <div class="bg-opacity-10 rounded bg-primary me-3 d-flex align-items-center justify-content-center"
                                                                                    style="width: 50px; height: 50px;">
                                                                                    <i
                                                                                        class="bi bi-box text-primary"></i>
                                                                                </div>
                                                                            @endif
                                                                            <div class="flex-grow-1">
                                                                                <h6
                                                                                    class="mb-1 card-title fw-semibold">
                                                                                    {{ $product['name'] }}</h6>

                                                                                <p class="mb-1 text-muted small">
                                                                                    {{ number_format($product['sale_price_ttc'], 0, ',', ' ') }}
                                                                                    Fbu</p>
                                                                                <div
                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                    <span
                                                                                        class="badge {{ $product['quantity_disponible'] <= 2 ? 'bg-warning' : 'bg-success' }}">
                                                                                        {{ $product['code'] }} qte:
                                                                                        {{ $product['quantity_disponible'] }}
                                                                                    </span>
                                                                                    <i
                                                                                        class="bi bi-plus-circle text-primary"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <!-- Bouton charger plus -->
                                                    @if ($selected_category_id && count($filtered_products) >= $products_per_page)
                                                        <div class="mt-3 text-center">
                                                            <button type="button" class="btn btn-outline-primary"
                                                                wire:click="loadMoreProducts">
                                                                <span wire:loading.remove
                                                                    wire:target="loadMoreProducts">
                                                                    <i class="bi bi-arrow-down-circle me-1"></i>
                                                                    Charger plus de produits
                                                                </span>
                                                                <span wire:loading wire:target="loadMoreProducts">
                                                                    <div class="spinner-border spinner-border-sm me-1"
                                                                        role="status"></div>
                                                                    Chargement...
                                                                </span>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Message si aucun produit trouvé -->
                                            @if (($product_search || $selected_category_id) && count($filtered_products) == 0)
                                                <div class="mt-3 border-0 alert alert-info rounded-3">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    @if ($product_search)
                                                        Aucun produit disponible trouvé pour "{{ $product_search }}"
                                                    @else
                                                        Aucun produit disponible dans cette catégorie
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="py-5 text-center text-muted">
                                <div class="mb-4">
                                    <i class="opacity-25 bi bi-search display-1"></i>
                                </div>
                                <h5 class="mb-2">Rechercher des produits</h5>
                                <p class="mb-3">Cliquez sur "Rechercher" pour parcourir et ajouter des produits</p>
                                <button type="button" class="btn btn-primary" wire:click="toggleProductSearch">
                                    <i class="bi bi-search me-2"></i>
                                    Rechercher des produits
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Panier et Paiement -->
            <div class="col-xl-6">
                <!-- Section Produits Sélectionnés -->
                <div class="mb-4 border-0 shadow-sm card hover-lift">
                    <div class="text-white border-0 card-header bg-gradient-success rounded-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-cart me-2"></i>
                                Panier
                                @if (count($items) > 0)
                                    <span class="badge bg-light text-success ms-2">{{ count($items) }}</span>
                                @endif

                                <!-- Indicateur de chargement dans le header -->
                                <span wire:loading
                                    wire:target="updateItemQuantity,updateItemPrice,updateItemDiscount,removeItem,addToCart,clearCart,validateSale"
                                    class="ms-2">
                                    <div class="spinner-border spinner-border-sm text-light" role="status"
                                        style="width: 1rem; height: 1rem;">
                                        <span class="visually-hidden">Chargement...</span>
                                    </div>
                                </span>
                            </h5>
                            @if (count($items) > 0)
                                <div class="gap-2 d-flex">
                                    <button type="button" wire:click="validateSale" class="btn btn-primary btn-sm">
                                        <i class="bi bi-check me-1"></i>
                                        Valider
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-sm"
                                        wire:click="clearCart"
                                        wire:confirm="Êtes-vous sûr de vouloir vider le panier ?">
                                        <i class="bi bi-trash me-1"></i>
                                        Vider
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-0 card-body">
                        @if (count($items) == 0)
                            <div class="p-4 py-5 text-center text-muted">
                                <div class="mb-3">
                                    <i class="opacity-25 bi bi-cart-x display-4"></i>
                                </div>
                                <h6 class="mb-2">Panier vide</h6>
                                <p class="mb-0 small">Ajoutez des produits pour commencer</p>
                            </div>
                        @else
                            <div>
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 40px;">Image</th>
                                            <th>Nom</th>
                                            <th class="text-center" style="width: 100px;">Quantité</th>
                                            <th class="text-center" style="width: 100px;">Prix</th>
                                            <th class="text-sm-center" style="width: 70px;">Remise(%)</th>
                                            <th class="text-center" style="width: 120px;">Total</th>
                                            <th class="text-center" style="width: 40px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $index => $item)
                                            @php
                                                $quantity = floatval($item['quantity']);
                                                $price = floatval($item['sale_price']);
                                                $discount = floatval($item['discount'] ?? 0);
                                                $subtotal = $quantity * $price;
                                                $discountAmount = ($subtotal * $discount) / 100;
                                                $finalAmount = $subtotal - $discountAmount;
                                                $availableStock = $item['available_stock'] ?? 0;
                                                $isOverStock = $quantity > $availableStock;
                                            @endphp
                                            <tr wire:key="cart-item-{{ $item['product_id'] }}-{{ $index }}"
                                                class="{{ $isOverStock ? 'table-danger border-danger' : '' }}">
                                                <td class="text-center">
                                                    @if (isset($item['image']) && $item['image'])
                                                        <img src="{{ asset('storage/' . $item['image']) }}"
                                                            alt="Product" class="rounded"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-opacity-10 rounded bg-primary d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px;">
                                                            <i class="bi bi-box text-primary"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="fw-semibold">{{ Str::limit($item['name'], 10, '...') }}
                                                            #{{ $item['code'] }}</span>
                                                        <div class="gap-2 d-flex align-items-center">
                                                            <span
                                                                class="badge {{ $availableStock <= 2 ? 'bg-warning' : 'bg-success' }} badge-sm">
                                                                Stock: {{ $availableStock }}
                                                            </span>
                                                            @if ($isOverStock)
                                                                <span class="badge bg-danger badge-sm">
                                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                                    Stock dépassé
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="position-relative">
                                                        <input type="number"
                                                            class="text-center form-control form-control-sm {{ $isOverStock ? 'is-invalid' : '' }}"
                                                            wire:change="updateItemQuantity({{ $item['product_id'] }}, $event.target.value)"
                                                            value="{{ $item['quantity'] }}" min="0.01"
                                                            max="{{ $availableStock }}" step="0.01"
                                                            style="width: 80px;"
                                                            data-product-id="{{ $item['product_id'] }}"
                                                            data-available-stock="{{ $availableStock }}"
                                                            onkeyup="validateQuantity(this)">

                                                        @if ($isOverStock)
                                                            <div class="invalid-tooltip">
                                                                Max: {{ $availableStock }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number"
                                                        class="text-center form-control form-control-sm"
                                                        wire:change="updateItemPrice({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['sale_price'] }}" min="0"
                                                        step="0.01" style="width: 100px;">
                                                </td>
                                                <td class="text-center">
                                                    <input type="number"
                                                        class="text-center form-control form-control-sm"
                                                        wire:change="updateItemDiscount({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['discount'] ?? 0 }}" min="0"
                                                        max="100" step="0.01" style="width: 70px;">
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span
                                                            class="fw-bold {{ $isOverStock ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($finalAmount, 0, ',', ' ') }} Fbu
                                                        </span>
                                                        @if ($discount > 0)
                                                            <small class="text-muted text-decoration-line-through">
                                                                {{ number_format($subtotal, 0, ',', ' ') }} Fbu
                                                            </small>
                                                        @endif
                                                        @if ($isOverStock)
                                                            <small class="text-danger">
                                                                <i class="bi bi-exclamation-triangle"></i>
                                                                Stock insuffisant
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        wire:click="removeItem({{ $item['product_id'] }})"
                                                        title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Alerte globale pour les stocks insuffisants -->
                                @php
                                    $hasOverStockItems = collect($items)->contains(function ($item) {
                                        return floatval($item['quantity']) > ($item['available_stock'] ?? 0);
                                    });
                                @endphp

                                @if ($hasOverStockItems)
                                    <div class="mx-3 mb-3 border-0 alert alert-danger rounded-3" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <div>
                                                <strong>Attention!</strong>
                                                Certains produits ont une quantité supérieure au stock disponible.
                                                <br>
                                                <small>
                                                    Veuillez ajuster les quantités avant de procéder à la vente.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Totaux dans le panier -->
                            <div class="p-3 border-top bg-light">
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="small">Sous-total:</span>
                                    <span class="fw-semibold">{{ number_format($total_subtotal, 0, ',', ' ') }}
                                        Fbu</span>
                                </div>
                                @if ($total_discount > 0)
                                    <div class="mb-2 d-flex justify-content-between">
                                        <span class="small">Remise:</span>
                                        <span
                                            class="fw-semibold text-warning">-{{ number_format($total_discount, 0, ',', ' ') }}
                                            Fbu</span>
                                    </div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold text-success">{{ number_format($total_amount, 0, ',', ' ') }}
                                        Fbu</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section Paiement -->
                <div class="mb-4 border-0 shadow-sm card hover-lift">
                    <div class="text-white border-0 card-header bg-gradient-info rounded-top">
                        <h5 class="mb-0">
                            <i class="bi bi-credit-card me-2"></i>
                            Paiement
                        </h5>
                    </div>
                    <div class="p-4 card-body">
                        <div class="mb-3">
                            <label for="paid_amount" class="form-label fw-semibold">
                                Montant payé <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" id="paid_amount" min="0" step="0.01"
                                    placeholder="0"
                                    class="form-control {{ $this->paymentStatus['type'] == 'success' ? 'disabled' : '' }} @error('paid_amount') is-invalid @enderror"
                                    wire:model.live="paid_amount"
                                    {{ $this->paymentStatus['type'] == 'success' ? 'disabled' : '' }}>

                                <span class="input-group-text">Fbu</span>
                            </div>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($total_amount > 0)
                            <div
                                class="alert {{ $this->paymentStatus['type'] == 'success' ? 'alert-success' : ($this->paymentStatus['type'] == 'info' ? 'alert-info' : 'alert-warning') }} border-0 rounded-3">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="bi {{ $this->paymentStatus['type'] == 'success' ? 'bi-check-circle' : ($this->paymentStatus['type'] == 'info' ? 'bi-info-circle' : 'bi-exclamation-triangle') }} me-2"></i>
                                    <small>{{ $this->paymentStatus['message'] }}</small>
                                </div>
                            </div>

                            <!-- Boutons de montant rapide -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Montant rapide:</label>
                                <div class="flex-wrap gap-2 d-flex">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        wire:click="setExactAmount">
                                        Exact
                                    </button>
                                    @php
                                        $quickAmounts = [
                                            ceil($total_amount / 1000) * 1000,
                                            ceil($total_amount / 5000) * 5000,
                                            ceil($total_amount / 10000) * 10000,
                                        ];
                                        $quickAmounts = array_unique($quickAmounts);
                                        sort($quickAmounts);
                                    @endphp
                                    @foreach (array_slice($quickAmounts, 0, 3) as $index => $amount)
                                        @if ($amount > $total_amount)
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                wire:click="setQuickAmount({{ $amount }})"
                                                wire:key="quick-amount-{{ $index }}-{{ $amount }}">
                                                {{ number_format($amount, 0, ',', ' ') }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="note" class="form-label fw-semibold">Note (optionnel)</label>
                            <textarea class="form-control" wire:model="note" id="note" rows="3"
                                placeholder="Commentaires sur cette vente..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="border-0 shadow-sm card hover-lift">
                    <div class="p-4 card-body">
                        <div class="gap-2 d-grid">
                            @php
                                $hasStockErrors = collect($items)->contains(function ($item) {
                                    return floatval($item['quantity']) > ($item['available_stock'] ?? 0);
                                });
                            @endphp

                            <button type="submit" class="btn btn-success btn-lg" wire:target="save"
                                wire:loading.attr="disabled"
                                {{ empty($items) || !$client_id || $hasStockErrors ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="save">
                                    <i class="bi bi-check-circle me-2"></i>
                                    {{ $hasStockErrors ? 'Corriger les stocks avant de sauvegarder' : 'Enregistrer la vente' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <i class="spinner-border spinner-border-sm me-2"></i>
                                    Enregistrement...
                                </span>
                            </button>

                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Retour
                                    </a>
                                </div>
                                <div class="col-6">
                                    @if (count($items) > 0)
                                        <button type="button" class="btn btn-outline-warning w-100"
                                            wire:click="clearCart"
                                            wire:confirm="Êtes-vous sûr de vouloir vider le panier ?">
                                            <span wire:loading.remove wire:target="clearCart">
                                                <i class="bi bi-trash me-1"></i>
                                                Vider
                                            </span>
                                            <span wire:loading wire:target="clearCart">
                                                <i class="spinner-border spinner-border-sm me-1"></i>
                                                Vidage...
                                            </span>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary w-100" disabled>
                                            <i class="bi bi-trash me-1"></i>
                                            Vider
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (empty($items) || !$client_id || $hasStockErrors)
                            <div class="mt-3 border-0 alert alert-warning rounded-3">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    @if (!$client_id && empty($items))
                                        Sélectionnez un client et ajoutez des produits pour continuer
                                    @elseif(!$client_id)
                                        Sélectionnez un client pour continuer
                                    @elseif(empty($items))
                                        Ajoutez des produits pour continuer
                                    @elseif($hasStockErrors)
                                        Corrigez les quantités supérieures au stock disponible avant de continuer
                                    @endif
                                </small>
                            </div>
                        @endif

                        @error('stock_validation')
                            <div class="mt-3 border-0 alert alert-danger rounded-3">
                                <div class="mb-1 fw-semibold">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Erreur de validation:
                                </div>
                                <small>{{ $message }}</small>
                            </div>
                        @enderror

                        @error('stock_error')
                            <div class="mt-3 border-0 alert alert-danger rounded-3">
                                <div class="mb-1 fw-semibold">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Erreurs de stock:
                                </div>
                                @if (is_array($message))
                                    @foreach ($message as $error)
                                        <small class="d-block">• {{ $error }}</small>
                                    @endforeach
                                @else
                                    <small>{{ $message }}</small>
                                @endif
                            </div>
                        @enderror

                        @error('error')
                            <div class="mt-3 border-0 alert alert-danger rounded-3">
                                <div class="mb-1 fw-semibold">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Erreur:
                                </div>
                                <small>{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation en temps réel de la quantité
            window.validateQuantity = function(input) {
                const quantity = parseFloat(input.value) || 0;
                const availableStock = parseFloat(input.dataset.availableStock) || 0;
                const productId = input.dataset.productId;

                // Supprimer les classes précédentes
                input.classList.remove('is-invalid', 'is-valid');

                // Supprimer l'ancien tooltip s'il existe
                const existingTooltip = input.parentNode.querySelector('.invalid-tooltip');
                if (existingTooltip) {
                    existingTooltip.remove();
                }

                if (quantity > availableStock) {
                    // Ajouter la classe d'erreur
                    input.classList.add('is-invalid');

                    // Créer le tooltip d'erreur
                    const tooltip = document.createElement('div');
                    tooltip.className = 'invalid-tooltip';
                    tooltip.textContent = `Max: ${availableStock}`;
                    input.parentNode.appendChild(tooltip);

                    // Marquer la ligne en rouge
                    const row = input.closest('tr');
                    if (row) {
                        row.classList.add('table-danger', 'border-danger');
                    }

                    // Afficher une alerte toast (optionnel)
                    showStockAlert(productId, quantity, availableStock);
                } else if (quantity > 0) {
                    // Quantité valide
                    input.classList.add('is-valid');

                    // Retirer la couleur rouge de la ligne
                    const row = input.closest('tr');
                    if (row) {
                        row.classList.remove('table-danger', 'border-danger');
                    }
                }
            };

            // Fonction pour afficher une alerte toast
            window.showStockAlert = function(productId, quantity, availableStock) {
                // Éviter les alertes répétitives
                if (window.lastAlertTime && Date.now() - window.lastAlertTime < 2000) {
                    return;
                }
                window.lastAlertTime = Date.now();

                // Créer un toast personnalisé
                const toast = document.createElement('div');
                toast.className = 'toast-custom alert alert-warning position-fixed';
                toast.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
        `;

                toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <div>
                    <strong>Stock insuffisant!</strong><br>
                    <small>Produit #${productId}: Stock disponible ${availableStock}, quantité demandée ${quantity}</small>
                </div>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

                document.body.appendChild(toast);

                // Auto-supprimer après 5 secondes
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.style.animation = 'slideOutRight 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 5000);
            };

            // Amélioration de l'UX avec des feedbacks visuels
            Livewire.on('productAdded', () => {
                const cartBadge = document.querySelector('.badge.bg-light.text-success');
                if (cartBadge) {
                    cartBadge.classList.add('badge-pulse');
                    setTimeout(() => {
                        cartBadge.classList.remove('badge-pulse');
                    }, 2000);
                }
            });

            // Gestion des événements Livewire pour les alertes
            Livewire.on('error', (event) => {
                showToast(event.message, 'error');
            });

            Livewire.on('success', (event) => {
                showToast(event.message, 'success');
            });

            // Fonction générique pour afficher des toasts
            window.showToast = function(message, type = 'info') {
                const toast = document.createElement('div');
                const alertClass = type === 'error' ? 'alert-danger' :
                    type === 'success' ? 'alert-success' : 'alert-info';

                toast.className = `toast-custom alert ${alertClass} position-fixed`;
                toast.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
        `;

                const icon = type === 'error' ? 'bi-exclamation-triangle' :
                    type === 'success' ? 'bi-check-circle' : 'bi-info-circle';

                toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi ${icon} me-2"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close ms-2" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.style.animation = 'slideOutRight 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, type === 'error' ? 7000 : 4000);
            };

            // Smooth scroll pour les sections
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Auto-focus sur les champs de recherche quand ils deviennent visibles
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        const searchInputs = document.querySelectorAll(
                            'input[placeholder*="Rechercher"]');
                        searchInputs.forEach(input => {
                            if (input.offsetParent !== null && !input.hasAttribute(
                                    'data-focused')) {
                                input.setAttribute('data-focused', 'true');
                                setTimeout(() => input.focus(), 100);
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>
@endpush
