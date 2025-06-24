@extends('layouts.app')

@section('title', 'Entre Multiple en Stock' )

@section('content')
<div>
    @livewire("stock.entre-multiple", [
        'stock' => $stock
        ])
</div>
@stop
