@extends('layouts.app')

@section('title', 'Détails du stock')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails du stock</h5>
                    <div class="btn-group">
                        <a href="{{ route('stocks.edit', $stock->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-4 fw-bold">Nom :</div>
                        <div class="col-md-8">{{ $stock->name }}</div>
                    </div>

                    @if($stock->location)
                    <div class="mb-3 row">
                        <div class="col-md-4 fw-bold">Emplacement :</div>
                        <div class="col-md-8">{{ $stock->location }}</div>
                    </div>
                    @endif

                    @if($stock->agency)
                    <div class="mb-3 row">
                        <div class="col-md-4 fw-bold">Agence :</div>
                        <div class="col-md-8">{{ $stock->agency->name }}</div>
                    </div>
                    @endif

                    @if($stock->user)
                    <div class="mb-3 row">
                        <div class="col-md-4 fw-bold">Responsable :</div>
                        <div class="col-md-8">{{ $stock->user->name }}</div>
                    </div>
                    @endif

                    @if($stock->description)
                    <div class="mb-3 row">
                        <div class="mb-2 col-12 fw-bold">Description :</div>
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    {!! nl2br(e($stock->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-4 row">
                        <div class="col-md-6">
                            <div class="small text-muted">
                                Créé le : {{ $stock->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="small text-muted">
                                Dernière mise à jour : {{ $stock->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
