@extends('layouts.app')

@section('title', 'Détail du Type de Dépense')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('expense-types.index') }}" class="text-decoration-none">
                    <i class="bi bi-tags"></i> Types de Dépense
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-eye"></i> Détail
            </li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0">
                        <i class="bi bi-eye me-2"></i>
                        Détail du Type de Dépense
                    </h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nom</dt>
                        <dd class="col-sm-8">{{ $expense_type->name }}</dd>

                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $expense_type->description }}</dd>

                        <dt class="col-sm-4">Agence</dt>
                        <dd class="col-sm-8">{{ $expense_type->agency->label ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="card-footer bg-light d-flex justify-content-end">
                    <a href="{{ route('expense-types.edit', $expense_type) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <a href="{{ route('expense-types.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
