@extends('layouts.app')

@section('title', 'Créer un category')

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
                <a href="{{ route('categories.index') }}" class="text-decoration-none">
                    <i class="bi bi-boxes"></i> categories
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-plus-circle"></i> Nouveau category
            </li>
        </ol>
    </nav>

    <!-- Formulaire de création -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer un nouveau category
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        @include('category._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
