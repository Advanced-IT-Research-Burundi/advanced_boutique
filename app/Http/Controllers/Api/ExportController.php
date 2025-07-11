<?php

namespace App\Http\Controllers\Api;

use App\Models\StockProduct;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    //
    public function exportExcel($token)
    {
        $data = session('excel_export_' . $token);

        if (!$data || now()->gt($data['expires_at'])) {
            abort(404);
        }

        session()->forget('excel_export_' . $token);

        // Stock ID
        $stockId = $data['stock_id'];

        $stockProducts = StockProduct::with(['product'])->where('stock_id', $stockId)->get();

        $data = [];
        // Add Header
        $data[] = [
            'product_code' => 'Code',
            'product_name' => 'Nom',
            'quantity' => 'Quantité',
            'price' => 'Prix',
            'total' => 'Total',
        ];
        foreach ($stockProducts as $stockProduct) {
            $data[] = [
                'product_code' => $stockProduct->product->code,
                'product_name' => $stockProduct->product->name,
                'quantity' => $stockProduct->quantity,
                'price' => $stockProduct->sale_price_ttc,
                'total' => $stockProduct->quantity * $stockProduct->sale_price_ttc,
            ];
        }

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            public $data;
            public function __construct($data)
            {
                $this->data = $data;
            }
            public function array(): array {
                return $this->data;
            }
        }, 'stock_' . $stockId . '.xlsx');
    }
}
