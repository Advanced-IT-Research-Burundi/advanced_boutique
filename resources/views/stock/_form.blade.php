@if(isset($stock))
    @method('PUT')
    <input type="hidden" name="stock_id" value="{{ $stock->id }}">
@endif

@csrf

<div class="row">
    <div class="mb-3 col-md-6">
        <label for="name" class="form-label">Nom du stock <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
               name="name" value="{{ old('name', $stock->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label for="location" class="form-label">Emplacement</label>
        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location"
               name="location" value="{{ old('location', $stock->location ?? '') }}">
        @error('location')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
              name="description" rows="3">{{ old('description', $stock->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="mb-3 col-md-6">
        <label for="agency_id" class="form-label">Agence</label>
        <select class="form-select @error('agency_id') is-invalid @enderror" id="agency_id" name="agency_id">
            <option value="">Sélectionner une agence</option>
            @foreach($agencies as $agency)
                <option value="{{ $agency->id }}"
                    {{ old('agency_id', $stock->agency_id ?? '') == $agency->id ? 'selected' : '' }}>
                    {{ $agency->name }}
                </option>
            @endforeach
        </select>
        @error('agency_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


</div>

<div class="mt-4 d-flex justify-content-between">
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i>
        {{ isset($stock) ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
</div>
