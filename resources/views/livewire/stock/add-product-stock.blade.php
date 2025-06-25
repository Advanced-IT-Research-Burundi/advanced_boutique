<div>
    {{-- Do your work, then step back. --}}

    <div class="mb-3 row align-items-center">
        <div class="col">
            <h6 class="mb-0">Stock {{ $stock->name }}</h6>
        </div>
        <div class="col text-end">
            <a href="{{ route('entre_multiple', $stock->id)}}" class="btn btn-primary btn-sm">
                Entre Multiple en Stock
            </a>
        </div>
    </div>
    <div class="mt-2">
        <input type="text" wire:model="search" wire:keyup="searchProduct" placeholder="Rechercher un produit">
        <ul>
            @foreach ($products as $product)
            <li class="gap-2 mt-2 d-flex justify-content-between align-items-center">
                <strong>{{ $product->code }}</strong>
                <span>{{ $product->name }}</span>
                <button wire:click="addProduct({{ $product->id }})" class="btn btn-primary btn-sm"> <i class="bi bi-cart-plus"></i> Ajouter au stock</button>
            </li>
            <hr>
            @endforeach
        </ul>
    </div>

    <div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Les produits du stock {{ $stock->name }}</h5>
                <div class="mb-3 input-group">
    <input
        type="text"
        wire:model.live="stockProductSearch"
        wire:keyup.debounce.300ms="searchStockProducts"
        placeholder="Rechercher un produit"
        class="form-control"
    >
    <button class="btn btn-primary" type="button" wire:click="searchStockProducts">
        <i class="bi bi-search"></i> Rechercher
    </button>
</div>
                <table id="stockProductsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>CODE</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Date </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockProducts as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->product->code }}</td>
                            <td>{{ $product->product->name }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($product->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('stocks.mouvement', $product->id) }}" class="btn btn-info btn-sm"> <i class="bi bi-eye"></i> Mouvement</a>

                                <button wire:click="removeProduct({{ $product->id }})" class="btn btn-danger btn-sm"> <i class="bi bi-cart-x"></i> Supprimer</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $stockProducts->links() }}
            </div>
        </div>
    </div>
</div>
