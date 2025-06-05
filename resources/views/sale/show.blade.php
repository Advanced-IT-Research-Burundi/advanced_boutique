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
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>
                    Modifier
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print me-1"></i>
                    Imprimer
                </button>
                <button onclick="downloadPDF()" class="btn btn-danger">
                    <i class="fas fa-download me-1"></i>
                    PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Facture -->
    <div class="invoice-container">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5" id="invoice-content">
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
                            <h5 class="section-title">Informations de vente:</h5>
                            <div class="sale-details">
                                <p><strong>Vendeur:</strong> {{ $sale->user->name }}</p>
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

                <!-- Totaux -->
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
                                <p class="text-muted">Merci pour votre confiance. Cette facture est générée automatiquement.</p>
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
</div>

<style>
/* Styles généraux */
.invoice-container {
    max-width: 1000px;
    margin: 0 auto;
}

/* Header de la facture */
.company-logo {
    max-height: 80px;
    max-width: 200px;
}

.company-name {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 10px;
}

.company-details p {
    color: #6c757d;
    font-size: 0.9rem;
}

.invoice-title {
    color: #2c3e50;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.invoice-number {
    font-size: 1.2rem;
    font-weight: 600;
    color: #007bff;
    margin-bottom: 15px;
}

.invoice-dates p {
    margin-bottom: 5px;
    color: #6c757d;
}

/* Sections client et vente */
.section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 2px solid #007bff;
}

.client-name {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 10px;
}

.client-details p, .sale-details p {
    margin-bottom: 8px;
    color: #6c757d;
}

/* Tableau des articles */
.invoice-table {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.invoice-table thead th {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.invoice-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.invoice-table tbody tr:hover {
    background-color: #f8f9fa;
}

.invoice-table td {
    padding: 15px 12px;
    vertical-align: middle;
}

.product-info strong {
    color: #2c3e50;
}

/* Totaux */
.invoice-totals .table td {
    border: none;
    padding: 8px 15px;
    font-size: 1rem;
}

.invoice-totals .table-dark td {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    font-size: 1.1rem;
}

/* Informations de paiement */
.payment-details p {
    margin-bottom: 8px;
    font-size: 1rem;
}

/* Footer */
.invoice-footer {
    margin-top: 40px;
}

.signature-line {
    width: 200px;
    margin-left: auto;
}

.signature-line hr {
    margin-bottom: 5px;
    border-color: #2c3e50;
}

/* Badges */
.badge {
    font-size: 0.8rem;
    padding: 6px 12px;
}

/* Styles d'impression */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        font-size: 12px;
        line-height: 1.4;
    }

    .invoice-container {
        max-width: 100%;
        margin: 0;
    }

    .card {
        box-shadow: none !important;
        border: none !important;
    }

    .card-body {
        padding: 20px !important;
    }

    .invoice-table {
        box-shadow: none;
    }

    .invoice-table thead th {
        background: #2c3e50 !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }

    .invoice-totals .table-dark td {
        background: #2c3e50 !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }

    .badge {
        border: 1px solid #000;
        background: transparent !important;
        color: #000 !important;
    }

    /* Saut de page */
    .invoice-container {
        page-break-inside: avoid;
    }
}

/* Responsivité */
@media (max-width: 768px) {
    .invoice-title {
        font-size: 2rem;
    }

    .card-body {
        padding: 20px !important;
    }

    .invoice-table {
        font-size: 0.85rem;
    }

    .company-logo {
        max-height: 60px;
    }

    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .btn-group .btn {
        flex: 1;
        min-width: auto;
    }
}

/* Animations subtiles */
.card {
    transition: transform 0.2s ease;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Amélioration de l'accessibilité */
.invoice-table thead th {
    position: relative;
}

.table-responsive {
    border-radius: 8px;
}

/* Style pour les icônes */
.fas {
    width: 16px;
    text-align: center;
}
</style>

<script>
// Fonction pour télécharger en PDF
function downloadPDF() {
    // Option 1: Utiliser window.print() avec une CSS print optimisée
    window.print();

    // Option 2: Si vous voulez implémenter jsPDF ou une autre solution
    // Vous pouvez ajouter votre logique ici
}

// Amélioration de l'impression
window.addEventListener('beforeprint', function() {
    document.title = 'Facture #{{ str_pad($sale->id, 6, "0", STR_PAD_LEFT) }} - {{ $sale->client->name }}';
});

// Animation d'entrée
document.addEventListener('DOMContentLoaded', function() {
    const card = document.querySelector('.card');
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';

    setTimeout(() => {
        card.style.transition = 'all 0.5s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
    }, 100);
});
</script>
@endsection
