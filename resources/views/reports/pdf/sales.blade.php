<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .stat-card { border: 1px solid #ddd; padding: 15px; width: 23%; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Période: {{ $period }}</p>
        <p>Agence: {{ $agency }}</p>
        <p>Généré le: {{ $generated_at }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h4>Total Ventes</h4>
            <p>{{ number_format($data['total_amount'], 0, ',', ' ') }} FBU</p>
        </div>
        <div class="stat-card">
            <h4>Nombre Ventes</h4>
            <p>{{ $data['total_count'] }}</p>
        </div>
        <div class="stat-card">
            <h4>Montant Moyen</h4>
            <p>{{ number_format($data['average_amount'], 0, ',', ' ') }} FBU</p>
        </div>
        <div class="stat-card">
            <h4>Ventes Payées</h4>
            <p>{{ $data['paid_sales'] }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Détail des Ventes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Montant Total</th>
                    <th>Montant Payé</th>
                    <th>Reste à Payer</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['sales_details'] as $sale)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($sale['sale_date'])->format('d/m/Y') }}</td>
                    <td>{{ $sale['client_name'] }}</td>
                    <td class="text-right">{{ number_format($sale['total_amount'], 0, ',', ' ') }} FBU</td>
                    <td class="text-right">{{ number_format($sale['paid_amount'], 0, ',', ' ') }} FBU</td>
                    <td class="text-right">{{ number_format($sale['due_amount'], 0, ',', ' ') }} FBU</td>
                    <td>{{ $sale['paid'] ? 'Payé' : 'En attente' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
