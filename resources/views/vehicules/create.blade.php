@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Ajouter un nouveau véhicule</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('vehicules.store') }}" method="POST">
                        @csrf

                        @include('vehicules._form')

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
