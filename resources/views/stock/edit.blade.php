@extends('layouts.app')

@section('title', 'Modifier le Stock')

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
                <a href="{{ route('stocks.index') }}" class="text-decoration-none">
                    <i class="bi bi-boxes"></i> Stocks
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('stocks.show', $stock) }}" class="text-decoration-none">
                    {{ $stock->name }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil"></i> Modifier
            </li>
        </ol>
    </nav>

    <!-- Formulaire de modification -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Modifier le stock: {{ $stock->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('stocks.update', $stock) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('stock._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
