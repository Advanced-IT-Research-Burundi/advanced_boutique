<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des Produits</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 15px;
        }

        .header {
            position: relative;
        }

        .header img {
            width: 80%;
            height: 200px;
        }

        .date-section {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 9pt;
            font-weight: bold;
        }

        .title {
            text-align: center;
            font-size: 11pt;
            margin-top: -10px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        th {
            background-color: #e8e8e8;
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
        }

        td {
            border: 1px solid #999;
            padding: 4px;
            font-size: 8pt;
        }

        .category-row {
            background-color: #d0d0d0;
            font-weight: bold;
            text-align: left;
        }

        .striped {
            background-color: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }
        /* .header-img {
            width: 80%;
            height: 300px;
        } */
    </style>
</head>
<body>
    <div class="header">
        <img class="header-img" src="{{ public_path('images/ubwiza.png') }}" alt="Header UBB">
        <div class="date-section">{{ date('d/m/Y') }}</div>
    </div>

    <div class="title">LISTE DES PRODUITS</div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Code article</th>
                <th style="width: 50%;">Nom article</th>
                <th style="width: 17.5%;" class="text-right">PVHT</th>
                <th style="width: 17.5%;" class="text-right">PVTTC</th>
            </tr>
        </thead>
        <tbody>
            @php $rowCount = 0; @endphp
            @foreach($products as $categoryId => $categoryProducts)
                @php
                    $category = $categoryProducts->first()->category;
                @endphp
                <tr>
                    <td colspan="4" class="category-row">
                        {{ $category ? $category->name : 'Sans catégorie' }}
                    </td>
                </tr>
                @foreach($categoryProducts as $product)
                    @php $rowCount++; @endphp
                    <tr class="{{ $rowCount % 2 == 0 ? 'striped' : '' }}">
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td class="text-right">{{ number_format($product->sale_price_ht, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($product->sale_price_ttc, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
