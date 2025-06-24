<div>
    {{-- Do your work, then step back. --}}

    <div class="row">
    <div class="col"><h6>Stock {{ $stock->name }}</h6></div>
    <div class="col">
        <a href="{{ route('entre_multiple', $stock->id)}}">Entre Multiple en Stock </a>
    </div>
    </div>
    <div class="mt-2">
        <input type="text" wire:model="search" wire:keyup="searchProduct" placeholder="Rechercher un produit">
        <ul>
            @foreach ($products as $product)
            <li class="gap-2 mt-2 d-flex justify-content-between align-items-center">
                <strong>{{ $product->name }}</strong>
                <span>{{ $product->description }}</span>
                <button wire:click="addProduct({{ $product->id }})" class="btn btn-primary btn-sm"> <i class="bi bi-cart-plus"></i> Ajouter au stock</button>
            </li>
            <hr>
            @endforeach
        </ul>
    </div>

    <div>

        <div class="card">
            <div class="card-body">
                <table id="stockProductsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product ID</th>
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
                            <td>{{ $product->product_id }}</td>
                            <td>{{ $product->product->name }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($product->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td>

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
