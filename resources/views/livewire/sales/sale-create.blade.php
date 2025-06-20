<div class="px-4 py-3 container-fluid">
    <form wire:submit.prevent="save">
        <div class="row g-4">
            <!-- Colonne principale gauche -->
            <div class="col-xl-8">
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
                                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>

                                    @if($client_search && count($filtered_clients) > 0)
                                        <div  class="mt-1 border-0 shadow-lg dropdown-menu show w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
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
                                @if($selected_category_id)
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
                                        wire:click="$set('show_product_search', true)">
                                    <i class="bi bi-search me-1"></i>
                                    Rechercher
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
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="position-relative">
                                                <input type="text"
                                                    class="form-control"
                                                    wire:model.live.debounce.300ms="product_search"
                                                    placeholder="Rechercher un produit par nom..."
                                                    autocomplete="off">
                                                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select" wire:model="selected_category_id" wire:change="selectCategory">
                                            <option value="">Toutes les catégories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }} ({{ $category->products_count }})
                                                </option>
                                            @endforeach
                                        </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-secondary w-100"
                                                    wire:click="$set('show_product_search', false)">
                                                <i class="bi bi-x me-1"></i>
                                                Fermer
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Indicateur de catégorie sélectionnée -->
                                    @if($selected_category_id)
                                        @php
                                            $selectedCategory = $categories->firstWhere('id', $selected_category_id);
                                        @endphp
                                        @if($selectedCategory)
                                            <div class="mt-3 p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <div class="p-2 bg-primary bg-opacity-20 rounded-circle me-3">
                                                            <i class="bi bi-funnel text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1 text-primary fw-bold">
                                                                Catégorie sélectionnée: {{ $selectedCategory->name }}
                                                            </h6>
                                                            <small class="text-muted">
                                                                Affichage des produits de cette catégorie uniquement
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                            wire:click="showAllProducts">
                                                        <i class="bi bi-x me-1"></i>
                                                        Afficher tout
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Filtres par catégorie -->
                                    @if(!$product_search && !$selected_category_id)
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
                                                        {{ $category->name }}
                                                        <span class="badge bg-light text-dark ms-1">{{ $category->products_count }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Affichage des produits par catégorie -->
                                    @if(count($products_by_category) > 0)
                                        <div class="mt-4" style="max-height: 500px; overflow-y: auto;">
                                            @foreach($products_by_category as $category_data)
                                                @if(count($category_data['products']) > 0)
                                                    <div class="mb-4 category-section">
                                                        <div class="mb-3 d-flex align-items-center">
                                                            <h6 class="mb-0 text-primary fw-bold">
                                                                <i class="bi bi-tag me-2"></i>
                                                                {{ $category_data['name'] }}
                                                            </h6>
                                                            <span class="badge bg-primary ms-2">{{ count($category_data['products']) }}</span>
                                                            <hr class="flex-grow-1 ms-3">
                                                        </div>

                                                        <div class="row g-2">
                                                            @foreach($category_data['products'] as $product)
                                                                <div class="col-md-6 col-lg-4">
                                                                    <div class="border shadow-sm card h-100 product-card"
                                                                        style="cursor: pointer;"
                                                                        wire:click="addProductToSale({{ $product->id }})">
                                                                        <div class="p-3 card-body">
                                                                            <div class="d-flex align-items-start">
                                                                                @if($product->image)
                                                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                                                        alt="{{ $product->name }}"
                                                                                        class="rounded me-3"
                                                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                                                @else
                                                                                    <div class="bg-opacity-10 rounded bg-primary me-3 d-flex align-items-center justify-content-center"
                                                                                        style="width: 50px; height: 50px;">
                                                                                        <i class="bi bi-box text-primary"></i>
                                                                                    </div>
                                                                                @endif
                                                                                <div class="flex-grow-1">
                                                                                    <h6 class="mb-1 card-title fw-semibold">{{ $product->name }}</h6>
                                                                                    <p class="mb-1 text-muted small">{{ number_format($product->sale_price, 0, ',', ' ') }} Fbu</p>
                                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                                        <span class="badge {{ $product->available_stock <= ($product->alert_quantity ?? 5) ? 'bg-warning' : 'bg-success' }}">
                                                                                            Stock: {{ $product->available_stock }}
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
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Affichage des produits filtrés -->
                                    @if(count($filtered_products) > 0)
                                        <div class="mt-2 row g-2" style="max-height: 500px; overflow-y: auto;">
                                            <!-- En-tête avec indicateur pour les produits filtrés -->
                                            @if($selected_category_id || $product_search)
                                                <div class="col-12 mb-3">
                                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-funnel text-primary me-2"></i>
                                                            <span class="fw-semibold">
                                                                @if($product_search)
                                                                    Résultats pour "{{ $product_search }}"
                                                                @elseif($selected_category_id)
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

                                            @foreach($filtered_products as $product)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="border shadow-sm card h-100 product-card"
                                                        style="cursor: pointer;"
                                                        wire:click="addProductToSale({{ $product->id }})">
                                                        <div class="p-3 card-body">
                                                            <div class="d-flex align-items-start">
                                                                @if($product->image)
                                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                                        alt="{{ $product->name }}"
                                                                        class="rounded me-3"
                                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-opacity-10 rounded bg-primary me-3 d-flex align-items-center justify-content-center"
                                                                        style="width: 50px; height: 50px;">
                                                                        <i class="bi bi-box text-primary"></i>
                                                                    </div>
                                                                @endif
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1 card-title fw-semibold">{{ $product->name }}</h6>
                                                                    <p class="mb-1 text-muted small">{{ number_format($product->sale_price, 0, ',', ' ') }} Fbu</p>
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <span class="badge {{ $product->available_stock <= ($product->alert_quantity ?? 5) ? 'bg-warning' : 'bg-success' }}">
                                                                            Stock: {{ $product->available_stock }}
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
                                    @endif

                                    <!-- Message si aucun produit trouvé -->
                                    @if($product_search && count($filtered_products) == 0 && count($products_by_category) == 0)
                                        <div class="mt-3 border-0 alert alert-info rounded-3">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Aucun produit disponible trouvé pour "{{ $product_search }}"
                                        </div>
                                    @endif
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
                                        wire:click="$set('show_product_search', true)">
                                    <i class="bi bi-search me-2"></i>
                                    Rechercher des produits
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Panier et Paiement -->
            <div class="col-xl-4">
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
                                <button type="button" class="btn btn-outline-light btn-sm"
                                        wire:click="clearCart"
                                        wire:confirm="Êtes-vous sûr de vouloir vider le panier ?">
                                    <i class="bi bi-trash me-1"></i>
                                    Vider
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(empty($items))
                            <div class="p-4 py-5 text-center text-muted">
                                <div class="mb-3">
                                    <i class="opacity-25 bi bi-cart-x display-4"></i>
                                </div>
                                <h6 class="mb-2">Panier vide</h6>
                                <p class="mb-0 small">Ajoutez des produits pour commencer</p>
                            </div>
                        @else
                            <div style="max-height: 400px; overflow-y: auto;">
                                @foreach($items as $item)
                                    @php
                                        $product = $products->find($item['product_id']);
                                        $quantity = floatval($item['quantity']);
                                        $price = floatval($item['sale_price']);
                                        $discount = floatval($item['discount'] ?? 0);
                                        $subtotal = $quantity * $price;
                                        $discountAmount = ($subtotal * $discount) / 100;
                                        $finalAmount = $subtotal - $discountAmount;
                                    @endphp
                                    <div class="p-3 border-bottom cart-item" wire:key="cart-item-{{ $item['product_id'] }}">
                                        <div class="d-flex align-items-start">
                                            @if($product && $product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="rounded me-3"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-opacity-10 rounded bg-primary me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bi bi-box text-primary"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold">{{ $product->name ?? 'Produit inconnu' }}</h6>

                                                <!-- Quantité -->
                                                <div class="mb-2">
                                                    <label class="form-label small">Quantité:</label>
                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        wire:change="updateItemQuantity({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['quantity'] }}"
                                                        min="0.01"
                                                        step="0.01"
                                                        style="width: 80px;">
                                                </div>

                                                <!-- Prix unitaire -->
                                                <div class="mb-2">
                                                    <label class="form-label small">Prix:</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number"
                                                            class="form-control"
                                                            wire:change="updateItemPrice({{ $item['product_id'] }}, $event.target.value)"
                                                            value="{{ $item['sale_price'] }}"
                                                            min="0"
                                                            step="0.01">
                                                        <span class="input-group-text">Fbu</span>
                                                    </div>
                                                </div>

                                                <!-- Remise -->
                                                <div class="mb-2">
                                                    <label class="form-label small">Remise (%):</label>
                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        wire:change="updateItemDiscount({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['discount'] ?? 0 }}"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        style="width: 70px;">
                                                </div>

                                                <!-- Total et stock -->
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold text-success">{{ number_format($finalAmount, 0, ',', ' ') }} Fbu</div>
                                                        @if($discount > 0)
                                                            <small class="text-muted text-decoration-line-through">{{ number_format($subtotal, 0, ',', ' ') }} Fbu</small>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge {{ $item['available_stock'] <= ($product->alert_quantity ?? 5) ? 'bg-warning' : 'bg-success' }} d-block mb-1">
                                                            Stock: {{ $item['available_stock'] }}
                                                        </span>
                                                        @if($item['quantity'] > $item['available_stock'])
                                                            <small class="text-danger d-block">
                                                                <i class="bi bi-exclamation-triangle"></i>
                                                                Insuffisant
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm ms-2"
                                                    wire:click="removeItem({{ $item['product_id'] }})"
                                                    title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Totaux dans le panier -->
                            <div class="p-3 border-top bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Sous-total:</span>
                                    <span class="fw-semibold">{{ number_format($total_subtotal, 0, ',', ' ') }} Fbu</span>
                                </div>
                                @if($total_discount > 0)
                                    <div class="d-flex justify-content-between mb-2">
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
                                            @php $amount = ceil($amount / 1000) * 1000; @endphp
                                            <button
                                                type="button"
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
                                            <i class="bi bi-trash me-1"></i>
                                            Vider
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
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
    <!-- Styles CSS additionnels -->
<style>
.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.02);
}

.cart-item:hover {
    background-color: rgba(var(--bs-light), 0.5);
}

.bg-gradient-primary {
    background: var(--bs-primary);
}

.bg-gradient-success {
    background: linear-gradient(45deg, var(--bs-success), #20c997);
}

.bg-gradient-info {
    background: linear-gradient(45deg, var(--bs-info), #17a2b8);
}

.summary-item {
    padding: 0.25rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.summary-item:last-child {
    border-bottom: none;
}

.avatar {
    width: 60px;
    height: 60px;
}

.avatar-sm {
    width: 32px;
    height: 32px;
}

/* Styles spécifiques pour le panier */
.cart-item {
    transition: background-color 0.2s ease;
}

.cart-item:last-child {
    border-bottom: none !important;
}

/* Styles pour les indicateurs de catégorie */
.category-indicator {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));
    border: 1px solid rgba(13, 110, 253, 0.2);
    backdrop-filter: blur(10px);
}

.category-filter-section {
    background: rgba(248, 249, 250, 0.8);
    border-radius: 0.5rem;
    padding: 1rem;
    margin: 1rem 0;
}

.products-header {
    background: linear-gradient(90deg, rgba(13, 110, 253, 0.05), transparent);
    border-left: 4px solid var(--bs-primary);
    padding: 0.75rem 1rem;
    margin: 1rem 0;
    border-radius: 0 0.375rem 0.375rem 0;
}

/* Animation pour les transitions de catégorie */
.category-transition {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive pour mobile */
@media (max-width: 1199px) {
    .col-xl-8 {
        order: 2;
    }
    .col-xl-4 {
        order: 1;
    }
}

@media (max-width: 768px) {
    .table-responsive table th,
    .table-responsive table td {
        white-space: nowrap;
        font-size: 0.875rem;
    }

    .hover-lift:hover {
        transform: none;
    }

    .cart-item .input-group-sm {
        width: 100%;
    }

    /* Ajustements pour les filtres de catégorie sur mobile */
    .category-filter-section {
        padding: 0.75rem;
    }

    .products-header {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}

/* Animation pour les ajouts au panier */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.cart-item {
    animation: slideIn 0.3s ease-out;
}

/* Styles pour les badges de statut */
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
}

/* Effet de surbrillance pour la catégorie active */
.category-active {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-primary-dark));
    color: white;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.category-inactive {
    transition: all 0.2s ease;
}

.category-inactive:hover {
    background-color: rgba(13, 110, 253, 0.1);
    border-color: var(--bs-primary);
    transform: translateY(-1px);
}

/* Amélioration de l'affichage des produits */
.product-grid-transition {
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Indicateur visuel pour les produits en stock faible */
.low-stock-indicator {
    position: relative;
    overflow: hidden;
}

.low-stock-indicator::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffc107, transparent);
    animation: lowStockPulse 2s infinite;
}

@keyframes lowStockPulse {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Style pour la recherche active */
.search-active {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}

/* Amélioration des états de hover pour les cartes produits */
.product-card {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.product-card:hover::before {
    left: 100%;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* Style pour les messages d'état */
.alert-custom {
    border: none;
    border-radius: 0.5rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Amélioration de l'affichage du panier */
.cart-summary {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.05), rgba(25, 135, 84, 0.02));
    border-top: 2px solid var(--bs-success);
}

/* Responsive amélioré pour les très petits écrans */
@media (max-width: 576px) {
    .category-filter-section .btn {
        font-size: 0.8rem;
        padding: 0.375rem 0.5rem;
    }

    .products-header h6 {
        font-size: 1rem;
    }

    .product-card .card-body {
        padding: 0.75rem;
    }

    .cart-item .form-control-sm {
        font-size: 0.8rem;
    }
}
</style>

@endpush
