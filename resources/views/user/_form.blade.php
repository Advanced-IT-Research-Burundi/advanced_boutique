<div class="row">
    <!-- Informations personnelles -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>
                    Informations personnelles
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('first_name') is-invalid @enderror"
                               id="first_name"
                               name="first_name"
                               value="{{ old('first_name', $user->first_name ?? '') }}"
                               required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('last_name') is-invalid @enderror"
                               id="last_name"
                               name="last_name"
                               value="{{ old('last_name', $user->last_name ?? '') }}"
                               required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email', $user->email ?? '') }}"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="tel"
                           class="form-control @error('phone') is-invalid @enderror"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', $user->phone ?? '') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">Date de naissance</label>
                        <input type="date"
                               class="form-control @error('date_of_birth') is-invalid @enderror"
                               id="date_of_birth"
                               name="date_of_birth"
                               value="{{ old('date_of_birth', $user->date_of_birth ?? '') }}">
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Genre</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">Sélectionner...</option>
                            <option value="male" {{ old('gender', $user->gender ?? '') === 'male' ? 'selected' : '' }}>Homme</option>
                            <option value="female" {{ old('gender', $user->gender ?? '') === 'female' ? 'selected' : '' }}>Femme</option>
                            <option value="other" {{ old('gender', $user->gender ?? '') === 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Adresse</label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                              id="address"
                              name="address"
                              rows="3">{{ old('address', $user->address ?? '') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="profile_photo" class="form-label">Photo de profil</label>
                    <input type="file"
                           class="form-control @error('profile_photo') is-invalid @enderror"
                           id="profile_photo"
                           name="profile_photo"
                           accept="image/*">
                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($user) && $user->profile_photo)
                        <div class="mt-2">
                            <img src="{{ Storage::url($user->profile_photo) }}"
                                 alt="Photo actuelle"
                                 class="img-thumbnail"
                                 width="100">
                            <small class="text-muted d-block">Photo actuelle</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informations professionnelles -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-briefcase me-2"></i>
                    Informations professionnelles
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">Sélectionner un rôle...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role ?? '') === $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $user->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="suspended" {{ old('status', $user->status ?? '') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="company_id" class="form-label">Entreprise</label>
                    <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                        <option value="">Sélectionner une entreprise...</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $user->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="agency_id" class="form-label">Agence</label>
                    <select class="form-select @error('agency_id') is-invalid @enderror" id="agency_id" name="agency_id">
                        <option value="">Sélectionner une agence...</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}" {{ old('agency_id', $user->agency_id ?? '') == $agency->id ? 'selected' : '' }}>
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

        <!-- Sécurité -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="bi bi-shield-lock me-2"></i>
                    Sécurité
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="password" class="form-label">
                        Mot de passe
                        @if(!isset($user))
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           {{ !isset($user) ? 'required' : '' }}>
                    @if(isset($user))
                        <small class="form-text text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                    @endif
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        Confirmer le mot de passe
                        @if(!isset($user))
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="password"
                           class="form-control"
                           id="password_confirmation"
                           name="password_confirmation"
                           {{ !isset($user) ? 'required' : '' }}>
                </div>

                @if(isset($user))
                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="checkbox"
                               id="must_change_password"
                               name="must_change_password"
                               value="1"
                               {{ old('must_change_password', $user->must_change_password ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="must_change_password">
                            Forcer le changement de mot de passe à la prochaine connexion
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="checkbox"
                               id="two_factor_enabled"
                               name="two_factor_enabled"
                               value="1"
                               {{ old('two_factor_enabled', $user->two_factor_enabled ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="two_factor_enabled">
                            Activer l'authentification à deux facteurs
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Boutons d'action -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour
            </a>
            <div>
                <button type="reset" class="btn btn-outline-warning me-2">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    Réinitialiser
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-2"></i>
                    {{ isset($user) ? 'Modifier' : 'Créer' }}
                </button>
            </div>
        </div>
    </div>
</div>
