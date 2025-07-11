<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockProductExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $stockProducts;

    public function __construct($stockProducts)
    {
        $this->stockProducts = $stockProducts;
    }

    public function collection()
    {
        return collect($this->stockProducts);
    }

    public function headings(): array
    {
        return [
            'ID',
            'CODE',
            'Catégorie',
            'Nom du Produit',
            'Quantité',
            'Prix Unitaire',
            'Valeur Stock',
            'Date Création'
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->product->code ?? 'N/A',
            $product->product->category->name ?? 'N/A',
            $product->product->name ?? 'N/A',
            $product->quantity,
            $product->sale_price_ttc,
            $product->quantity * $product->sale_price_ttc,
            $product->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // En-têtes en gras
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 15, // CODE
            'C' => 20, // Catégorie
            'D' => 30, // Nom du Produit
            'E' => 12, // Quantité
            'F' => 15, // Prix Unitaire
            'G' => 15, // Valeur Stock
            'H' => 20, // Date
        ];
    }
}
