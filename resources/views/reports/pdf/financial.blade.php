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
        .positive { color: green; }
        .negative { color: red; }
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
            <h4>Revenus</h4>
            <p class="positive">{{ number_format($data['total_revenue'], 0, ',', ' ') }} FBU</p>
        </div>
        <div class="stat-card">
            <h4>Dépenses</h4>
            <p class="negative">{{ number_format($data['total_expenses'], 0, ',', ' ') }} FBU</p>
        </div>
        <div class="stat-card">
            <h4>Bénéfice Net</h4>
            <p class="{{ $data['net_profit'] >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($data['net_profit'], 0, ',', ' ') }} FBU
            </p>
        </div>
        <div class="stat-card">
            <h4>Marge Bénéficiaire</h4>
            <p>{{ $data['profit_margin'] }}%</p>
        </div>
    </div>

    <div class="section">
        <h3>Flux de Trésorerie</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>ENTRÉES</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Ventes</td>
                    <td class="text-right positive">{{ number_format($data['cash_flow']['sales'], 0, ',', ' ') }} FBU</td>
                </tr>
                <tr>
                    <td>Autres revenus</td>
                    <td class="text-right positive">{{ number_format($data['cash_flow']['other_income'], 0, ',', ' ') }} FBU</td>
                </tr>
                <tr>
                    <td><strong>SORTIES</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Achats</td>
                    <td class="text-right negative">{{ number_format($data['cash_flow']['purchases'], 0, ',', ' ') }} FBU</td>
                </tr>
                <tr>
                    <td>Dépenses</td>
                    <td class="text-right negative">{{ number_format($data['cash_flow']['expenses'], 0, ',', ' ') }} FBU</td>
                </tr>
                <tr>
                    <td><strong>FLUX NET</strong></td>
                    <td class="text-right {{ $data['cash_flow']['net_flow'] >= 0 ? 'positive' : 'negative' }}">
                        <strong>{{ number_format($data['cash_flow']['net_flow'], 0, ',', ' ') }} FBU</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
