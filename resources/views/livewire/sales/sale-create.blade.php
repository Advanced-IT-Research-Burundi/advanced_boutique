<div class="container-fluid px-4 py-3">
    {{-- {{$products}} --}}
    @foreach ($products as $product)
        {{ $product->name }} - {{ $product->available_stock }} Fbu
    @endforeach
    <form wire:submit.prevent="save">
        <div class="row g-4">
            <!-- Colonne principale -->
            <div class="col-xl-8">
                <!-- Section Client -->
                <div class="card border-0 shadow-sm mb-4 hover-lift">
                    <div class="card-header bg-gradient-primary text-white border-0 rounded-top">
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
                    <div class="card-body p-4">
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
                                        <div  class="dropdown-menu show w-100 shadow-lg border-0 mt-1" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                            @foreach($filtered_clients as $client)
                                                <a href="#" class="dropdown-item d-flex align-items-center py-2 px-3"
                                                   wire:click="selectClient({{ $client->id }})">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
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
                            <div class="alert alert-info border-0 mt-3 bg-info bg-opacity-10 rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-info bg-opacity-20 rounded-circle me-3 p-2">
                                        <i class="bi bi-person text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $selected_client->name }}</h6>
                                        <div class="d-flex flex-wrap gap-3">
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
                <div class="card border-0 shadow-sm hover-lift">
                    <div class="card-header bg-gradient-primary text-white border-0 rounded-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bi bi-box-seam me-2"></i>
                                Produits de la vente
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-light btn-sm"
                                        wire:click="$set('show_product_search', true)">
                                    <i class="bi bi-search me-1"></i>
                                    Rechercher
                                </button>
                                <button type="button" class="btn btn-light btn-sm pulse-animation"
                                        wire:click="addEmptyItem">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Ajouter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Recherche de produits -->
                        @if($show_product_search)
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <div class="position-relative">
                                                <input type="text"
                                                       class="form-control"
                                                       wire:model.live.debounce.300ms="product_search"
                                                       placeholder="Rechercher un produit par nom..."
                                                       autocomplete="off">
                                                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-secondary w-100"
                                                    wire:click="$set('show_product_search', false)">
                                                <i class="bi bi-x me-1"></i>
                                                Fermer
                                            </button>
                                        </div>
                                    </div>

                                    @if($product_search && count($filtered_products) > 0)
                                        <div class="row g-2 mt-2">
                                            @foreach($filtered_products as $product)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card h-100 border product-card"
                                                         style="cursor: pointer;"
                                                         wire:click="addProductToSale({{ $product->id }})">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-start">
                                                                @if($product->image)
                                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                                         alt="{{ $product->name }}"
                                                                         class="rounded me-3"
                                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                                                                         style="width: 50px; height: 50px;">
                                                                        <i class="bi bi-box text-primary"></i>
                                                                    </div>
                                                                @endif
                                                                <div class="flex-grow-1">
                                                                    <h6 class="card-title mb-1 fw-semibold">{{ $product->name }}</h6>
                                                                    <p class="text-muted small mb-1">{{ number_format($product->sale_price, 0, ',', ' ') }} F</p>
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <span class="badge {{ $product->available_stock <= $product->alert_quantity ? 'bg-warning' : 'bg-success' }}">
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
                                </div>
                            </div>
                        @endif

                        @if(empty($items) || (count($items) == 1 && empty($items[0]['product_id'])))
                            <div class="text-center py-5 text-muted">
                                <div class="mb-4">
                                    <i class="bi bi-basket display-1 opacity-25"></i>
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
                                <table class="table table-hover align-middle">
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
                                        @foreach($items as $index => $item)
                                            <tr class="product-row" wire:key="item-{{ $index }}">
                                                <td>
                                                    @if($item['product_id'])
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
                                                                <div class="bg-primary bg-opacity-10 rounded me-2 d-flex align-items-center justify-content-center"
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
                                                    @else
                                                        <select class="form-select form-select-sm @error('items.'.$index.'.product_id') is-invalid @enderror"
                                                                wire:model.live="items.{{ $index }}.product_id">
                                                            <option value="">Sélectionner...</option>
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}">
                                                                    {{ $product->name }} - {{ number_format($product->sale_price, 0, ',', ' ') }} F
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('items.'.$index.'.product_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number"
                                                               class="form-control @error('items.'.$index.'.quantity') is-invalid @enderror"
                                                               wire:model.live="items.{{ $index }}.quantity"
                                                               min="0.01"
                                                               step="0.01"
                                                               placeholder="0">
                                                        @if($item['unit'])
                                                            <span class="input-group-text">{{ $item['unit'] }}</span>
                                                        @endif
                                                    </div>
                                                    @error('items.'.$index.'.quantity')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number"
                                                               class="form-control @error('items.'.$index.'.sale_price') is-invalid @enderror"
                                                               wire:model.live="items.{{ $index }}.sale_price"
                                                               min="0"
                                                               step="0.01"
                                                               placeholder="0">
                                                        <span class="input-group-text">F</span>
                                                    </div>
                                                    @error('items.'.$index.'.sale_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           class="form-control form-control-sm"
                                                           wire:model.live="items.{{ $index }}.discount"
                                                           min="0"
                                                           max="100"
                                                           step="0.01"
                                                           placeholder="0">
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary fs-6">
                                                        {{ number_format($item['subtotal'] ?? 0, 0, ',', ' ') }} F
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($item['product_id'])
                                                        @php
                                                            $product = $products->find($item['product_id']);
                                                            $stock = $item['available_stock'];
                                                            $is_low = $product && $stock <= $product->alert_quantity;
                                                        @endphp
                                                        <div class="text-center">
                                                            <span class="badge {{ $is_low ? 'bg-warning' : 'bg-success' }} fs-6">
                                                                {{ $stock }}
                                                            </span>
                                                            @if($is_low)
                                                                <div class="mt-1">
                                                                    <small class="text-warning">
                                                                        <i class="bi bi-exclamation-triangle"></i>
                                                                        Faible
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm"
                                                            wire:click="removeItem({{ $index }})"
                                                            title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @error('items')
                            <div class="alert alert-danger mt-3 border-0 rounded-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
                            </div>
                        @enderror

                        @if($errors->has('stock_error'))
                            <div class="alert alert-warning mt-3 border-0 rounded-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                                    <div>
                                        <strong>Erreurs de stock :</strong>
                                        <ul class="mb-0 mt-2">
                                           @foreach($errors->get('stock_error') as $error)
                                            <li>{{ is_array($error) ? implode(', ', $error) : $error }}</li>
                                        @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne résumé -->
            <div class="col-xl-4">
                <div class="sticky-top" style="top: 1rem;">
                    <!-- Résumé de commande -->
                    <div class="card border-0 shadow-lg mb-4 overflow-hidden">
                        <div class="card-header bg-gradient-primary text-white border-0 p-4">
                            <h5 class="mb-0">
                                <i class="bi bi-calculator me-2"></i>
                                Résumé de la vente
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 py-2 border-bottom">
                                <span class="text-muted">Sous-total :</span>
                                <span class="fw-semibold fs-5">{{ number_format($subtotal, 0, ',', ' ') }} Fbu</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 py-2 border-bottom">
                                <span class="text-muted">Remise totale :</span>
                                <span class="text-success fw-semibold fs-5">- {{ number_format($total_discount, 0, ',', ' ') }} Fbu</span>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark fs-5">Total :</strong>
                                    <strong class="text-primary fs-3">{{ number_format($total_amount, 0, ',', ' ') }} Fbu</strong>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="paid_amount" class="form-label fw-semibold">
                                    Montant payé <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="number"
                                           class="form-control @error('paid_amount') is-invalid @enderror"
                                           wire:model.live="paid_amount"
                                           id="paid_amount"
                                           min="0"
                                           step="0.01"
                                           placeholder="0">
                                    <span class="input-group-text">Fbu</span>
                                </div>
                                @error('paid_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 py-2">
                                <span class="text-muted fw-semibold">Reste à payer :</span>
                                <span class="fw-bold fs-5 {{ $due_amount > 0 ? 'text-warning' : 'text-success' }}">
                                    {{ number_format($due_amount, 0, ',', ' ') }} Fbu
                                </span>
                            </div>

                            @if($this->payment_status)
                                <div class="alert alert-{{ $this->payment_status['type'] }} border-0 py-3 rounded-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-{{ $this->payment_status['type'] == 'success' ? 'check-circle' : ($this->payment_status['type'] == 'warning' ? 'exclamation-triangle' : 'x-circle') }} me-2 fs-5"></i>
                                        <span class="fw-semibold">{{ $this->payment_status['message'] }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label for="note" class="form-label fw-semibold">Note (optionnel)</label>
                                <textarea class="form-control"
                                          wire:model="note"
                                          id="note"
                                          rows="3"
                                          placeholder="Ajouter une note à cette vente..."></textarea>
                            </div>

                            <div class="d-grid gap-3">
                                <button type="submit"
                                        class="btn btn-primary btn-lg py-3"
                                        wire:loading.attr="disabled"
                                        wire:target="save">
                                    <span wire:loading.remove wire:target="save">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Enregistrer la vente
                                    </span>
                                    <span wire:loading wire:target="save">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Enregistrement...
                                    </span>
                                </button>

                                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-lg py-3">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
    <style>
    /* .hover-lift {
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    } */

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }

    .product-card {
        transition: all 0.2s ease-in-out;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        border-color: #0d6efd !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    }

    .avatar-sm {
        width: 2rem;
        height: 2rem;
    }

    .avatar {
        width: 3rem;
        height: 3rem;
    }

    .dropdown-menu.show {
        animation: slideDown 0.2s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush
