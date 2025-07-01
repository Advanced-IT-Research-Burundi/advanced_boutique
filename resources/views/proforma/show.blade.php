@extends('layouts.app')

@section('title', 'Facture Proforma #' . str_pad($proforma->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 text-primary">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Facture Proforma
                    </h2>
                    <p class="mb-0 text-muted">PRO-{{ str_pad($proforma->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="gap-2 d-flex">
                    <a href="{{ route('proformas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Retour
                    </a>

                    <a class="btn btn-outline-success" href="{{ route('proformas.validate', $proforma->id) }}" onclick="return confirm('Êtes-vous sûr de vouloir valider cette facture proforma ?')">
                        <i class="bi bi-printer me-1"></i>Valider
                    </a>
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Imprimer
                    </button>
                    <button class="btn btn-primary" onclick="downloadPDF()">
                        <i class="bi bi-download me-1"></i>Télécharger
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{$proforma}}

    <!-- Invoice Content -->
    <div class="border-0 shadow-sm card" id="invoice-content">
        <div class="card-body">
            <!-- Invoice Header -->
            <div class="mb-4 row">
                <div class="col-md-6">
                    <h3 class="text-primary">{{ config('app.name', 'GESTION STOCK') }}</h3>
                    <p class="mb-1">{{ $proforma->agency->name ?? 'Agence principale' }}</p>
                    <p class="mb-1">{{ $proforma->agency->address ?? 'Adresse de l\'agence' }}</p>
                    <p class="mb-1">Tél: {{ $proforma->agency->phone ?? '+257 XX XX XX XX' }}</p>
                    <p class="mb-0">Email: {{ $proforma->agency->email ?? 'email@agence.com' }}</p>
                </div>
                <div class="text-end col-md-6">
                    <h4 class="text-primary">FACTURE PROFORMA</h4>
                    <p class="mb-1"><strong>N°:</strong> PRO-{{ str_pad($proforma->id, 6, '0', STR_PAD_LEFT) }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($proforma->sale_date)->format('d/m/Y') }}</p>
                    <p class="mb-0"><strong>Créé par:</strong> {{ $proforma->createdBy->name ?? 'Utilisateur' }}</p>
                </div>
            </div>

            <hr class="my-4">

            <!-- Client Information -->
            <div class="mb-4 row">
                <div class="col-md-6">
                    <h5 class="mb-3">Informations Client</h5>
                    @if(!empty($client))
                        <p class="mb-1"><strong>Nom:</strong> {{ $client['name'] ?? 'Non spécifié' }}</p>
                        <p class="mb-1"><strong>Téléphone:</strong> {{ $client['phone'] ?? 'Non spécifié' }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $client['email'] ?? 'Non spécifié' }}</p>
                        <p class="mb-0"><strong>Adresse:</strong> {{ $client['address'] ?? 'Non spécifiée' }}</p>
                    @else
                        <p class="text-muted">Informations client non disponibles</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Détails de la Facture</h5>
                    <p class="mb-1"><strong>Type:</strong> {{ $proforma->invoice_type ?? 'Proforma' }}</p>
                    <p class="mb-1"><strong>Statut:</strong>
                        @if($proforma->due_amount == 0)
                            <span class="badge bg-success">Payé</span>
                        @elseif($proforma->due_amount < $proforma->total_amount)
                            <span class="badge bg-warning">Partiellement payé</span>
                        @else
                            <span class="badge bg-danger">Impayé</span>
                        @endif
                    </p>
                    @if($proforma->note)
                        <p class="mb-0"><strong>Note:</strong> {{ $proforma->note }}</p>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-4">
                <h5 class="mb-3">Articles</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Désignation</th>
                                <th>Unité</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-end">Prix Unit.</th>
                                <th class="text-end">Remise</th>
                                <th class="text-end">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQuantity = 0;
                                $totalDiscount = 0;
                            @endphp
                            @forelse($items as $item)
                                @php
                                    $totalQuantity += $item['quantity'] ?? 0;
                                    $totalDiscount += $item['discount'] ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $item['code'] ?? 'N/A' }}</td>
                                    <td>
                                        <strong>{{ $item['name'] ?? 'Article inconnu' }}</strong>
                                        @if(isset($item['available_stock']))
                                            <br><small class="text-muted">Stock disponible: {{ $item['available_stock'] }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item['unit'] ?? 'Unité' }}</td>
                                    <td class="text-center">{{ number_format($item['quantity'] ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($item['sale_price'] ?? 0, 0, ',', ' ') }} Fbu</td>
                                    <td class="text-end">{{ number_format($item['discount'] ?? 0, 0, ',', ' ') }} Fbu</td>
                                    <td class="text-end"><strong>{{ number_format($item['subtotal'] ?? 0, 0, ',', ' ') }} Fbu</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-muted">
                                        Aucun article trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">TOTAL</th>
                                <th class="text-center">{{ number_format($totalQuantity, 0, ',', ' ') }}</th>
                                <th class="text-end">-</th>
                                <th class="text-end">{{ number_format($totalDiscount, 0, ',', ' ') }} Fbu</th>
                                <th class="text-end">{{ number_format($proforma->total_amount, 0, ',', ' ') }} Fbu</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row">
                <div class="col-md-6">
                    <!-- Payment Information -->
                    <div class="p-3 border rounded bg-light">
                        <h6 class="mb-3">Informations de Paiement</h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1"><strong>Montant Total:</strong></p>
                                <p class="mb-1"><strong>Montant Payé:</strong></p>
                                <p class="mb-0"><strong>Reste à Payer:</strong></p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1">{{ number_format($proforma->total_amount, 0, ',', ' ') }} Fbu</p>
                                <p class="mb-1 text-success">{{ number_format($proforma->total_amount - $proforma->due_amount, 0, ',', ' ') }} Fbu</p>
                                <p class="mb-0 {{ $proforma->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($proforma->due_amount, 0, ',', ' ') }} Fbu
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Total Summary -->
                    <div class="p-3 border rounded bg-primary bg-opacity-10">
                        <h6 class="mb-3 text-primary">Résumé</h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1">Nombre d'articles:</p>
                                <p class="mb-1">Remise totale:</p>
                                <p class="mb-0"><strong>MONTANT TOTAL:</strong></p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1">{{ count($items) }}</p>
                                <p class="mb-1">{{ number_format($totalDiscount, 0, ',', ' ') }} Fbu</p>
                                <p class="mb-0"><strong class="fs-5 text-primary">{{ number_format($proforma->total_amount, 0, ',', ' ') }} Fbu</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="mt-4">
                <h6>Conditions générales:</h6>
                <ul class="small text-muted">
                    <li>Cette facture proforma est valable pendant 30 jours à compter de la date d'émission</li>
                    <li>Les prix sont exprimés en Francs Burundais (Fbu) et incluent toutes les taxes applicables</li>
                    <li>Le paiement doit être effectué avant la livraison des marchandises</li>
                    <li>Toute modification de commande après confirmation peut entraîner des frais supplémentaires</li>
                </ul>
            </div>

            <!-- Footer -->
            <div class="pt-4 mt-4 text-center border-top">
                <p class="mb-1"><strong>Merci pour votre confiance !</strong></p>
                <p class="mb-0 small text-muted">
                    Facture générée le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->name ?? 'Système' }}
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .btn, .d-flex.justify-content-between, .mb-4.row:first-child {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .container-fluid {
            padding: 0 !important;
        }

        body {
            background: white !important;
        }

        .table {
            page-break-inside: avoid;
        }

        .card-body {
            padding: 20px !important;
        }
    }

    .invoice-header {
        border-bottom: 3px solid var(--bs-primary);
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .table th {
        background-color: #f8f9fa !important;
        font-weight: 600;
    }

    .total-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
    function downloadPDF() {
        // Simple implementation - in a real app, you'd want to use a proper PDF generation library
        window.print();
    }

    // Auto-focus on print button for better UX
    document.addEventListener('DOMContentLoaded', function() {
        // Add some animation to the invoice on load
        const invoice = document.getElementById('invoice-content');
        invoice.style.opacity = '0';
        invoice.style.transform = 'translateY(20px)';

        setTimeout(() => {
            invoice.style.transition = 'all 0.5s ease';
            invoice.style.opacity = '1';
            invoice.style.transform = 'translateY(0)';
        }, 100);

        // Add click handlers for better interactivity
        const printBtn = document.querySelector('[onclick="window.print()"]');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                this.innerHTML = '<i class="bi bi-printer me-1"></i>Impression...';
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-printer me-1"></i>Imprimer';
                }, 2000);
            });
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }

        // Escape to go back
        if (e.key === 'Escape') {
            window.location.href = '{{ route("proformas.index") }}';
        }
    });
</script>
@endpush
@endsection
