<div class="mt-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h2>{{ $stock->name }}</h2>
            <h2 class="mb-0 h5">Entrée Multiple de Produits</h2>
            <a href="{{ route('stocks.list', $stock->id) }}" class="btn btn-primary btn-sm">Retour</a>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="gap-3 d-flex w-75">
                    <div class="w-50">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Rechercher un produit..."
                                wire:model.live="search"
                            >
                        </div>
                    </div>
                    <div class="w-50">
                        <select class="form-select" wire:model.live="selectedCategory">
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <button
                        wire:click="entreMultiple"
                        class="btn btn-primary btn-sm"
                        wire:loading.attr="disabled"
                        wire:loading.class="disabled"
                    >
                        <span wire:loading.class="visually-hidden">Valider les entrées</span>
                        <span class="spinner-border spinner-border-sm me-1 visually-hidden" wire:loading wire:target="entreMultiple"></span>
                        <span wire:loading wire:target="entreMultiple">Traitement en cours...</span>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Quantité Actuelle</th>
                            <th>Quantité Entrée</th>
                            <th>Unité</th>
                            <th>Prix Unitaire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr wire:key="{{ $product->id }}">
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->product->code }}</td>
                                <td>{{ $product->product->name }}</td>
                                <td>{{ $product->product->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $product->quantity }}</span>
                                </td>
                                <td style="width: 150px;">
                                    <input
                                        type="number"
                                        wire:model="quantities.{{ $product->id }}"
                                        class="form-control form-control-sm"
                                        min="0"
                                        step="1"
                                        placeholder="0"
                                    >
                                </td>
                                <td>{{ $product->product->unit }}</td>
                                <td>
                                    <input
                                        type="number"
                                        wire:model="prices.{{ $product->id }}"
                                        class="form-control form-control-sm"
                                        min="0"
                                        step="1"
                                        placeholder="0"
                                        value="{{ $product->product->sale_price }}">
                                </td>
                                <td>
                                    <button
                                        wire:click="clearQuantity({{ $product->id }})"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Réinitialiser la quantité"
                                    >
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr wire:key="empty">
                                <td colspan="8" class="py-4 text-center">
                                    <div class="text-muted">Aucun produit trouvé.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
