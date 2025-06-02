@extends('layouts.app')

@section('title', 'Créer un Produit')

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
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-plus-circle"></i> Créer
            </li>
        </ol>
    </nav>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer un nouveau produit
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @include('product._form')

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Retour
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-arrow-clockwise me-2"></i>
                                            Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Créer le produit
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
