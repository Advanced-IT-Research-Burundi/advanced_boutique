@extends('layouts.app')

@section('title', 'Facture #' . $sale->id)

@section('content')
<div class="container-fluid py-4">
    <!-- Header avec actions -->
    <div class="row mb-4 no-print">
        <div class="col-md-6">
            <h2 class="h4 mb-0 text-gray-800">
                <i class="fas fa-file-invoice me-2"></i>
                Facture #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
            </h2>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour
                </a>
                {{-- <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>
                    Modifier
                </a> --}}
                <button id="printInvoice"  class="btn btn-success">
                    <i class="fas fa-print me-1"></i>
                    Imprimer Facture
                </button>
                <button id="printReceipt" class="btn btn-info">
                    <i class="fas fa-receipt me-1"></i>
                    Imprimer Reçu POS
                </button>
                <a href="{{ route('sales.pdf', $sale) }}"  class="btn btn-danger">
                    <i class="fas fa-download me-1"></i>
                    PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Facture -->
    <div class="invoice-container" id="invoiceContent">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <!-- Header de la facture -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        @if(isset($company) && $company->tp_logo)
                            <img src="{{ asset('storage/' . $company->tp_logo) }}" alt="Logo" class="company-logo mb-3">
                        @endif
                        <div class="company-info">
                            <h3 class="company-name">{{ $company->tp_name ?? 'Nom de l\'entreprise' }}</h3>
                            <div class="company-details">
                                @if($company->tp_address ?? false)
                                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $company->tp_address }}</p>
                                @endif
                                @if($company->tp_phone_number ?? false)
                                    <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $company->tp_phone_number }}</p>
                                @endif
                                @if($company->tp_email ?? false)
                                    <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $company->tp_email }}</p>
                                @endif
                                @if($company->tp_TIN ?? false)
                                    <p class="mb-0"><strong>NIF:</strong> {{ $company->tp_TIN }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="invoice-header">
                            <h1 class="invoice-title">FACTURE</h1>
                            <div class="invoice-meta">
                                <p class="invoice-number"># {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</p>
                                <div class="invoice-dates">
                                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</p>
                                    <p><strong>Heure:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations client -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <div class="client-info">
                            <h5 class="section-title">Facturé à:</h5>
                            <div class="client-details">
                                <h6 class="client-name">
                                    @if($sale->client->patient_type === 'morale')
                                        {{ $sale->client->societe }}
                                    @else
                                        {{ $sale->client->name }} {{ $sale->client->first_name }} {{ $sale->client->last_name }}
                                    @endif
                                </h6>
                                @if($sale->client->nif)
                                    <p class="mb-1"><strong>NIF:</strong> {{ $sale->client->nif }}</p>
                                @endif
                                @if($sale->client->phone)
                                    <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $sale->client->phone }}</p>
                                @endif
                                @if($sale->client->email)
                                    <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $sale->client->email }}</p>
                                @endif
                                @if($sale->client->address)
                                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>{{ $sale->client->address }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sale-info">
                            <h5 class="section-title">Statut de paiement:</h5>
                            <div class="sale-details">
                                <p><strong>Statut:</strong>
                                    @if($sale->due_amount <= 0)
                                        <span class="badge bg-success">Payée</span>
                                    @else
                                        <span class="badge bg-warning">Partiellement payée</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des articles -->
                <div class="table-responsive mb-4">
                    <table class="table invoice-table">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 40%">Article</th>
                                <th style="width: 10%" class="text-center">Qté</th>
                                <th style="width: 15%" class="text-end">Prix unitaire</th>
                                <th style="width: 10%" class="text-center">Remise</th>
                                <th style="width: 20%" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->saleItems as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="product-info">
                                        <strong>{{ $item->product->name }}</strong>
                                        @if($item->product->description)
                                            <small class="text-muted d-block">{{ $item->product->description }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                                <td class="text-end">{{ number_format($item->sale_price, 0) }} BIF</td>
                                <td class="text-center">
                                    @if($item->discount > 0)
                                        {{ number_format($item->discount, 0) }} BIF
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end font-weight-bold">{{ number_format($item->subtotal, 0) }} BIF</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totaux et montant en lettres -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="payment-info">
                            <h6 class="mb-3">Informations de paiement:</h6>
                            <div class="payment-details">
                                <p><strong>Montant payé:</strong> {{ number_format($sale->paid_amount, 0) }} BIF</p>
                                @if($sale->due_amount > 0)
                                    <p class="text-danger"><strong>Montant dû:</strong> {{ number_format($sale->due_amount, 0) }} BIF</p>
                                @endif
                            </div>
                            <div class="amount-in-words mt-3 p-3 bg-light rounded">
                                <h6>Montant payé en lettres:</h6>
                                <p class="mb-0"><em>{{ getNumberToWord($sale->paid_amount) }} francs burundais</em></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="invoice-totals">
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-end"><strong>Sous-total:</strong></td>
                                    <td class="text-end">{{ number_format($sale->saleItems->sum('subtotal'), 0) }} BIF</td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>Remise totale:</strong></td>
                                    <td class="text-end">{{ number_format($sale->saleItems->sum('discount'), 0) }} BIF</td>
                                </tr>
                                <tr class="table-dark">
                                    <td class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($sale->total_amount, 0) }} BIF</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="invoice-footer mt-5 pt-4 border-top">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="notes">
                                <h6>Notes:</h6>
                                <p class="text-muted">Merci pour votre confiance.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="signature-area">
                                <div class="signature-line mt-5">
                                    <hr>
                                    <small class="text-muted">Signature autorisée</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reçu POS (caché par défaut) -->
    <div class="receipt-container d-none" id="receiptContent">
        <div class="receipt">
            <div class="receipt-header text-center">
                <h4>{{ $company->tp_name ?? 'ENTREPRISE' }}</h4>
                <p>{{ $company->tp_address ?? 'Adresse' }}</p>
                <p>Tel: {{ $company->tp_phone_number ?? 'N/A' }}</p>
                @if($company->tp_TIN ?? false)
                    <p>NIF: {{ $company->tp_TIN }}</p>
                @endif
                <div class="receipt-divider">================================</div>
                <h5>REÇU DE VENTE</h5>
                <div class="receipt-divider">================================</div>
            </div>

            <div class="receipt-info">
                <p><strong>N° Reçu:</strong> {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</p>
                <p><strong>Client:</strong>
                    @if($sale->client->patient_type === 'morale')
                        {{ $sale->client->societe }}
                    @else
                        {{ $sale->client->name }} {{ $sale->client->first_name }}
                    @endif
                </p>
                <div class="receipt-divider">--------------------------------</div>
            </div>

            <div class="receipt-items">
                @foreach($sale->saleItems as $item)
                <div class="receipt-item">
                    <div class="item-name">{{ $item->product->name }}</div>
                    <div class="item-details">
                        {{ $item->quantity }} x {{ number_format($item->sale_price, 0) }}
                        @if($item->discount > 0)
                            (-{{ number_format($item->discount, 0) }})
                        @endif
                        <span class="float-end">{{ number_format($item->subtotal, 0) }}</span>
                    </div>
                </div>
                @endforeach
                <div class="receipt-divider">--------------------------------</div>
            </div>

            <div class="receipt-totals">
                <div class="total-line">
                    <span>Sous-total:</span>
                    <span class="float-end">{{ number_format($sale->saleItems->sum('subtotal'), 0) }} BIF</span>
                </div>
                @if($sale->saleItems->sum('discount') > 0)
                <div class="total-line">
                    <span>Remise:</span>
                    <span class="float-end">-{{ number_format($sale->saleItems->sum('discount'), 0) }} BIF</span>
                </div>
                @endif
                <div class="receipt-divider">================================</div>
                <div class="total-line total-final">
                    <span><strong>TOTAL:</strong></span>
                    <span class="float-end"><strong>{{ number_format($sale->total_amount, 0) }} BIF</strong></span>
                </div>
                <div class="total-line">
                    <span>Payé:</span>
                    <span class="float-end">{{ number_format($sale->paid_amount, 0) }} BIF</span>
                </div>
                @if($sale->due_amount > 0)
                <div class="total-line text-danger">
                    <span>Dû:</span>
                    <span class="float-end">{{ number_format($sale->due_amount, 0) }} BIF</span>
                </div>
                @endif
            </div>

            <div class="receipt-footer text-center mt-3">
                <div class="receipt-divider">================================</div>
                <p><small>Montant payé: {{ getNumberToWord($sale->paid_amount) }} francs burundais</small></p>
                <div class="receipt-divider">================================</div>
                <p><small>Merci pour votre visite!</small></p>
                <p><small>{{ now()->format('d/m/Y H:i:s') }}</small></p>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/facture.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/facture.js')}}"></script>
@endpush
