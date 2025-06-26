<div>
    {{-- The best athlete wants his opponent at his best. --}}

    <h4>Transfert de stock </h4>


    <div class="row">
        <div class="col-md-6">
            <label for="stockSource">Stock source</label>
            <select wire:model="stockSource" id="stockSource" wire:change="updateStockSource" class="form-control">
                <option value="">Sélectionner un stock</option>
                @foreach ($stocks as $stock)
                    <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="destination_stock_id">Stock destination</label>
            <select wire:model="destination_stock_id" id="destination_stock_id" class="form-control">
                <option value="">Sélectionner un stock</option>
                @foreach ($stocks as $stock)
                    <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mt-2">
    @foreach ($categories as $category)
   <button style="background-color: {{ $category->id == $selectedCategory ? 'darkgreen' : '' }}" wire:click="updateProductListe({{ $category->id }})" class="btn btn-primary">{{ $category->name }}</button>
    @endforeach
    </div>


    <div class="row">
        <div class="col-md-6">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>CODE</th>
                    <th>CATÉGORIE</th>
                    <th>Quantité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->stockProducts->where('stock_id', $stockSource)->first()->quantity }}</td>
                    <td>
                        <button wire:click="addProduct({{ $product->id }})" class="btn btn-primary btn-sm">Ajouter</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="text-white card-header bg-primary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-check me-2"></i>
                        Produits sélectionnés
                        <span class="bg-white badge text-primary ms-2">{{ count($selectedProducts) }}</span>
                    </h5>
                    @if(count($selectedProducts) > 0)
                        <button wire:click="$set('selectedProducts', [])"
                                class="btn btn-sm btn-light"
                                title="Vider la sélection">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>

                @if(count($selectedProducts) > 0)
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table mb-0 table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedProductsItems as $product)
                                    @php
                                        $stockProduct = $product->stockProducts->where('stock_id', $stockSource)->first();
                                        $maxQty = $stockProduct ? $stockProduct->quantity : 0;
                                        $quantity = $quantities[$product->id] ?? 1;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            <small class="text-muted">{{ $product->code }}</small>
                                        </td>
                                        <td class="text-center" style="width: 120px;">
                                            <input type="number"
                                                   wire:model.lazy="quantities.{{ $product->id }}"
                                                   min="1"
                                                   max="{{ $maxQty }}"
                                                   class="text-center form-control form-control-sm"
                                                   style="width: 70px; display: inline-block;">
                                            <small class="text-muted d-block">Max: {{ $maxQty }}</small>
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="removeFromTransfer({{ $product->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Retirer">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Total:</strong> {{ count($selectedProducts) }} produit(s)
                            </div>
                            <button wire:click="transfer"
                                    class="btn btn-primary"
                                    {{ !$destination_stock_id ? 'disabled' : '' }}>
                                <i class="bi bi-arrow-left-right me-2"></i> Transférer
                            </button>
                        </div>
                    </div>
                @else
                    <div class="py-5 text-center card-body">
                        <div class="mb-3 text-muted">
                            <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                        </div>
                        <p class="mb-0 text-muted">Aucun produit sélectionné</p>
                        <small class="text-muted">Cliquez sur le bouton "+" pour ajouter des produits</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush
