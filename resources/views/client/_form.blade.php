<div class="row">

    <div class="col-md-6 mb-3">
        <label for="patient_type" class="form-label required">Type de client</label>
        <select class="form-select @error('patient_type') is-invalid @enderror"
                id="patient_type"
                name="patient_type"
                required>
            <option value="physique" {{ old('patient_type', $client->patient_type ?? 'physique') == 'physique' ? 'selected' : '' }}>
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


    <div class="col-md-6 mb-3">
        <label for="name" class="form-label required" id="name_label">
            {{ old('patient_type', $client->patient_type ?? 'physique') == 'morale' ? 'Nom' : 'Nom' }}
        </label>
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


<div class="row" id="personne-physique-fields"
     style="display: {{ old('patient_type', $client->patient_type ?? 'physique') == 'physique' ? 'flex' : 'none' }};">

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


<div class="row" id="personne-morale-fields"
     style="display: {{ old('patient_type', $client->patient_type ?? '') == 'morale' ? 'flex' : 'none' }};">

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
    <div class="col-md-12 mb-3">
        <label for="address" class="form-label">Adresse</label>
        <textarea class="form-control @error('address') is-invalid @enderror"
                  id="address"
                  name="address"
                  rows="3">{{ old('address', $client->address ?? '') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- <div class="col-md-4 mb-3">
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
    </div> --}}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientTypeSelect = document.getElementById('patient_type');
    const personnePhysiqueFields = document.getElementById('personne-physique-fields');
    const personneMoraleFields = document.getElementById('personne-morale-fields');
    const nameLabel = document.getElementById('name_label');

    function toggleFields() {
        if (patientTypeSelect.value === 'morale') {
            personnePhysiqueFields.style.display = 'none';
            personneMoraleFields.style.display = 'flex';
            nameLabel.textContent = 'Nom';

            document.getElementById('first_name').value = '';
            document.getElementById('last_name').value = '';
        } else {

            personnePhysiqueFields.style.display = 'flex';
            personneMoraleFields.style.display = 'none';
            nameLabel.textContent = 'Nom';

            document.getElementById('societe').value = '';
            document.getElementById('nif').value = '';
        }
    }

    toggleFields();

    patientTypeSelect.addEventListener('change', toggleFields);
});
</script>

<style>
    .required::after {
        content: " *";
        color: red;
    }

    .fade-transition {
        transition: opacity 0.3s ease-in-out;
    }
</style>
