<div class="row">
    <!-- Informations Générales -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informations Générales
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">
                            <i class="bi bi-hash text-primary me-1"></i>
                            Code <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('code') is-invalid @enderror"
                               id="code"
                               name="code"
                               value="{{ old('code', $agency->code ?? '') }}"
                               placeholder="Ex: AG001"
                               required>
                        @error('code')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-building text-primary me-1"></i>
                            Nom de l'agence <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $agency->name ?? '') }}"
                               placeholder="Ex: Agence Centre-Ville"
                               required>
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">
                        <i class="bi bi-geo-alt text-primary me-1"></i>
                        Adresse <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('adresse') is-invalid @enderror"
                              id="adresse"
                              name="adresse"
                              rows="3"
                              placeholder="Entrez l'adresse complète de l'agence..."
                              required>{{ old('adresse', $agency->adresse ?? '') }}</textarea>
                    @error('adresse')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="company_id" class="form-label">
                            <i class="bi bi-briefcase text-primary me-1"></i>
                            Entreprise <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('company_id') is-invalid @enderror"
                                id="company_id"
                                name="company_id"
                                required>
                            <option value="">Sélectionnez une entreprise</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}"
                                        {{ old('company_id', $agency->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="manager_id" class="form-label">
                            <i class="bi bi-person-gear text-primary me-1"></i>
                            Manager
                        </label>
                        <select class="form-select @error('manager_id') is-invalid @enderror"
                                id="manager_id"
                                name="manager_id">
                            <option value="">Aucun manager assigné</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}"
                                        {{ old('manager_id', $agency->manager_id ?? '') == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('manager_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Configuration -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Configuration
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="parent_agency_id" class="form-label">
                        <i class="bi bi-diagram-3 text-success me-1"></i>
                        Agence Parent
                    </label>
                    <select class="form-select @error('parent_agency_id') is-invalid @enderror"
                            id="parent_agency_id"
                            name="parent_agency_id">
                        <option value="">Aucune agence parent</option>
                        @foreach($parentAgencies as $parentAgency)
                            <option value="{{ $parentAgency->id }}"
                                    {{ old('parent_agency_id', $agency->parent_agency_id ?? '') == $parentAgency->id ? 'selected' : '' }}>
                                {{ $parentAgency->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_agency_id')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        Laissez vide si c'est une agence principale
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                               type="checkbox"
                               role="switch"
                               id="is_main_office"
                               name="is_main_office"
                               value="1"
                               {{ old('is_main_office', $agency->is_main_office ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_main_office">
                            <i class="bi bi-star text-warning me-1"></i>
                            <strong>Siège Social</strong>
                        </label>
                    </div>
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        Cochez si cette agence est le siège social
                    </div>
                </div>

                <!-- Status Info -->
                <div class="alert alert-info">
                    <i class="bi bi-lightbulb me-2"></i>
                    <strong>Information :</strong>
                    <ul class="mb-0 mt-2">
                        <li>Le code doit être unique</li>
                        <li>Une agence peut avoir des sous-agences</li>
                        <li>Seul un siège social peut être défini par entreprise</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ isset($agency) ? 'Mettre à jour' : 'Créer l\'agence' }}
                    </button>
                    <a href="{{ route('agencies.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate code based on name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');

    if (nameInput && codeInput && !codeInput.value) {
        nameInput.addEventListener('input', function() {
            const name = this.value;
            if (name.length >= 3) {
                const code = 'AG' + name.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, '') +
                           Math.floor(Math.random() * 100).toString().padStart(2, '0');
                codeInput.value = code;
            }
        });
    }

    // Validation en temps réel
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    }
});
</script>
@endpush
