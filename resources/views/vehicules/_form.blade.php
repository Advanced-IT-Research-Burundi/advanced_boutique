@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="brand" class="form-label">Marque</label>
            <input type="text" class="form-control @error('brand') is-invalid @enderror"
                   id="brand" name="brand"
                   value="{{ old('brand', $vehicule->brand ?? '') }}" required>
            @error('brand')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="model" class="form-label">Modèle</label>
            <input type="text" class="form-control @error('model') is-invalid @enderror"
                   id="model" name="model"
                   value="{{ old('model', $vehicule->model ?? '') }}" required>
            @error('model')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="year" class="form-label">Année</label>
            <input type="number" class="form-control @error('year') is-invalid @enderror"
                   id="year" name="year" min="1900" max="{{ date('Y') + 1 }}"
                   value="{{ old('year', $vehicule->year ?? '') }}" required>
            @error('year')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="immatriculation" class="form-label">Immatriculation</label>
            <input type="text" class="form-control @error('immatriculation') is-invalid @enderror"
                   id="immatriculation" name="immatriculation"
                   value="{{ old('immatriculation', $vehicule->immatriculation ?? '') }}" required>
            @error('immatriculation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-4">
    <label for="status" class="form-label">Statut</label>
    <select class="form-select @error('status') is-invalid @enderror"
            id="status" name="status" required>
        <option value="" disabled {{ !isset($vehicule->status) ? 'selected' : '' }}>Sélectionnez un statut</option>
        <option value="disponible" {{ old('status', $vehicule->status ?? '') == 'disponible' ? 'selected' : '' }}>Disponible</option>
        <option value="en_location" {{ old('status', $vehicule->status ?? '') == 'en_location' ? 'selected' : '' }}>En location</option>
        <option value="en_reparation" {{ old('status', $vehicule->status ?? '') == 'en_reparation' ? 'selected' : '' }}>En réparation</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror"
              id="description" name="description" rows="3">{{ old('description', $vehicule->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mt-4 d-flex justify-content-between">
    <a href="{{ route('vehicules.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> {{ isset($vehicule) ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
</div>
