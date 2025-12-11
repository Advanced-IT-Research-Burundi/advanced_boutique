<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Company, Agency, Sale, Purchase, Product, Client, Supplier, Stock, StockProduct, CashRegister, Expense};
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Génère les données de rapport selon le type demandé
     */
    public function index(Request $request)
    {

        try {
            $reportType = $request->get('report_type', 'overview');
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfDay());
            $agencyId = $request->get('agency_id');

            // Validation des dates
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            $data = [];

            switch ($reportType) {
                case 'overview':
                    $data = $this->getOverviewData($startDate, $endDate, $agencyId);
                    break;
                case 'sales':
                    $data = $this->getSalesData($startDate, $endDate, $agencyId);
                    break;
                case 'purchases':
                    $data = $this->getPurchasesData($startDate, $endDate, $agencyId);
                    break;
                case 'inventory':
                    $data = $this->getInventoryData($agencyId);
                    break;
                case 'financial':
                    $data = $this->getFinancialData($startDate, $endDate, $agencyId);
                    break;
                case 'customers':
                    $data = $this->getCustomersData($startDate, $endDate, $agencyId);
                    break;
                case 'suppliers':
                    $data = $this->getSuppliersData($startDate, $endDate, $agencyId);
                    break;
                default:
                    return sendError('Type de rapport non supporté', 400);
            }

            return sendResponse($data, 'Données de rapport générées avec succès');
        } catch (\Exception $e) {
            return $e;
            return sendError('Erreur lors de la génération du rapport: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Exporte le rapport en PDF
     */
    public function export(Request $request)
    {
        try {
            $reportType = $request->get('report_type', 'overview');
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfDay());
            $agencyId = $request->get('agency_id');

            // Validation des dates
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            // Récupération des données
            $data = [];
            switch ($reportType) {
                case 'overview':
                    $data = $this->getOverviewData($startDate, $endDate, $agencyId);
                    break;
                case 'sales':
                    $data = $this->getSalesData($startDate, $endDate, $agencyId);
                    break;
                case 'financial':
                    $data = $this->getFinancialData($startDate, $endDate, $agencyId);
                    break;
                // Ajouter d'autres types selon les besoins
            }

            // Données pour le PDF
            $pdfData = [
                'title' => $this->getReportTitle($reportType),
                'period' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
                'agency' => $agencyId ? Agency::find($agencyId)->name : 'Toutes les agences',
                'data' => $data,
                'report_type' => $reportType,
                'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
            ];

            // Génération du PDF
            $pdf = PDF::loadView('reports.pdf.' . $reportType, $pdfData);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'rapport_' . $reportType . '_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return sendError('Erreur lors de l\'export PDF: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Données de vue d'ensemble
     */
    private function getOverviewData($startDate, $endDate, $agencyId = null)
    {
        $salesQuery = Sale::whereBetween('sale_date', [$startDate, $endDate]);
        $purchasesQuery = Purchase::whereBetween('purchase_date', [$startDate, $endDate]);
        $expensesQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        if ($agencyId) {
            $salesQuery->where('agency_id', $agencyId);
            $purchasesQuery->where('agency_id', $agencyId);
            $expensesQuery->where('agency_id', $agencyId);
        }

        $totalSales = $salesQuery->sum('total_amount');
        $totalPurchases = $purchasesQuery->sum('total_amount');
        $totalExpenses = $expensesQuery->sum('amount');

        $salesCount = $salesQuery->count();
        $purchasesCount = $purchasesQuery->count();

        // Clients actifs (qui ont fait des achats dans la période)
        $activeCustomers = $salesQuery->distinct('client_id')->count();

        // Top 5 produits les plus vendus
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->when($agencyId, function ($query) use ($agencyId) {
                return $query->where('sales.agency_id', $agencyId);
            })
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as quantity'),
                DB::raw('SUM(sale_items.sale_price) as total_sales')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        // Alertes de stock bas
        $lowStockAlerts = StockProduct::with(['product', 'stock'])
            ->whereHas('product', function ($query) use ($agencyId) {
                if ($agencyId) {
                    $query->where('agency_id', $agencyId);
                }
            })
            // ->whereRaw('quantity <= alert_quantity')
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($stockProduct) {
                return [
                    'product_name' => $stockProduct?->product?->name ?? $stockProduct->product_name,
                    'stock_name' => $stockProduct?->stock?->name ?? $stockProduct->stock_id . ' inconnu',
                    'quantity' => $stockProduct?->quantity ?? 0,
                    'alert_threshold' => $stockProduct?->alert_quantity ?? 0
                ];
            });

        return [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'total_expenses' => $totalExpenses,
            'profit' => $totalSales - $totalPurchases - $totalExpenses,
            'sales_count' => $salesCount,
            'purchases_count' => $purchasesCount,
            'active_customers' => $activeCustomers,
            'top_products' => $topProducts,
            'low_stock_alerts' => $lowStockAlerts
        ];
    }

    /**
     * Données de rapport des ventes
     */
    private function getSalesData($startDate, $endDate, $agencyId = null)
    {
        $salesQuery = Sale::with(['client'])->whereBetween('sale_date', [$startDate, $endDate]);

        if ($agencyId) {
            $salesQuery->where('agency_id', $agencyId);
        }

        $sales = $salesQuery->get();
        $totalAmount = $sales->sum('total_amount');
        $totalCount = $sales->count();
        $averageAmount = $totalCount > 0 ? $totalAmount / $totalCount : 0;
        $paidSales = $sales->where('due_amount', 0)->count();

        $salesDetails = $sales->map(function ($sale) {
            return [
                'sale_date' => $sale->sale_date,
                'client_name' => $sale->client->name ?? 'Client supprimé',
                'total_amount' => $sale->total_amount,
                'paid_amount' => $sale->paid_amount,
                'due_amount' => $sale->due_amount,
                'paid' => $sale->due_amount == 0
            ];
        });

        return [
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'average_amount' => $averageAmount,
            'paid_sales' => $paidSales,
            'unpaid_sales' => $totalCount - $paidSales,
            'sales_details' => $salesDetails
        ];
    }

    /**
     * Données de rapport des achats
     */
    private function getPurchasesData($startDate, $endDate, $agencyId = null)
    {
        $purchasesQuery = Purchase::with(['supplier'])->whereBetween('purchase_date', [$startDate, $endDate]);

        if ($agencyId) {
            $purchasesQuery->where('agency_id', $agencyId);
        }

        $purchases = $purchasesQuery->get();
        $totalAmount = $purchases->sum('total_amount');
        $totalCount = $purchases->count();
        $averageAmount = $totalCount > 0 ? $totalAmount / $totalCount : 0;
        $paidPurchases = $purchases->where('due_amount', 0)->count();

        $purchasesDetails = $purchases->map(function ($purchase) {
            return [
                'purchase_date' => $purchase->purchase_date,
                'supplier_name' => $purchase->supplier->name ?? 'Fournisseur supprimé',
                'total_amount' => $purchase->total_amount,
                'paid_amount' => $purchase->paid_amount,
                'due_amount' => $purchase->due_amount,
                'paid' => $purchase->due_amount == 0
            ];
        });

        return [
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'average_amount' => $averageAmount,
            'paid_purchases' => $paidPurchases,
            'unpaid_purchases' => $totalCount - $paidPurchases,
            'purchases_details' => $purchasesDetails
        ];
    }

    /**
     * Données de rapport d'inventaire
     */
    private function getInventoryData($agencyId = null)
    {
        $stockProductsQuery = StockProduct::with(['product', 'stock']);

        if ($agencyId) {
            $stockProductsQuery->whereHas('product', function ($query) use ($agencyId) {
                $query->where('agency_id', $agencyId);
            });
        }

        $stockProducts = $stockProductsQuery->get();

        $totalProducts = $stockProducts->count();
        $totalValue = $stockProducts->sum(function ($sp) {
            return $sp->quantity * $sp->product->purchase_price;
        });
        $lowStockItems = $stockProducts->where('quantity', '<=', 'alert_quantity')->count();

        // Produits par catégorie
        $productsByCategory = $stockProducts->groupBy('product.category.name')
            ->map(function ($items, $category) {
                return [
                    'category' => $category ?? 'Non catégorisé',
                    'count' => $items->count(),
                    'total_quantity' => $items->sum('quantity'),
                    'total_value' => $items->sum(function ($sp) {
                        return $sp->quantity * $sp->product->purchase_price;
                    })
                ];
            })->values();

        // Stocks par location
        $stocksByLocation = $stockProducts->groupBy('stock.name')
            ->map(function ($items, $stockName) {
                return [
                    'stock_name' => $stockName,
                    'products_count' => $items->count(),
                    'total_quantity' => $items->sum('quantity'),
                    'total_value' => $items->sum(function ($sp) {
                        return $sp->quantity * $sp->product->purchase_price;
                    })
                ];
            })->values();

        return [
            'total_products' => $totalProducts,
            'total_value' => $totalValue,
            'low_stock_items' => $lowStockItems,
            'products_by_category' => $productsByCategory,
            'stocks_by_location' => $stocksByLocation,
            'stock_details' => $stockProducts->map(function ($sp) {
                return [
                    'product_name' => $sp->product->name,
                    'stock_name' => $sp->stock->name,
                    'quantity' => $sp->quantity,
                    'alert_quantity' => $sp->alert_quantity,
                    'unit_price' => $sp->product->purchase_price,
                    'total_value' => $sp->quantity * $sp->product->purchase_price
                ];
            })
        ];
    }

    /**
     * Données de rapport financier
     */
    private function getFinancialData($startDate, $endDate, $agencyId = null)
    {
        $salesQuery = Sale::whereBetween('sale_date', [$startDate, $endDate]);
        $purchasesQuery = Purchase::whereBetween('purchase_date', [$startDate, $endDate]);
        $expensesQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        if ($agencyId) {
            $salesQuery->where('agency_id', $agencyId);
            $purchasesQuery->where('agency_id', $agencyId);
            $expensesQuery->where('agency_id', $agencyId);
        }

        $totalRevenue = $salesQuery->sum('total_amount');
        $totalPurchases = $purchasesQuery->sum('total_amount');
        $totalExpenses = $expensesQuery->sum('amount');
        $netProfit = $totalRevenue - $totalPurchases - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

        // Flux de trésorerie
        $cashFlow = [
            'sales' => $totalRevenue,
            'other_income' => 0, // À adapter selon vos besoins
            'purchases' => $totalPurchases,
            'expenses' => $totalExpenses,
            'net_flow' => $totalRevenue - $totalPurchases - $totalExpenses
        ];

        // Évolution mensuelle
        $monthlyData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $monthRevenue = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
                ->when($agencyId, function ($query) use ($agencyId) {
                    return $query->where('agency_id', $agencyId);
                })
                ->sum('total_amount');

            $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->when($agencyId, function ($query) use ($agencyId) {
                    return $query->where('agency_id', $agencyId);
                })
                ->sum('amount');

            $monthlyData[] = [
                'month' => $monthStart->format('M Y'),
                'revenue' => $monthRevenue,
                'expenses' => $monthExpenses,
                'profit' => $monthRevenue - $monthExpenses
            ];

            $currentDate->addMonth();
        }

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'cash_flow' => $cashFlow,
            'monthly_data' => $monthlyData
        ];
    }

    /**
     * Données de rapport clients
     */
    private function getCustomersData($startDate, $endDate, $agencyId = null)
    {
        $clientsQuery = Client::with(['sales' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        }]);

        if ($agencyId) {
            $clientsQuery->where('agency_id', $agencyId);
        }

        $clients = $clientsQuery->get();
        $totalClients = $clients->count();
        $activeClients = $clients->filter(function ($client) {
            return $client->sales->count() > 0;
        })->count();

        // Top clients
        $topClients = $clients->map(function ($client) {
            $totalSales = $client->sales->sum('total_amount');
            $salesCount = $client->sales->count();

            return [
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'total_sales' => $totalSales,
                'sales_count' => $salesCount,
                'average_sale' => $salesCount > 0 ? $totalSales / $salesCount : 0
            ];
        })->sortByDesc('total_sales')->take(10)->values();

        return [
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'inactive_clients' => $totalClients - $activeClients,
            'top_clients' => $topClients,
            'clients_details' => $clients->map(function ($client) {
                return [
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'patient_type' => $client->patient_type,
                    'total_sales' => $client->sales->sum('total_amount'),
                    'sales_count' => $client->sales->count(),
                    'last_sale' => $client->sales->max('sale_date')
                ];
            })
        ];
    }

    /**
     * Données de rapport fournisseurs
     */
    private function getSuppliersData($startDate, $endDate, $agencyId = null)
    {
        $suppliersQuery = Supplier::with(['purchases' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('purchase_date', [$startDate, $endDate]);
        }]);

        if ($agencyId) {
            $suppliersQuery->where('agency_id', $agencyId);
        }

        $suppliers = $suppliersQuery->get();
        $totalSuppliers = $suppliers->count();
        $activeSuppliers = $suppliers->filter(function ($supplier) {
            return $supplier->purchases->count() > 0;
        })->count();

        // Top fournisseurs
        $topSuppliers = $suppliers->map(function ($supplier) {
            $totalPurchases = $supplier->purchases->sum('total_amount');
            $purchasesCount = $supplier->purchases->count();

            return [
                'name' => $supplier->name,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'total_purchases' => $totalPurchases,
                'purchases_count' => $purchasesCount,
                'average_purchase' => $purchasesCount > 0 ? $totalPurchases / $purchasesCount : 0
            ];
        })->sortByDesc('total_purchases')->take(10)->values();

        return [
            'total_suppliers' => $totalSuppliers,
            'active_suppliers' => $activeSuppliers,
            'inactive_suppliers' => $totalSuppliers - $activeSuppliers,
            'top_suppliers' => $topSuppliers,
            'suppliers_details' => $suppliers->map(function ($supplier) {
                return [
                    'name' => $supplier->name,
                    'email' => $supplier->email,
                    'phone' => $supplier->phone,
                    'total_purchases' => $supplier->purchases->sum('total_amount'),
                    'purchases_count' => $supplier->purchases->count(),
                    'last_purchase' => $supplier->purchases->max('purchase_date')
                ];
            })
        ];
    }

    /**
     * Retourne le titre du rapport selon le type
     */
    private function getReportTitle($reportType)
    {
        $titles = [
            'overview' => 'Rapport de Vue d\'ensemble',
            'sales' => 'Rapport des Ventes',
            'purchases' => 'Rapport des Achats',
            'inventory' => 'Rapport d\'Inventaire',
            'financial' => 'Rapport Financier',
            'customers' => 'Rapport des Clients',
            'suppliers' => 'Rapport des Fournisseurs'
        ];

        return $titles[$reportType] ?? 'Rapport';
    }
}
