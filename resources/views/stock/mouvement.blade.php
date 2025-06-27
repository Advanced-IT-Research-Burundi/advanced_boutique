@extends('layouts.app')

@section('title', 'Mouvements')

@section('content')
<div>
    <div>
        <a href="{{ route('stocks.index') }}" class="btn btn-primary">Retour</a>

    </div>
    <div>
      @livewire('stock.stock-mouvement', ['stock' => $stock])
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Designation</th>
                        <th>Quantity</th>
                        <th> Unit</th>
                        <th>Price</th>
                        <th>Currency</th>
                        <th>Type</th>
                        <th>Invoice Ref</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Agene</th>
                        <th>Date</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($stock->stockProductMouvements as $stockProductMouvement)
                        <tr>
                            <td>{{ $stockProductMouvement->item_code }}</td>
                            <td>{{ $stockProductMouvement->item_designation }}</td>
                            <td>{{ $stockProductMouvement->item_quantity }}</td>
                            <td>{{ $stockProductMouvement->item_measurement_unit }}</td>
                            <td>{{ $stockProductMouvement->item_purchase_or_sale_price }}</td>
                            <td>{{ $stockProductMouvement->item_purchase_or_sale_currency }}</td>
                            <td>{{ $stockProductMouvement->item_movement_type }}</td>
                            <td>{{ $stockProductMouvement->invoice_ref }}</td>
                            <td>{{ $stockProductMouvement->description }}</td>
                            <td>{{ $stockProductMouvement->date }}</td>
                            <td>{{ $stockProductMouvement->time }}</td>
                            <td>{{ $stockProductMouvement->user }}</td>
                            <td>{{ $stockProductMouvement->item_movement_agency }}</td>
                            <td>{{ $stockProductMouvement->item_movement_stock }}</td>
                            <td>{{ $stockProductMouvement->item_movement_stock_product }}</td>
                            <td>{{ $stockProductMouvement->item_movement_system_or_device_id }}</td>
                            <td>{{ $stockProductMouvement->created_at }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection
