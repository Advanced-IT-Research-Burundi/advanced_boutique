<div>
    {{-- Do your work, then step back. --}}
    <h1>Stock</h1>
    <div class="mt-2">
        <input type="text" wire:model="search" wire:keyup="searchProduct" placeholder="Rechercher un produit">
        <ul>
            @foreach ($products as $product)
                <li class=" d-flex justify-content-between align-items-center gap-2 mt-2 ">
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
            </div>
        </div>

        @push('scripts')
        <script>
            $(document).ready(function() {
                $('#stockProductsTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "order": [[0, 'desc']],
                    "language": {
                        "search": "Rechercher:",
                        "lengthMenu": "Afficher _MENU_ entrées par page",
                        "zeroRecords": "Aucun enregistrement trouvé",
                        "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        "infoEmpty": "Aucune entrée disponible",
                        "infoFiltered": "(filtré à partir de _MAX_ entrées totales)",
                        "paginate": {
                            "first": "Premier",
                            "last": "Dernier",
                            "next": "Suivant",
                            "previous": "Précédent"
                        }
                    }
                });
            });
        </script>
        @endpush
    </div>
</div>
