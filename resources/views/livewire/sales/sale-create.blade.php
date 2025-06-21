<div class="px-4 py-3 container-fluid">
    <form wire:submit.prevent="save">
        <div class="row g-4">
            <!-- Colonne principale gauche -->
            <div class="col-md-6">
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
                    <div class="p-4 card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="client_search" class="form-label fw-semibold">
                                    Client <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text"
                                           class="form-control @error('client_id') is-invalid @enderror"
                                           wire:model.live.debounce.300ms="client_search"
                                           placeholder="Rechercher un client par nom ou téléphone..."
                                           id="client_search"
                                           autocomplete="off">

                                    <!-- Loading indicator pour la recherche client -->
                                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                        <div wire:loading wire:target="updatedClientSearch,searchClients" class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Recherche...</span>
                                        </div>
                                        <i wire:loading.remove wire:target="updatedClientSearch,searchClients" class="bi bi-search text-muted"></i>
                                    </div>

                                    <!-- Bouton de recherche -->
                                    @if(!$show_client_search && strlen($client_search) >= 2)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary position-absolute top-50 end-0 translate-middle-y me-5"
                                                wire:click="searchClients"
                                                style="z-index: 10;">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    @endif

                                    <!-- Dropdown des clients -->
                                    @if($show_client_search && count($filtered_clients) > 0)
                                        <div class="mt-1 border-0 shadow-lg dropdown-menu show w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                            @foreach($filtered_clients as $client)
                                                <a href="#" class="px-3 py-2 dropdown-item d-flex align-items-center"
                                                   wire:click="selectClient({{ $client->id }})">
                                                    <div class="bg-opacity-10 avatar-sm bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-person text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold">{{ $client->name }}</div>
                                                        @if($client->phone)
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
                                    @if($client_search_loading)
                                        <div class="mt-1 border-0 shadow-lg dropdown-menu show w-100" style="z-index: 1000;">
                                            <div class="px-3 py-4 text-center">
                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
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
                                <label for="sale_date" class="form-label fw-semibold">
                                    Date de vente <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('sale_date') is-invalid @enderror"
                                       wire:model="sale_date"
                                       id="sale_date">
                                @error('sale_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($selected_client)
                            <div class="mt-3 bg-opacity-10 border-0 alert alert-info bg-info rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-opacity-20 avatar bg-info rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person fs-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $selected_client->name }}</h6>
                                        <div class="flex-wrap gap-3 d-flex">
                                            @if($selected_client->phone)
                                                <span class="text-muted small">
                                                    <i class="bi bi-telephone me-1"></i>{{ $selected_client->phone }}
                                                </span>
                                            @endif
                                            @if($selected_client->email)
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
                                @if($selected_category_id && $categories)
                                    @php
                                        $selectedCategory = $categories->firstWhere('id', $selected_category_id);
                                    @endphp
                                    @if($selectedCategory)
                                        <span class="badge bg-light text-primary ms-2">
                                            <i class="bi bi-tag me-1"></i>{{ $selectedCategory->name }}
                                        </span>
                                    @endif
                                @endif
                            </h5>
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
                                @if(count($items) > 0)
                                    <button type="button" class="btn btn-outline-light btn-sm"
                                            wire:click="clearCart"
                                            wire:confirm="Êtes-vous sûr de vouloir vider le panier ?">
                                        <i class="bi bi-trash me-1"></i>
                                        Vider le panier
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-4 card-body">
                        @if($show_product_search)
                            <div class="mb-4 border-0 card bg-light">
                                <div class="card-body">
                                    <!-- Loading pour les catégories -->
                                    <div wire:loading wire:target="loadCategories" class="py-3 text-center">
                                        <div class="spinner-border text-primary me-2" role="status"></div>
                                        <span class="text-muted">Chargement des catégories...</span>
                                    </div>

                                    <div wire:loading.remove wire:target="loadCategories">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="position-relative">
                                                    <input type="text"
                                                        class="form-control"
                                                        wire:model.live.debounce.300ms="product_search"
                                                        placeholder="Rechercher un produit par nom..."
                                                        autocomplete="off">

                                                    <!-- Loading indicator pour la recherche produit -->
                                                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                                        <div wire:loading wire:target="updatedProductSearch,searchProducts" class="spinner-border spinner-border-sm text-primary" role="status">
                                                            <span class="visually-hidden">Recherche...</span>
                                                        </div>
                                                        <i wire:loading.remove wire:target="updatedProductSearch,searchProducts" class="bi bi-search text-muted"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-select" wire:model="selected_category_id" wire:change="selectCategory">
                                                    <option value="">Toutes les catégories</option>
                                                    @if($categories)
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">
                                                                {{ $category->name }} ({{ $category->products_count }})
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-secondary w-100"
                                                        wire:click="toggleProductSearch">
                                                    <i class="bi bi-x me-1"></i>
                                                    Fermer
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Filtres par catégorie -->
                                        @if(!$product_search && !$selected_category_id && $categories)
                                            <div class="mt-3">
                                                <div class="flex-wrap gap-2 d-flex">
                                                    <button type="button"
                                                            class="btn btn-sm {{ !$selected_category_id ? 'btn-primary' : 'btn-outline-primary' }}"
                                                            wire:click="showAllProducts">
                                                        <i class="bi bi-grid me-1"></i>
                                                        Toutes les catégories
                                                    </button>
                                                    @foreach($categories as $category)
                                                        <button type="button"
                                                                wire:key="category-{{ $category->id }}"
                                                                class="btn btn-sm {{ $selected_category_id == $category->id ? 'btn-primary' : 'btn-outline-primary' }}"
                                                                wire:click="selectCategory({{ $category->id }})">
                                                            <span wire:loading.remove wire:target="selectCategory">
                                                                {{ $category->name }}
                                                                <span class="badge bg-light text-dark ms-1">{{ $category->products_count }}</span>
                                                            </span>
                                                            <span wire:loading wire:target="selectCategory" class="spinner-border spinner-border-sm"></span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Loading pour les produits -->
                                        <div wire:loading wire:target="selectCategory,loadProductsByCategory,searchProducts" class="mt-4 py-5 text-center">
                                            <div class="spinner-border text-primary me-2" role="status"></div>
                                            <span class="text-muted">Chargement des produits...</span>
                                        </div>

                                        <!-- Affichage des produits filtrés -->
                                        <div wire:loading.remove wire:target="selectCategory,loadProductsByCategory,searchProducts">
                                            @if(count($filtered_products) > 0)
                                                <div class="mt-4">
                                                    <!-- En-tête avec indicateur pour les produits filtrés -->
                                                    @if($selected_category_id || $product_search)
                                                        <div class="mb-3">
                                                            <div class="p-3 d-flex align-items-center justify-content-between bg-light rounded-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-funnel text-primary me-2"></i>
                                                                    <span class="fw-semibold">
                                                                        @if($product_search)
                                                                            Résultats pour "{{ $product_search }}"
                                                                        @elseif($selected_category_id && $categories)
                                                                            @php
                                                                                $selectedCategory = $categories->firstWhere('id', $selected_category_id);
                                                                            @endphp
                                                                            Produits dans {{ $selectedCategory->name ?? 'cette catégorie' }}
                                                                        @endif
                                                                    </span>
                                                                    <span class="badge bg-primary ms-2">{{ count($filtered_products) }} produit(s)</span>
                                                                </div>
                                                                @if($selected_category_id || $product_search)
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                                            wire:click="showAllProducts">
                                                                        <i class="bi bi-arrow-clockwise me-1"></i>
                                                                        Réinitialiser
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row g-2" style="max-height: 500px; overflow-y: auto;">
                                                        @foreach($filtered_products as $product)
                                                            <div class="col-md-6 col-lg-4" wire:key="product-{{ $product['id'] }}">
                                                                <div class="border shadow-sm card h-100 product-card"
                                                                    style="cursor: pointer;"
                                                                    wire:click="addProductToSale({{ $product['id'] }})">
                                                                    <div class="p-3 card-body">
                                                                        <div class="d-flex align-items-start">
                                                                            @if($product['image'])
                                                                                <img src="{{ asset('storage/' . $product['image']) }}"
                                                                                    alt="{{ $product['name'] }}"
                                                                                    class="rounded me-3"
                                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                                            @else
                                                                                <div class="bg-opacity-10 rounded bg-primary me-3 d-flex align-items-center justify-content-center"
                                                                                    style="width: 50px; height: 50px;">
                                                                                    <i class="bi bi-box text-primary"></i>
                                                                                </div>
                                                                            @endif
                                                                            <div class="flex-grow-1">
                                                                                <h6 class="mb-1 card-title fw-semibold">{{ $product['name'] }}</h6>
                                                                                <p class="mb-1 text-muted small">{{ number_format($product['sale_price_ttc'], 0, ',', ' ') }} Fbu</p>
                                                                                <div class="d-flex align-items-center justify-content-between">
                                                                                    <span class="badge {{ $product['available_stock'] <= ($product['alert_quantity'] ?? 5) ? 'bg-warning' : 'bg-success' }}">
                                                                                        Stock: {{ $product['available_stock'] }}
                                                                                    </span>
                                                                                    <i class="bi bi-plus-circle text-primary"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <!-- Bouton charger plus -->
                                                    @if($selected_category_id && count($filtered_products) >= $products_per_page)
                                                        <div class="mt-3 text-center">
                                                            <button type="button" class="btn btn-outline-primary"
                                                                    wire:click="loadMoreProducts">
                                                                <span wire:loading.remove wire:target="loadMoreProducts">
                                                                    <i class="bi bi-arrow-down-circle me-1"></i>
                                                                    Charger plus de produits
                                                                </span>
                                                                <span wire:loading wire:target="loadMoreProducts">
                                                                    <i class="spinner-border spinner-border-sm me-1"></i>
                                                                    Chargement...
                                                                </span>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Message si aucun produit trouvé -->
                                            @if(($product_search || $selected_category_id) && count($filtered_products) == 0)
                                                <div class="mt-3 border-0 alert alert-info rounded-3">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    @if($product_search)
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
                                <button type="button" class="btn btn-primary"
                                        wire:click="toggleProductSearch">
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
                                @if(count($items) > 0)
                                    <span class="badge bg-light text-success ms-2">{{ count($items) }}</span>
                                @endif
                            </h5>
                            @if(count($items) > 0)
                                <div class="d-flex gap-2">
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
                        @if(count($items) == 0)
                            <div class="p-4 py-5 text-center text-muted">
                                <div class="mb-3">
                                    <i class="opacity-25 bi bi-cart-x display-4"></i>
                                </div>
                                <h6 class="mb-2">Panier vide</h6>
                                <p class="mb-0 small">Ajoutez des produits pour commencer</p>
                            </div>
                        @else
                            <!-- Loading pour les actions du panier -->
                            <div wire:loading wire:target="updateItemQuantity,updateItemPrice,updateItemDiscount,removeItem" class="p-3 text-center bg-light">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                <span class="text-muted small">Mise à jour du panier...</span>
                            </div>

                            <div wire:loading.remove wire:target="updateItemQuantity,updateItemPrice,updateItemDiscount,removeItem">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 40px;">Image</th>
                                            <th>Nom</th>
                                            <th class="text-center" style="width: 100px;">Quantité</th>
                                            <th class="text-center" style="width: 100px;">Prix</th>
                                            <th class="text-center" style="width: 70px;">Remise</th>
                                            <th class="text-center" style="width: 120px;">Total</th>
                                            <th class="text-center" style="width: 40px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $index => $item)
                                            @php
                                                $quantity = floatval($item['quantity']);
                                                $price = floatval($item['sale_price']);
                                                $discount = floatval($item['discount'] ?? 0);
                                                $subtotal = $quantity * $price;
                                                $discountAmount = ($subtotal * $discount) / 100;
                                                $finalAmount = $subtotal - $discountAmount;
                                            @endphp
                                            <tr wire:key="cart-item-{{ $item['product_id'] }}-{{ $index }}">
                                                <td class="text-center">
                                                    @if(isset($item['image']) && $item['image'])
                                                        <img src="{{ asset('storage/' . $item['image']) }}"
                                                            alt="Product"
                                                            class="rounded"
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
                                                        <span class="fw-semibold">Produit #{{ $item['product_id'] }}</span>
                                                        <span class="badge {{ $item['available_stock'] <= 5 ? 'bg-warning' : 'bg-success' }} badge-sm">
                                                            Stock: {{ $item['available_stock'] }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number"
                                                        class="text-center form-control form-control-sm"
                                                        wire:change="updateItemQuantity({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['quantity'] }}"
                                                        min="0.01"
                                                        step="0.01"
                                                        style="width: 80px;">
                                                </td>
                                                <td class="text-center">
                                                    <input type="number"
                                                        class="text-center form-control form-control-sm"
                                                        wire:change="updateItemPrice({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['sale_price'] }}"
                                                        min="0"
                                                        step="0.01"
                                                        style="width: 100px;">
                                                </td>
                                                <td class="text-center">
                                                    <input type="number"
                                                        class="text-center form-control form-control-sm"
                                                        wire:change="updateItemDiscount({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['discount'] ?? 0 }}"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        style="width: 70px;">
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-bold text-success">{{ number_format($finalAmount, 0, ',', ' ') }} Fbu</span>
                                                    @if($discount > 0)
                                                        <small class="text-muted text-decoration-line-through d-block">{{ number_format($subtotal, 0, ',', ' ') }} Fbu</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        wire:click="removeItem({{ $item['product_id'] }})"
                                                        title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totaux dans le panier -->
                            <div class="p-3 border-top bg-light">
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="small">Sous-total:</span>
                                    <span class="fw-semibold">{{ number_format($total_subtotal, 0, ',', ' ') }} Fbu</span>
                                </div>
                                @if($total_discount > 0)
                                    <div class="mb-2 d-flex justify-content-between">
                                        <span class="small">Remise:</span>
                                        <span class="fw-semibold text-warning">-{{ number_format($total_discount, 0, ',', ' ') }} Fbu</span>
                                    </div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold text-success">{{ number_format($total_amount, 0, ',', ' ') }} Fbu</span>
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
                                <input type="number"
                                    id="paid_amount"
                                    min="0"
                                    step="0.01"
                                    placeholder="0"
                                    class="form-control @error('paid_amount') is-invalid @enderror"
                                    wire:model.live="paid_amount">
                                <span class="input-group-text">Fbu</span>
                            </div>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($total_amount > 0)
                            <div class="alert {{ $this->paymentStatus['type'] == 'success' ? 'alert-success' : ($this->paymentStatus['type'] == 'info' ? 'alert-info' : 'alert-warning') }} border-0 rounded-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi {{ $this->paymentStatus['type'] == 'success' ? 'bi-check-circle' : ($this->paymentStatus['type'] == 'info' ? 'bi-info-circle' : 'bi-exclamation-triangle') }} me-2"></i>
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
                                            ceil($total_amount / 10000) * 10000
                                        ];
                                        $quickAmounts = array_unique($quickAmounts);
                                        sort($quickAmounts);
                                    @endphp
                                    @foreach(array_slice($quickAmounts, 0, 3) as $index => $amount)
                                        @if($amount > $total_amount)
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm"
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
                            <textarea class="form-control"
                                      wire:model="note"
                                      id="note"
                                      rows="3"
                                      placeholder="Commentaires sur cette vente..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="border-0 shadow-sm card hover-lift">
                    <div class="p-4 card-body">
                        <div class="gap-2 d-grid">
                            <button type="submit"
                                    class="btn btn-success btn-lg"
                                    wire:target="save"
                                    wire:loading.attr="disabled"
                                    {{ empty($items) || !$client_id ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="save">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Enregistrer la vente
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
                                    @if(count($items) > 0)
                                        <button type="button"
                                                class="btn btn-outline-warning w-100"
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

                        @if(empty($items) || !$client_id)
                            <div class="mt-3 border-0 alert alert-warning rounded-3">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    @if(!$client_id && empty($items))
                                        Sélectionnez un client et ajoutez des produits pour continuer
                                    @elseif(!$client_id)
                                        Sélectionnez un client pour continuer
                                    @else
                                        Ajoutez des produits pour continuer
                                    @endif
                                </small>
                            </div>
                        @endif

                        @error('stock_error')
                            <div class="mt-3 border-0 alert alert-danger rounded-3">
                                <div class="mb-1 fw-semibold">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Erreurs de stock:
                                </div>
                                @if(is_array($message))
                                    @foreach($message as $error)
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

@push('styles')
<style>
/* Styles existants + améliorations pour le loading */
.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.02);
    transform: translateY(-2px);
}

.cart-item:hover {
    background-color: rgba(var(--bs-light), 0.5);
}

.bg-gradient-primary {
    background: linear-gradient(45deg, var(--bs-primary), #0056b3);
}

.bg-gradient-success {
    background: linear-gradient(45deg, var(--bs-success), #20c997);
}

.bg-gradient-info {
    background: linear-gradient(45deg, var(--bs-info), #17a2b8);
}

/* Loading states */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: 0.375rem;
}

.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.skeleton-text {
    height: 1rem;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
}

.skeleton-text:last-child {
    margin-bottom: 0;
}

.skeleton-product {
    height: 100px;
    border-radius: 0.375rem;
}

/* Amélioration des transitions */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-up {
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Spinner personnalisé */
.spinner-border-custom {
    width: 1.5rem;
    height: 1.5rem;
    border-width: 0.2em;
}

/* States pour les boutons de catégorie */
.category-btn {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.category-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.category-btn.active {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-primary-dark));
    color: white;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

/* Amélioration du scroll */
.smooth-scroll {
    scroll-behavior: smooth;
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(13, 110, 253, 0.3) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(13, 110, 253, 0.3);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(13, 110, 253, 0.5);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .hover-lift:hover {
        transform: none;
    }

    .product-card:hover {
        transform: none;
    }

    .category-btn:hover {
        transform: none;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-sm {
        padding: 0.25rem 0.4rem;
        font-size: 0.8rem;
    }
}

/* Loading state pour les cartes */
.card-loading {
    position: relative;
}

.card-loading::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(1px);
    z-index: 5;
    border-radius: inherit;
}

.card-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 2rem;
    height: 2rem;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--bs-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 6;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Pulse effect pour les éléments interactifs */
.pulse-on-load {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Amélioration des badges */
.badge-pulse {
    animation: badgePulse 2s ease-in-out infinite;
}

@keyframes badgePulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
    }
}

.badge-sm {
    font-size: 0.65rem;
    padding: 0.15rem 0.4rem;
}

/* Optimisation pour les transitions entre états */
.content-transition {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.content-fade-out {
    opacity: 0;
    transform: translateY(-10px);
}

.content-fade-in {
    opacity: 1;
    transform: translateY(0);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Amélioration de l'UX avec des feedbacks visuels

    // Animation lors de l'ajout au panier
    Livewire.on('productAdded', () => {
        // Petit effet visuel pour confirmer l'ajout
        const cartBadge = document.querySelector('.badge.bg-light.text-success');
        if (cartBadge) {
            cartBadge.classList.add('badge-pulse');
            setTimeout(() => {
                cartBadge.classList.remove('badge-pulse');
            }, 2000);
        }
    });

    // Smooth scroll pour les sections
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
                const searchInputs = document.querySelectorAll('input[placeholder*="Rechercher"]');
                searchInputs.forEach(input => {
                    if (input.offsetParent !== null && !input.hasAttribute('data-focused')) {
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
