@extends('layouts.app')

@section('title', 'Modifier le stock')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier le stock : {{ $stock->name }}</h5>
                    <a href="{{ route('stocks.show', $stock->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
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

@push('styles')
<!-- Styles spécifiques à l'édition -->
<style>
    /* Ajoutez vos styles personnalisés ici si nécessaire */
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des sélecteurs avec Select2 si nécessaire
        $('#agency_id, #user_id').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Autres scripts spécifiques à l'édition
    });
</script>
@endpush
