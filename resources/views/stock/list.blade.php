@extends('layouts.app')

@section('title', 'Liste des produits')

@section('content')

<div class="container-fluid px-4">

    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('stocks.index') }}">Stocks</a></li>
            <li class="breadcrumb-item active" aria-current="page">Liste des produits</li>
        </ol>
    </nav>

    <div>
        @livewire('stock.add-product-stock', ['stock' => $stock])
    </div>

</div>

@endsection

