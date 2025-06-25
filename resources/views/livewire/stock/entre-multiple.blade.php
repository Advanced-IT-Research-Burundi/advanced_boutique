<div class="mt-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h2>{{ $stock->name }}</h2>
            <h2 class="mb-0 h5">Entrée Multiple de Produits</h2>
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

            <div class="mb-4">
                <button
                    wire:click="entreMultiple"
                    class="btn btn-primary"
                    wire:loading.attr="disabled"
                    wire:loading.class="disabled"
                >
                    <span wire:loading.class="visually-hidden">Valider les entrées</span>
                    <span class="spinner-border spinner-border-sm me-1 visually-hidden" wire:loading wire:target="entreMultiple"></span>
                    <span wire:loading wire:target="entreMultiple">Traitement en cours...</span>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Code du Produit</th>
                            <th>Categorie</th>
                            <th>Produit</th>
                            <th>Quantité Actuelle</th>
                            <th>Quantité Entrée</th>
                            <th>Unité</th>
                            <th>Prix Unitaire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockProducts as $product)
                            <tr>
                                <td>{{ $product->product_id }}</td>
                                <td>{{ $product->product->category->name ?? 'N/A' }}</td>
                                <td>{{ $product->product->name }}</td>
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
                                <input type="number" wire:model="prices.{{ $product->id }}" class="form-control form-control-sm" min="0" step="1" placeholder="0" value="{{ $product->product->sale_price }}">
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
                            <tr>
                                <td colspan="8" class="py-4 text-center">
                                    <div class="text-muted">Aucun produit trouvé dans ce stock.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


