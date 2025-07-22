<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockProductsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $stockProducts;
    protected $stock;

    public function __construct($stockProducts, $stock)
    {
        $this->stockProducts = $stockProducts;
        $this->stock = $stock;
    }

    public function collection()
    {
        return $this->stockProducts;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code Produit',
            'Nom du Produit',
            'Catégorie',
            'Quantité',
            'Prix Unitaire',
            'Valeur Stock',
            'Date Création'
        ];
    }

    public function map($stockProduct): array
    {
        return [
            $stockProduct->id,
            $stockProduct->product->code ?? '',
            $stockProduct->product->name ?? '',
            $stockProduct->category->name ?? '',
            $stockProduct->quantity,
            number_format($stockProduct->sale_price_ttc, 2),
            number_format($stockProduct->quantity * $stockProduct->sale_price_ttc, 2),
            $stockProduct->created_at->format('d/m/Y H:i:s')
        ];
    }

    public function title(): string
    {
        return 'Stock ' . $this->stock->name;
    }
}
