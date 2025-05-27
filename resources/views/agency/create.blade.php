@extends('layouts.app')

@section('title', 'Créer une Agence')

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
                <a href="{{ route('agencies.index') }}" class="text-decoration-none">
                    <i class="bi bi-building"></i> Agences
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-plus-circle"></i> Créer
            </li>
        </ol>
    </nav>

    <!-- Form -->
    <form action="{{ route('agencies.store') }}" method="POST" novalidate>
        @csrf
        @include('agency._form')
    </form>
</div>

@endsection
