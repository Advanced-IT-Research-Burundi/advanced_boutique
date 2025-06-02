@extends('layouts.app')

@section('title', 'Détail du Fournisseur')

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
                <i class="bi bi-eye"></i> Détail
            </li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-truck me-2"></i>
                {{ $supplier->name }}
            </h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nom</dt>
                <dd class="col-sm-9">{{ $supplier->name }}</dd>

                <dt class="col-sm-3">Téléphone</dt>
                <dd class="col-sm-9">{{ $supplier->phone ?? 'Non renseigné' }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $supplier->email ?? 'Non renseigné' }}</dd>

                <dt class="col-sm-3">Adresse</dt>
                <dd class="col-sm-9">{{ $supplier->address ?? 'Non renseignée' }}</dd>

                <dt class="col-sm-3">Agence</dt>
                <dd class="col-sm-9">{{ $supplier->agency->name ?? 'Non assignée' }}</dd>
            </dl>
        </div>
        <div class="card-footer d-flex justify-content-end bg-light">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>
@endsection
