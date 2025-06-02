@extends('layouts.app')

@section('title', 'Nouveau Client')

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
                <a href="{{ route('clients.index') }}" class="text-decoration-none">
                    <i class="bi bi-people"></i> Clients
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-plus-circle"></i> Nouveau Client
            </li>
        </ol>
    </nav>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        Créer un nouveau client
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf

                        @include('client._form')

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Créer le client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
