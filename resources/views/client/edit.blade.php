@extends('layouts.app')

@section('title', 'Modifier Client')

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
            <li class="breadcrumb-item">
                <a href="{{ route('clients.show', $client) }}" class="text-decoration-none">
                    <i class="bi bi-person"></i> {{ $client->name }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil"></i> Modifier
            </li>
        </ol>
    </nav>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Modifier le client
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('client._form')
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
