<div class="px-4 py-3 container-fluid">
    <form wire:submit.prevent="save">
        <div class="row g-4">
            <!-- Colonne principale -->
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

                <!-- Section Produits -->
                <div class="border-0 shadow-sm card hover-lift">
                    <div class="text-white border-0 card-header bg-gradient-primary rounded-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-box-seam me-2"></i>
                                Produits de la vente
                                @if(count($items) > 0)
                                    <span class="badge bg-light text-primary ms-2">{{ count($items) }}</span>
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
                                        Vider
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-sm">
                                        <i class="bi bi-arrow-clockwise me-1"></i>
                                        Actualiser
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-4 card-body">
                        <!-- Recherche de produits -->

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
                                    <div class="mt-4" style="max-height: 400px; overflow-y: auto;">
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

                                                    @if(!$product_search && !$selected_category_id && isset($category_data['id']))
                                                        <div class="mt-3 text-center">
                                                            <button type="button"
                                                                    class="btn btn-outline-primary btn-sm"
                                                                    wire:click="selectCategory({{ $category_data['id'] }})">
                                                                <i class="bi bi-eye me-1"></i>
                                                                Voir tous les produits de {{ $category_data['name'] }}
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Affichage des produits filtrés (quand une seule catégorie est sélectionnée) -->
                                @if(count($filtered_products) > 0)
                                    <div class="mt-2 row g-2" style="max-height: 400px; overflow-y: auto;">
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
                                        @if(count($selected_products) > 0)
                                            <br><small class="text-muted">Les produits déjà ajoutés au panier ne sont pas affichés.</small>
                                        @endif
                                    </div>
                                @endif

                                {{-- @if(!$product_search && !$selected_category_id && count($products_by_category) == 0)
                                    <div class="mt-3 border-0 alert alert-info rounded-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        {{ count($selected_products) > 0
                                            ? ' Les produits déjà ajoutés au panier ne sont pas affichés.'
                                            : (!$product_search == 0
                                                ? 'chercher et ajouter des produits à votre vente'
                                                : '') }}
                                    </div>
                                @endif --}}
                            </div>
                        </div>
                    @endif

                        @if(empty($items))
                            <div class="py-5 text-center text-muted">
                                <div class="mb-4">
                                    <i class="opacity-25 bi bi-basket display-1"></i>
                                </div>
                                <h5 class="mb-2">Aucun produit ajouté</h5>
                                <p class="mb-3">Commencez par rechercher et ajouter des produits à votre vente</p>
                                <button type="button" class="btn btn-primary"
                                        wire:click="$set('show_product_search', true)">
                                    <i class="bi bi-search me-2"></i>
                                    Rechercher des produits
                                </button>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="35%">Produit</th>
                                            <th width="12%">Quantité</th>
                                            <th width="15%">Prix unitaire</th>
                                            <th width="12%">Remise (%)</th>
                                            <th width="15%">Sous-total</th>
                                            <th width="8%">Stock</th>
                                            <th width="8%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $item)
                                            <tr class="product-row" wire:key="product-{{ $item['product_id'] }}">
                                                <td>
                                                    @php
                                                        $product = $products->find($item['product_id']);
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        @if($product && $product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                                alt="{{ $product->name }}"
                                                                class="rounded me-2"
                                                                style="width: 40px; height: 40px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-opacity-10 rounded bg-primary me-2 d-flex align-items-center justify-content-center"
                                                                style="width: 40px; height: 40px;">
                                                                <i class="bi bi-box text-primary"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold">{{ $product->name ?? 'Produit inconnu' }}</div>
                                                            @if($product && $product->unit)
                                                                <small class="text-muted">Unité: {{ $product->unit }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        wire:change="updateItemQuantity({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['quantity'] }}"
                                                        min="0.01"
                                                        step="0.01"
                                                        style="width: 80px;"
                                                        data-product-id="{{ $item['product_id'] }}">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        wire:change="updateItemPrice({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['sale_price'] }}"
                                                        min="0"
                                                        step="0.01"
                                                        data-product-id="{{ $item['product_id'] }}">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        wire:change="updateItemDiscount({{ $item['product_id'] }}, $event.target.value)"
                                                        value="{{ $item['discount'] ?? 0 }}"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        style="width: 70px;"
                                                        data-product-id="{{ $item['product_id'] }}">
                                                </td>
                                                <td class="fw-semibold">
                                                    @php
                                                        $quantity = floatval($item['quantity']);
                                                        $price = floatval($item['sale_price']);
                                                        $discount = floatval($item['discount'] ?? 0);
                                                        $subtotal = $quantity * $price;
                                                        $discountAmount = ($subtotal * $discount) / 100;
                                                        $finalAmount = $subtotal - $discountAmount;
                                                    @endphp
                                                    {{ number_format($finalAmount, 0, ',', ' ') }} Fbu
                                                    @if($discount > 0)
                                                        <br><small class="text-muted text-decoration-line-through">{{ number_format($subtotal, 0, ',', ' ') }} Fbu</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $item['available_stock'] <= ($product->alert_quantity ?? 5) ? 'bg-warning' : 'bg-success' }}">
                                                        {{ $item['available_stock'] }}
                                                    </span>
                                                    @if($item['quantity'] > $item['available_stock'])
                                                        <br><small class="text-danger">
                                                            <i class="bi bi-exclamation-triangle"></i>
                                                            Insuffisant
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm"
                                                            wire:click="removeItem({{ $item['product_id'] }})"
                                                            title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-4 text-center text-muted">
                                                    <i class="mb-2 bi bi-cart-x fs-1 d-block"></i>
                                                    Aucun produit dans le panier
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totaux -->
                            <div class="mt-3 border-0 card bg-light">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between">
                                                <span>Sous-total:</span>
                                                <span class="fw-semibold">{{ number_format($total_subtotal, 0, ',', ' ') }} Fbu</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between">
                                                <span>Remise totale:</span>
                                                <span class="fw-semibold text-warning">-{{ number_format($total_discount, 0, ',', ' ') }} Fbu</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between fs-5">
                                                <span class="fw-bold">Total:</span>
                                                <span class="fw-bold text-primary">{{ number_format($total_amount, 0, ',', ' ') }} Fbu</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
        </div>
    </div>
</div>

<!-- Colonne latérale -->
<div class="col-xl-4">
    <!-- Résumé de la vente -->
    <div class="mb-4 border-0 shadow-sm card hover-lift">
        <div class="text-white border-0 card-header bg-gradient-success rounded-top">
            <h5 class="mb-0">
                <i class="bi bi-calculator me-2"></i>
                Résumé de la vente
            </h5>
        </div>
        <div class="p-4 card-body">
            @if(count($items) > 0)
                <div class="mb-3 summary-item d-flex justify-content-between">
                    <span class="text-muted">Articles:</span>
                    <span class="fw-semibold">{{ count($items) }}</span>
                </div>
                <div class="mb-3 summary-item d-flex justify-content-between">
                    <span class="text-muted">Sous-total:</span>
                    <span class="fw-semibold">{{ number_format($total_subtotal, 0, ',', ' ') }} Fbu</span>
                </div>
                @if($total_discount > 0)
                    <div class="mb-3 summary-item d-flex justify-content-between">
                        <span class="text-muted">Remise:</span>
                        <span class="fw-semibold text-warning">-{{ number_format($total_discount, 0, ',', ' ') }} F</span>
                    </div>
                @endif
                <hr>
                <div class="mb-4 summary-item d-flex justify-content-between">
                    <span class="fw-bold fs-5">Total:</span>
                    <span class="fw-bold fs-5 text-success">{{ number_format($total_amount, 0, ',', ' ') }} F</span>
                </div>
            @else
                <div class="py-4 text-center text-muted">
                    <i class="opacity-25 bi bi-calculator display-4"></i>
                    <p class="mt-3 mb-0">Le résumé apparaîtra<br>après ajout des produits</p>
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
                    wire:model.live="paid_amount"

                >
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
                                    wire:key="quick-amount-{{ $index }}-{{ $amount }}"
                                >
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

<!-- Styles CSS additionnels -->
@push('styles')

<style>
.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

/* .hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
}

.product-card:hover {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.02);
} */

.product-row:hover {
    background-color: rgba(var(--light-blue), 0.02);
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

@media (max-width: 768px) {
    .table-responsive table th,
    .table-responsive table td {
        white-space: nowrap;
        font-size: 0.875rem;
    }

    .hover-lift:hover {
        transform: none;
    }
}
</style>

@endpush
