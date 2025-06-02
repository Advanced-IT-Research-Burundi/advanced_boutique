<div class="row">
    <!-- Type de patient -->
    <div class="col-md-6 mb-3">
        <label for="patient_type" class="form-label required">Type de client</label>
        <select class="form-select @error('patient_type') is-invalid @enderror"
                id="patient_type"
                name="patient_type"
                required>
            <option value="">Sélectionner le type</option>
            <option value="physique" {{ old('patient_type', $client->patient_type ?? '') == 'physique' ? 'selected' : '' }}>
                Personne physique
            </option>
            <option value="morale" {{ old('patient_type', $client->patient_type ?? '') == 'morale' ? 'selected' : '' }}>
                Personne morale
            </option>
        </select>
        @error('patient_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Nom -->
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label required">Nom</label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name', $client->name ?? '') }}"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <!-- Prénom -->
    <div class="col-md-6 mb-3">
        <label for="first_name" class="form-label">Prénom</label>
        <input type="text"
               class="form-control @error('first_name') is-invalid @enderror"
               id="first_name"
               name="first_name"
               value="{{ old('first_name', $client->first_name ?? '') }}">
        @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Nom de famille -->
    <div class="col-md-6 mb-3">
        <label for="last_name" class="form-label">Nom de famille</label>
        <input type="text"
               class="form-control @error('last_name') is-invalid @enderror"
               id="last_name"
               name="last_name"
               value="{{ old('last_name', $client->last_name ?? '') }}">
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row" id="entreprise-fields" style="display: none;">
    <!-- Société -->
    <div class="col-md-6 mb-3">
        <label for="societe" class="form-label">Société</label>
        <input type="text"
               class="form-control @error('societe') is-invalid @enderror"
               id="societe"
               name="societe"
               value="{{ old('societe', $client->societe ?? '') }}">
        @error('societe')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- NIF -->
    <div class="col-md-6 mb-3">
        <label for="nif" class="form-label">NIF</label>
        <input type="text"
               class="form-control @error('nif') is-invalid @enderror"
               id="nif"
               name="nif"
               value="{{ old('nif', $client->nif ?? '') }}">
        @error('nif')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <!-- Email -->
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               value="{{ old('email', $client->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Téléphone -->
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Téléphone</label>
        <input type="tel"
               class="form-control @error('phone') is-invalid @enderror"
               id="phone"
               name="phone"
               value="{{ old('phone', $client->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <!-- Adresse -->
    <div class="col-md-8 mb-3">
        <label for="address" class="form-label">Adresse</label>
        <textarea class="form-control @error('address') is-invalid @enderror"
                  id="address"
                  name="address"
                  rows="3">{{ old('address', $client->address ?? '') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Solde -->
    <div class="col-md-4 mb-3">
        <label for="balance" class="form-label">Solde initial (F)</label>
        <input type="number"
               class="form-control @error('balance') is-invalid @enderror"
               id="balance"
               name="balance"
               value="{{ old('balance', $client->balance ?? 0) }}"
               step="1"
               min="0">
        @error('balance')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <!-- Agence -->
    <div class="col-md-6 mb-3">
        <label for="agency_id" class="form-label">Agence</label>
        <select class="form-select @error('agency_id') is-invalid @enderror"
                id="agency_id"
                name="agency_id">
            <option value="">Sélectionner une agence</option>
            @foreach($agencies as $agency)
                <option value="{{ $agency->id }}"
                        {{ old('agency_id', $client->agency_id ?? '') == $agency->id ? 'selected' : '' }}>
                    {{ $agency->name }}
                </option>
            @endforeach
        </select>
        @error('agency_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientTypeSelect = document.getElementById('patient_type');
    const entrepriseFields = document.getElementById('entreprise-fields');

    function toggleEntrepriseFields() {
        if (patientTypeSelect.value === 'morale') {
            entrepriseFields.style.display = 'block';
        } else {
            entrepriseFields.style.display = 'none';
        }
    }

    // Initialiser l'affichage
    toggleEntrepriseFields();

    // Écouter les changements
    patientTypeSelect.addEventListener('change', toggleEntrepriseFields);
});
</script>

<style>
    .required::after {
        content: " *";
        color: red;
    }
</style>
