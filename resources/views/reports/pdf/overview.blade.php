<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .stat-card { border: 1px solid #ddd; padding: 15px; width: 23%; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Période: {{ $period }}</p>
        <p>Agence: {{ $agency }}</p>
        <p>Généré le: {{ $generated_at }}</p>
    </div>

    <div class="info">
        <h3>Résumé Exécutif</h3>
        <div class="stats">
            <div class="stat-card">
                <h4>Chiffre d'affaires</h4>
                <p>{{ number_format($data['total_sales'], 0, ',', ' ') }} FBU</p>
            </div>
            <div class="stat-card">
                <h4>Total Achats</h4>
                <p>{{ number_format($data['total_purchases'], 0, ',', ' ') }} FBU</p>
            </div>
            <div class="stat-card">
                <h4>Bénéfices</h4>
                <p>{{ number_format($data['profit'], 0, ',', ' ') }} FBU</p>
            </div>
            <div class="stat-card">
                <h4>Clients Actifs</h4>
                <p>{{ $data['active_customers'] }}</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Top 5 Produits</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité Vendue</th>
                    <th>Chiffre d'affaires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_products'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td class="text-right">{{ number_format($product->total_sales, 0, ',', ' ') }} FBU</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Alertes Stock</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Stock</th>
                    <th>Quantité</th>
                    <th>Seuil d'alerte</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['low_stock_alerts'] as $alert)
                <tr>
                    <td>{{ $alert['product_name'] }}</td>
                    <td>{{ $alert['stock_name'] }}</td>
                    <td>{{ $alert['quantity'] }}</td>
                    <td>{{ $alert['alert_threshold'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
