<div>
    {{-- Do your work, then step back. --}}
    <h1>Stock</h1>
    <div class="mt-2">
        <input type="text" wire:model="search" placeholder="Rechercher un produit">
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
</div>
