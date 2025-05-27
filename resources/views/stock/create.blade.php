@extends('layouts.app')

@section('title', 'Ajouter un stock')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un nouveau stock</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('stocks.store') }}" method="POST" autocomplete="off">
                        @include('stock._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Scripts spécifiques à la création si nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des sélecteurs avec Select2 si nécessaire
        $('#agency_id, #user_id').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush
