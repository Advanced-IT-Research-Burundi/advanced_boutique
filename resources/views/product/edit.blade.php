@extends('layouts.app')

@section('title', 'Modifier le Produit')

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
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <i class="bi bi-box-seam"></i> Produits
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                    {{ $product->name }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil"></i> Modifier
            </li>
        </ol>
    </nav>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Modifier le produit : {{ $product->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('product._form')

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Retour
                                    </a>
                                    <div>
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-list me-2"></i>
                                            Liste des produits
                                        </a>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Mettre à jour
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
