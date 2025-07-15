<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Products Report</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            background: white;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #2c3e50;
        }

        .header .date {
            font-size: 8px;
            color: #666;
            font-style: italic;
        }

        .table-container {
            width: 100%;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background: white;
        }

        th {
            background: #34495e;
            color: white;
            padding: 6px 3px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #2c3e50;
            font-size: 12px;
            line-height: 1.1;
        }

        td {
            padding: 4px 3px;
            border: 1px solid #bdc3c7;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            line-height: 1.1;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e8f4f8;
        }

        /* Largeurs des colonnes optimisées */
        .col-id { width: 5%; }
        .col-code { width: 10%; }
        .col-category { width: 12%; }
        .col-name { width: 25%; }
        .col-qty { width: 8%; }
        .col-price { width: 12%; }
        .col-value { width: 12%; }
        .col-date { width: 16%; }

        /* Alignements spécifiques */
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }

        /* Styles pour les montants */
        .amount {
            font-weight: bold;
            color: #27ae60;
        }

        .product-name {
            text-align: left;
            font-weight: 500;
        }

        .category {
            font-size: 6px;
            color: #666;
            font-style: italic;
        }

        .date-cell {
            font-size: 6px;
            color: #555;
        }

        /* Footer */
        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #bdc3c7;
            font-size: 12px;
            color: #666;
            text-align: center;
        }

        /* Totaux */
        .totals-row {
            background-color: #ecf0f1 !important;
            font-weight: bold;
            border-top: 2px solid #34495e;
        }

        .totals-row td {
            background-color: #ecf0f1;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Responsive pour petites colonnes */
        @media print {
            body { font-size: 12px; }
            th, td { font-size: 12px; padding: 2px 1px; }
            .header h1 { font-size: 14px; }
        }

        /* Éviter les coupures de page */
        tr {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Stock Products</h1>
        <div class="date">Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-code">CODE</th>
                    <th class="col-category">Catégorie</th>
                    <th class="col-name">Nom du Produit</th>
                    <th class="col-qty">Qté</th>
                    <th class="col-price">Prix Unit.</th>
                    <th class="col-value">Val. Stock</th>
                    <th class="col-date">Date Création</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQuantity = 0;
                    $totalValue = 0;
                @endphp

                @foreach($stockProducts as $product)
                @php
                    $stockValue = $product->quantity * $product->sale_price_ttc;
                    $totalQuantity += $product->quantity;
                    $totalValue += $stockValue;
                @endphp
                <tr>
                    <td class="text-center">{{ $product->id }}</td>
                    <td class="text-center">{{ $product->product->code ?? 'N/A' }}</td>
                    <td class="text-center">{{ $product->product->category->name ?? 'N/A' }}</td>
                    <td class="product-name">{{ $product->product->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($product->quantity, 0, ',', ' ') }}</td>
                    <td class="text-right amount">{{ number_format($product->sale_price_ttc, 0, ',', ' ') }} €</td>
                    <td class="text-right amount">{{ number_format($stockValue, 0, ',', ' ') }} BIF</td>
                    <td class="text-center date-cell">{{ \Carbon\Carbon::parse($product->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="4" class="text-right"><strong>TOTAUX:</strong></td>
                    <td class="text-center"><strong>{{ number_format($totalQuantity, 0, ',', ' ') }}</strong></td>
                    <td class="text-center">-</td>
                    <td class="text-right"><strong>{{ number_format($totalValue, 0, ',', ' ') }} BIF</strong></td>
                    <td class="text-center">-</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        <p>Nombre total d'articles: {{ count($stockProducts) }} | Valeur totale du stock: {{ number_format($totalValue, 2, ',', ' ') }} BIF</p>
        <p>Rapport généré automatiquement - {{ date('d/m/Y à H:i:s') }}</p>
    </div>
</body>
</html>
