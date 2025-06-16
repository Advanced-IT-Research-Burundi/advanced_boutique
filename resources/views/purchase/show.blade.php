@extends('layouts.app')

@section('title', "Détail de l'achat #{$purchase->id}")

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-eye me-2"></i>
                        Détail de l'achat #{{ $purchase->id }}
                    </h4>
                    <div>
                        <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-success btn-sm me-2" target="_blank">
                            <i class="bi bi-printer me-1"></i>
                            Imprimer facture
                        </a>
                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning btn-sm me-2">
                            <i class="bi bi-pencil-square me-1"></i>
                            Modifier
                        </a>
                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>
                            Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Fournisseur :</strong></td>
                                    <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Stock :</strong></td>
                                    <td>{{ $purchase->stock->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Agence :</strong></td>
                                    <td>{{ $purchase->agency->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date d'achat :</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Créé le :</strong></td>
                                    <td>{{ $purchase->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dernière modification :</strong></td>
                                    <td>{{ $purchase->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-calculator me-2"></i>Résumé financier</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Montant total :</strong></td>
                                    <td class="text-success"><strong>{{ number_format($purchase->total_amount, 2, ',', ' ') }} Fbu</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Montant payé :</strong></td>
                                    <td class="text-info">{{ number_format($purchase->paid_amount, 2, ',', ' ') }} FBU</td>
                                </tr>
                                <tr>
                                    <td><strong>Reste à payer :</strong></td>
                                    <td class="{{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                        <strong>{{ number_format($purchase->due_amount, 2, ',', ' ') }} </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        @if($purchase->due_amount == 0)
                                            <span class="badge bg-success">Payé</span>
                                        @elseif($purchase->paid_amount > 0)
                                            <span class="badge bg-warning">Partiellement payé</span>
                                        @else
                                            <span class="badge bg-danger">Non payé</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="mt-4"><i class="bi bi-basket3 me-2"></i>Produits achetés ({{ $purchase->purchaseItems->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->purchaseItems as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->name ?? '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->purchase_price, 2, ',', ' ') }} Fbu</td>
                                        <td class="text-success"><strong>{{ number_format($item->subtotal, 2, ',', ' ') }} Fbu</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Retour à la liste
                        </a>
                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square me-1"></i>
                            Modifier cet achat
                        </a>
                        <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-success" target="_blank">
                            <i class="bi bi-printer me-1"></i>
                            Imprimer facture
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
