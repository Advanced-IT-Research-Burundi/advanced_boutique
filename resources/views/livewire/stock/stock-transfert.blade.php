<div>
    {{-- The best athlete wants his opponent at his best. --}}

    <h4>Transfert de stock</h4>
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
   <button wire:click="updateProductListe({{ $category->id }})" class="btn btn-primary">{{ $category->name }}</button>
    @endforeach
    </div>


    <div>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>
                        <button wire:click="removeProduct({{ $product->id }})" class="btn btn-danger btn-sm">Supprimer</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
