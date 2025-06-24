<div>
    {{-- The best athlete wants his opponent at his best. --}}

    <h4>Transfert de stock</h4>
    <div>
        <div>
            <label for="stock_id">Stock</label>
            <select wire:model="stock_id" id="stock_id" class="form-control">
                <option value="">Sélectionner un stock</option>
                @foreach ($stocks as $stock)
                    <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="stock_id">Stock</label>
            <select wire:model="stock_id" id="stock_id" class="form-control">
                <option value="">Sélectionner un stock</option>
                @foreach ($stocks as $stock)
                    <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
