<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture Proforma N° {{ $purchase->id }}/{{ date('Y') }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            padding: 20px 0;
        }

        .logo-section {
            width: 120px;
            margin-right: 20px;
        }

        .logo {
            width: 100px;
            height: 100px;
            border: 2px solid #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 14px;
        }

        .company-info {
            flex: 1;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }

        .company-type {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 10px;
            line-height: 1.3;
            color: #666;
        }

        .client-section {
            margin: 20px 0;
        }

        .facture-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 30px 0;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .products-table th,
        .products-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .products-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .products-table .text-center {
            text-align: center;
        }

        .products-table .text-right {
            text-align: right;
        }

        .totals-section {
            margin-top: 20px;
        }

        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #333;
            padding: 8px;
        }

        .totals-table .label {
            font-weight: bold;
            width: 150px;
        }

        .totals-table .amount {
            text-align: right;
            width: 150px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .total-quantity {
            text-align: center;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <div class="logo">
                U.B.B
            </div>
        </div>
        <div class="company-info">
            <div class="company-name">UBWIZA BURUNDI BUSINESS SPRL "U.B.B"</div>
            <div class="company-type">COMMERCE GÉNÉRAL IMPORT -EXPORT</div>
            <div class="company-details">
                R.C : {{ config('app.company.rc', 'XXXXXXXXX') }}<br>
                N.I.F : {{ config('app.company.nif', 'XXXXXXXXX') }}<br>
                Tél : {{ config('app.company.phone', '+257 XX XX XX XX') }}<br>
                E-mail : {{ config('app.company.email', 'contact@ubb.bi') }}
            </div>
        </div>
    </div>

    <div class="client-section">
        <strong>Client : {{ $purchase->supplier->name ?? 'SOBIMA' }}</strong>
    </div>

    <div class="facture-title">
        FACTURE PROFORMA N° {{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}/{{ date('Y') }} du {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 50%;">Nature de l'article ou service</th>
                <th style="width: 15%;">Quantité</th>
                <th style="width: 15%;">PU</th>
                <th style="width: 20%;">PV-HTVA</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQuantity = 0;
                $subtotalHT = 0;
            @endphp
            @foreach($purchase->purchaseItems as $item)
                @php
                    $totalQuantity += $item->quantity;
                    $subtotalHT += $item->subtotal;
                @endphp
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->purchase_price, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td class="total-quantity">{{ number_format($totalQuantity, 2, ',', ' ') }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="totals-section">
        <table class="totals-table">
            @php
                $tva = $subtotalHT * 0.18; // TVA 18%
                $totalTTC = $subtotalHT + $tva;
            @endphp
            <tr>
                <td class="label">P.V H.TVA :</td>
                <td class="amount">{{ number_format($subtotalHT, 0, ',', ' ') }} FBU</td>
            </tr>
            <tr>
                <td class="label">TVA :</td>
                <td class="amount">{{ number_format($tva, 0, ',', ' ') }} FBU</td>
            </tr>
            <tr style="border-top: 2px solid #333;">
                <td class="label">Total TVAC :</td>
                <td class="amount"><strong>{{ number_format($totalTTC, 0, ',', ' ') }} FBU</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p><strong>Fait à Bujumbura le {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</strong></p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
