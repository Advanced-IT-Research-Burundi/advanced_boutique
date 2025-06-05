<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="user_id" class="form-label">
                <i class="bi bi-person-circle text-primary me-1"></i>
                Utilisateur responsable <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('user_id') is-invalid @enderror"
                    id="user_id"
                    name="user_id"
                    required>
                <option value="">Sélectionner un utilisateur</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                            {{ old('user_id', $cashRegister->user_id ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="stock_id" class="form-label">
                <i class="bi bi-box text-info me-1"></i>
                Stock associé <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('stock_id') is-invalid @enderror"
                    id="stock_id"
                    name="stock_id"
                    required>
                <option value="">Sélectionner un stock</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}"
                            {{ old('stock_id', $cashRegister->stock_id ?? '') == $stock->id ? 'selected' : '' }}>
                        {{ $stock->name }}
                    </option>
                @endforeach
            </select>
            @error('stock_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="opening_balance" class="form-label">
                <i class="bi bi-cash-coin text-success me-1"></i>
                Solde d'ouverture <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">€</span>
                <input type="number"
                       class="form-control @error('opening_balance') is-invalid @enderror"
                       id="opening_balance"
                       name="opening_balance"
                       value="{{ old('opening_balance', $cashRegister->opening_balance ?? '') }}"
                       step="0.01"
                       min="0"
                       required>
            </div>
            @error('opening_balance')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if(isset($cashRegister) && $cashRegister->exists)
        <div class="col-md-6">
            <div class="mb-3">
                <label for="closing_balance" class="form-label">
                    <i class="bi bi-cash-stack text-warning me-1"></i>
                    Solde de fermeture
                </label>
                <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input type="number"
                           class="form-control @error('closing_balance') is-invalid @enderror"
                           id="closing_balance"
                           name="closing_balance"
                           value="{{ old('closing_balance', $cashRegister->closing_balance ?? '') }}"
                           step="0.01"
                           min="0">
                </div>
                @error('closing_balance')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="status" class="form-label">
                <i class="bi bi-toggle-on text-primary me-1"></i>
                Statut <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('status') is-invalid @enderror"
                    id="status"
                    name="status"
                    required>
                <option value="">Sélectionner un statut</option>
                <option value="open"
                        {{ old('status', $cashRegister->status ?? '') == 'open' ? 'selected' : '' }}>
                    <i class="bi bi-unlock"></i> Ouverte
                </option>
                <option value="closed"
                        {{ old('status', $cashRegister->status ?? '') == 'closed' ? 'selected' : '' }}>
                    <i class="bi bi-lock"></i> Fermée
                </option>
                <option value="suspended"
                        {{ old('status', $cashRegister->status ?? '') == 'suspended' ? 'selected' : '' }}>
                    <i class="bi bi-pause"></i> Suspendue
                </option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="agency_id" class="form-label">
                <i class="bi bi-building text-info me-1"></i>
                Agence
            </label>
            <select class="form-select @error('agency_id') is-invalid @enderror"
                    id="agency_id"
                    name="agency_id">
                <option value="">Aucune agence</option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}"
                            {{ old('agency_id', $cashRegister->agency_id ?? '') == $agency->id ? 'selected' : '' }}>
                        {{ $agency->name }}
                    </option>
                @endforeach
            </select>
            @error('agency_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="opened_at" class="form-label">
                <i class="bi bi-calendar-check text-success me-1"></i>
                Date d'ouverture <span class="text-danger">*</span>
            </label>
            <input type="datetime-local"
                   class="form-control @error('opened_at') is-invalid @enderror"
                   id="opened_at"
                   name="opened_at"
                   value="{{ old('opened_at', isset($cashRegister) && $cashRegister->opened_at ? $cashRegister->opened_at->format('Y-m-d\TH:i') : '') }}"
                   required>
            @error('opened_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if(isset($cashRegister) && $cashRegister->exists)
        <div class="col-md-6">
            <div class="mb-3">
                <label for="closed_at" class="form-label">
                    <i class="bi bi-calendar-x text-danger me-1"></i>
                    Date de fermeture
                </label>
                <input type="datetime-local"
                       class="form-control @error('closed_at') is-invalid @enderror"
                       id="closed_at"
                       name="closed_at"
                       value="{{ old('closed_at', $cashRegister->closed_at ? $cashRegister->closed_at->format('Y-m-d\TH:i') : '') }}">
                @error('closed_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const closedAtInput = document.getElementById('closed_at');
    const closingBalanceInput = document.getElementById('closing_balance');

    if (statusSelect && closedAtInput) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'closed' && !closedAtInput.value) {
                closedAtInput.value = new Date().toISOString().slice(0, 16);
            } else if (this.value !== 'closed') {
                closedAtInput.value = '';
            }
        });
    }
});
</script>
