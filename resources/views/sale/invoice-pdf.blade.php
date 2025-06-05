<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-meta {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .company-logo {
            max-height: 60px;
            max-width: 150px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #e74c3c;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .invoice-dates {
            font-size: 11px;
            color: #666;
        }

        .client-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .client-info, .payment-status {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .client-info {
            margin-right: 15px;
        }

        .payment-status {
            margin-left: 15px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .client-name {
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .client-details {
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }

        .badge {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-warning {
            background: #ffc107;
            color: #212529;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .items-table th {
            background: #2c3e50;
            color: white;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .items-table .text-center {
            text-align: center;
        }

        .items-table .text-right {
            text-align: right;
        }

        .product-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .product-description {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        .totals-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .payment-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .invoice-totals {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 15px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 0;
            font-size: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .totals-table .total-row {
            background: #2c3e50;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .totals-table .total-row td {
            padding: 10px 0;
        }

        .amount-in-words {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-top: 15px;
            border-radius: 5px;
        }

        .amount-in-words h6 {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .amount-in-words p {
            font-size: 11px;
            color: #34495e;
            font-style: italic;
            font-weight: 500;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #3498db;
            display: table;
            width: 100%;
        }

        .notes {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .signature {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .signature-line {
            margin-top: 40px;
            width: 150px;
            margin-left: auto;
        }

        .signature-line hr {
            border: none;
            border-top: 2px solid #2c3e50;
            margin-bottom: 5px;
        }

        .signature-text {
            font-size: 10px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                @if(isset($company) && $company->tp_logo)
                    <img src="{{ public_path('storage/' . $company->tp_logo) }}" alt="Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $company->tp_name ?? 'Nom de l\'entreprise' }}</div>
                <div class="company-details">
                    @if($company->tp_address ?? false)
                        <div>{{ $company->tp_address }}</div>
                    @endif
                    @if($company->tp_phone_number ?? false)
                        <div>Tél: {{ $company->tp_phone_number }}</div>
                    @endif
                    @if($company->tp_email ?? false)
                        <div>Email: {{ $company->tp_email }}</div>
                    @endif
                    @if($company->tp_TIN ?? false)
                        <div><strong>NIF:</strong> {{ $company->tp_TIN }}</div>
                    @endif
                </div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-title">FACTURE</div>
                <div class="invoice-number"># {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-dates">
                    <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</div>
                    <div><strong>Heure:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Informations client et statut -->
        <div class="client-section">
            <div class="client-info">
                <div class="section-title">Facturé à:</div>
                <div class="client-name">
                    @if($sale->client->patient_type === 'morale')
                        {{ $sale->client->societe }}
                    @else
                        {{ $sale->client->name }} {{ $sale->client->first_name }} {{ $sale->client->last_name }}
                    @endif
                </div>
                <div class="client-details">
                    @if($sale->client->nif)
                        <div><strong>NIF:</strong> {{ $sale->client->nif }}</div>
                    @endif
                    @if($sale->client->phone)
                        <div>Tél: {{ $sale->client->phone }}</div>
                    @endif
                    @if($sale->client->email)
                        <div>Email: {{ $sale->client->email }}</div>
                    @endif
                    @if($sale->client->address)
                        <div>Adresse: {{ $sale->client->address }}</div>
                    @endif
                </div>
            </div>
            <div class="payment-status">
                <div class="section-title">Statut de paiement:</div>
                <div>
                    <strong>Statut:</strong>
                    @if($sale->due_amount <= 0)
                        <span class="badge">Payée</span>
                    @else
                        <span class="badge badge-warning">Partiellement payée</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tableau des articles -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 40%">Article</th>
                    <th style="width: 10%" class="text-center">Qté</th>
                    <th style="width: 15%" class="text-right">Prix unitaire</th>
                    <th style="width: 10%" class="text-center">Remise</th>
                    <th style="width: 20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product->name }}</div>
                        @if($item->product->description)
                            <div class="product-description">{{ $item->product->description }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format($item->sale_price, 0) }} BIF</td>
                    <td class="text-center">
                        @if($item->discount > 0)
                            {{ number_format($item->discount, 0) }} BIF
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right"><strong>{{ number_format($item->subtotal, 0) }} BIF</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totaux et informations de paiement -->
        <div class="totals-section">
            <div class="payment-info">
                <h6>Informations de paiement:</h6>
                <div style="font-size: 11px; margin-bottom: 10px;">
                    <div><strong>Montant payé:</strong> {{ number_format($sale->paid_amount, 0) }} BIF</div>
                    @if($sale->due_amount > 0)
                        <div style="color: #e74c3c;"><strong>Montant dû:</strong> {{ number_format($sale->due_amount, 0) }} BIF</div>
                    @endif
                </div>

                <div class="amount-in-words">
                    <h6>Montant payé en lettres:</h6>
                    <p>{{ getNumberToWord($sale->paid_amount) }} francs burundais</p>
                </div>
            </div>

            <div class="invoice-totals">
                <table class="totals-table">
                    <tr>
                        <td style="text-align: right;"><strong>Sous-total:</strong></td>
                        <td style="text-align: right;">{{ number_format($sale->saleItems->sum('subtotal'), 0) }} BIF</td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><strong>Remise totale:</strong></td>
                        <td style="text-align: right;">{{ number_format($sale->saleItems->sum('discount'), 0) }} BIF</td>
                    </tr>
                    <tr class="total-row">
                        <td style="text-align: right;"><strong>TOTAL:</strong></td>
                        <td style="text-align: right;"><strong>{{ number_format($sale->total_amount, 0) }} BIF</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="notes">
                <h6 style="font-size: 12px; margin-bottom: 8px;">Notes:</h6>
                <p style="font-size: 11px; color: #666;">Merci pour votre confiance.</p>
            </div>
            <div class="signature">
                <div class="signature-line">
                    <hr>
                    <div class="signature-text">Signature autorisée</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
