<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Mouvement de Stock</h4>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="saveMovement">
            <!-- Movement Type and Date -->
            <div class="row">
                <div class="col-md-3">
                    <label for="item_movement_type" class="form-label">Type de Mouvement</label>
                    <select
                        id="item_movement_type"
                        wire:model="item_movement_type"
                        class="form-select"
                        required
                    >
                        <option value="">Sélectionner le type de mouvement</option>
                        @foreach (MOUVEMENT_STOCK as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('item_movement_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="item_movement_date" class="form-label">Date du Mouvement</label>
                    <input
                        type="datetime-local"
                        id="item_movement_date"
                        wire:model="item_movement_date"
                        class="form-control"
                        required
                    >
                    @error('item_movement_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="item_purchase_or_sale_price" class="form-label">Prix</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ $item_purchase_or_sale_currency ?? 'BIF' }}</span>
                        <input
                            type="number"
                            id="item_purchase_or_sale_price"
                            wire:model="item_purchase_or_sale_price"
                            step="0.01"
                            min="0"
                            class="form-control"
                            placeholder="0.00"
                        >
                    </div>
                    @error('item_purchase_or_sale_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="item_purchase_or_sale_currency" class="form-label">Devise</label>
                    <select
                        id="item_purchase_or_sale_currency"
                        wire:model="item_purchase_or_sale_currency"
                        class="form-select"
                    >
                        <option value="BIF">BIF - Franc Burundais</option>
                        <option value="USD">USD - Dollar Américain</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="RWF">RWF - Franc Rwandais</option>
                        <option value="CDF">CDF - Franc Congolais</option>
                    </select>
                    @error('item_purchase_or_sale_currency') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Quantity -->
            <div class="mb-3">
                <label for="item_quantity" class="form-label">Quantité</label>
                <div class="input-group">
                    <input
                        type="number"
                        id="item_quantity"
                        wire:model="item_quantity"
                        step="0.01"
                        min="0.01"
                        class="form-control"
                        required
                    >
                    <span class="input-group-text">{{ $stock->measurement_unit ?? 'pcs' }}</span>
                </div>
                @error('item_quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label for="item_movement_note" class="form-label">Notes</label>
                <textarea
                    id="item_movement_note"
                    wire:model="item_movement_note"
                    rows="3"
                    class="form-control"
                    placeholder="Informations supplémentaires sur ce mouvement..."
                ></textarea>
                @error('item_movement_note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button
                    type="submit"
                    class="btn btn-primary btn-lg"
                >
                    <i class="fas fa-save me-2"></i>Enregistrer le Mouvement
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Pour gérer les messages de succès/erreur
    document.addEventListener('livewire:initialized', () => {
        @this.on('movement-saved', (event) => {
            toastr.success(event.message);
        });

        @this.on('movement-error', (event) => {
            toastr.error(event.message);
        });
    });
</script>
@endpush
