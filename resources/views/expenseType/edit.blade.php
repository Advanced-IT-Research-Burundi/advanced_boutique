@extends('layouts.app')

@section('title', 'Modifier le Type de Dépense')

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
                        Modifier le Type de Dépense
                    </h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form action="{{ route('expense-types.update', $expense_type) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $expense_type->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control">{{ old('description', $expense_type->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select name="agency_id" class="form-select">
                                <option value="">Sélectionner une agence</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ old('agency_id', $expense_type->agency_id) == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('expense-types.index') }}" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
