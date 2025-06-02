@extends('layouts.app')

@section('title', 'Modifier le Fournisseur')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('suppliers.index') }}" class="text-decoration-none">
                    <i class="bi bi-truck"></i> Fournisseurs
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil"></i> Modifier
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Modifier le fournisseur
                    </h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="agency_id" class="form-label">Agence</label>
                                <select name="agency_id" class="form-select">
                                    <option value="">Sélectionner une agence</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->id }}" {{ old('agency_id', $supplier->agency_id) == $agency->id ? 'selected' : '' }}>
                                            {{ $agency->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $supplier->address) }}">
                            </div>
                        </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-end">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Mettre à jour
                    </button>
                </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection
