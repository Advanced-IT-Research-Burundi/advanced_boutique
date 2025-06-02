<div class="row">
    <!-- Nom du category -->
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">
            <i class="bi bi-box me-1"></i>
            Nom du category <span class="text-danger">*</span>
        </label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name', $category->name ?? '') }}"
               placeholder="Saisissez le nom du category"
               required>
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    <!-- Agence -->
    <div class="col-md-6 mb-3">
        <label for="agency_id" class="form-label">
            <i class="bi bi-building me-1"></i>
            Agence
        </label>
        <select class="form-select @error('agency_id') is-invalid @enderror"
                id="agency_id"
                name="agency_id">
            <option value="">Sélectionnez une agence</option>
            @foreach($agencies as $agency)
                <option value="{{ $agency->id }}"
                        {{ old('agency_id', $category->agency_id ?? '') == $agency->id ? 'selected' : '' }}>
                    {{ $agency->name }}
                </option>
            @endforeach
        </select>
        @error('agency_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

</div>


<!-- Description -->
<div class="mb-3">
    <label for="description" class="form-label">
        <i class="bi bi-card-text me-1"></i>
        Description
    </label>
    <textarea class="form-control @error('description') is-invalid @enderror"
              id="description"
              name="description"
              rows="4"
              placeholder="Description détaillée du category...">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<!-- Informations supplémentaires pour édition -->
@if(isset($category) && $category->exists)
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Créé par:</strong> {{ $category->createdBy->full_name ?? 'N/A' }}
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        <strong>Créé le:</strong> {{ $category->created_at->format('d/m/Y à H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Boutons d'action -->
<div class="d-flex justify-content-between mt-4">
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Retour à la liste
    </a>

    <div>
        @if(isset($category) && $category->exists)
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>
                Mettre à jour
            </button>
        @else
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Créer le category
            </button>
        @endif
    </div>
</div>
