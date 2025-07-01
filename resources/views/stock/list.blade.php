@extends('layouts.app')

@section('title', 'Liste des produits')

@section('content')

<div class="px-4 container-fluid">

    <nav aria-label="breadcrumb" class="justify-between mb-4 d-flex">
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

