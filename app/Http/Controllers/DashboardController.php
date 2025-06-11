<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Company, Agency, Sale, Purchase, Product, Client, Supplier, Stock, StockProduct, CashRegister, Expense};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $agency_id = $request->get('agency_id') ?? $user->current_agency_id;

        // Période par défaut : ce mois
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Statistiques principales
        $stats = $this->getMainStats($agency_id, $startDate, $endDate);

        // Graphiques et données
        $chartData = $this->getChartData($agency_id, $period);
        $topProducts = $this->getTopProducts($agency_id, $startDate, $endDate);
        $recentActivities = $this->getRecentActivities($agency_id);
        $lowStockProducts = $this->getLowStockProducts($agency_id);
        $cashRegisterStatus = $this->getCashRegisterStatus($agency_id);
        $salesTrend = $this->getSalesTrend($agency_id);

        // Agences pour le filtre
        $agencies = Agency::where('company_id', $user->company_id)->get();

        return view('dashboard', compact(
            'stats',
            'chartData',
            'topProducts',
            'recentActivities',
            'lowStockProducts',
            'cashRegisterStatus',
            'salesTrend',
            'agencies',
            'agency_id',
            'period'
        ));
    }

    private function getMainStats($agency_id, $startDate, $endDate)
    {
        $baseQuery = function($model) use ($agency_id) {
            return $agency_id ? $model->where('agency_id', $agency_id) : $model;
        };

        // Chiffre d'affaires
        $currentRevenue = $baseQuery(Sale::query())
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');

        $previousRevenue = $baseQuery(Sale::query())
            ->whereBetween('sale_date', [
                $startDate->copy()->subDays($endDate->diffInDays($startDate)),
                $startDate
            ])
            ->sum('total_amount');

        $revenueGrowth = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        // Ventes aujourd'hui
        $todaySales = $baseQuery(Sale::query())
            ->whereDate('sale_date', Carbon::today())
            ->sum('total_amount');

        $todaySalesCount = $baseQuery(Sale::query())
            ->whereDate('sale_date', Carbon::today())
            ->count();

        // Produits en stock
        $stockQuery = $baseQuery(StockProduct::query());
        if ($agency_id) {
            $stockQuery->whereHas('stock', function($q) use ($agency_id) {
                $q->where('agency_id', $agency_id);
            });
        }

        $totalProducts = $stockQuery->sum('quantity');
        $lowStockCount = $stockQuery->whereHas('product', function($q) {
            $q->whereRaw('stock_products.quantity <= products.alert_quantity');
        })->count();

        // Clients actifs (ayant fait au moins un achat ce mois)
        $activeClientsCount = $baseQuery(Sale::query())
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->distinct('client_id')
            ->count('client_id');

        $newClientsCount = $baseQuery(Client::query())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'revenue' => [
                'amount' => $currentRevenue,
                'growth' => round($revenueGrowth, 1),
                'is_positive' => $revenueGrowth >= 0
            ],
            'today_sales' => [
                'amount' => $todaySales,
                'count' => $todaySalesCount
            ],
            'products' => [
                'total' => $totalProducts,
                'low_stock' => $lowStockCount
            ],
            'clients' => [
                'active' => $activeClientsCount,
                'new' => $newClientsCount
            ]
        ];
    }

    private function getChartData($agency_id, $period)
    {
        $days = $period == '7' ? 7 : ($period == '30' ? 30 : 90);
        $labels = [];
        $salesData = [];
        $purchasesData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format($days <= 7 ? 'D' : 'j/n');

            $baseQuery = function($model) use ($agency_id) {
                return $agency_id ? $model->where('agency_id', $agency_id) : $model;
            };

            $dailySales = $baseQuery(Sale::query())
                ->whereDate('sale_date', $date)
                ->sum('total_amount');

            $dailyPurchases = $baseQuery(Purchase::query())
                ->whereDate('purchase_date', $date)
                ->sum('total_amount');

            $salesData[] = $dailySales;
            $purchasesData[] = $dailyPurchases;
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'purchases' => $purchasesData
        ];
    }

    private function getTopProducts($agency_id, $startDate, $endDate, $limit = 5)
    {
        $query = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit);

        if ($agency_id) {
            $query->where('sales.agency_id', $agency_id);
        }

        return $query->get();
    }

    private function getRecentActivities($agency_id, $limit = 10)
    {
        $activities = collect();

        // Récentes ventes
        $baseQuery = function($model) use ($agency_id) {
            return $agency_id ? $model->where('agency_id', $agency_id) : $model;
        };

        $recentSales = $baseQuery(Sale::with('client'))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($sale) {
                return [
                    'type' => 'sale',
                    'title' => 'Nouvelle vente',
                    'description' => "Vente #{$sale->id} - Client: {$sale->client->name}",
                    'amount' => $sale->total_amount,
                    'created_at' => $sale->created_at,
                    'badge_class' => 'bg-success',
                    'icon' => 'bi-cart-check'
                ];
            });

        // Récents achats
        $recentPurchases = $baseQuery(Purchase::with('supplier'))
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($purchase) {
                return [
                    'type' => 'purchase',
                    'title' => 'Réapprovisionnement',
                    'description' => "Achat #{$purchase->id} - Fournisseur: {$purchase->supplier->name}",
                    'amount' => $purchase->total_amount,
                    'created_at' => $purchase->created_at,
                    'badge_class' => 'bg-info',
                    'icon' => 'bi-box-arrow-in-down'
                ];
            });

        // Nouveaux clients
        $newClients = $baseQuery(Client::query())
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function($client) {
                return [
                    'type' => 'client',
                    'title' => 'Nouveau client',
                    'description' => "Inscription: {$client->name}",
                    'amount' => null,
                    'created_at' => $client->created_at,
                    'badge_class' => 'bg-primary',
                    'icon' => 'bi-person-plus'
                ];
            });

        return $activities
            ->merge($recentSales)
            ->merge($recentPurchases)
            ->merge($newClients)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();
    }

    private function getLowStockProducts($agency_id, $limit = 5)
    {
        $query = DB::table('stock_products')
            ->join('products', 'stock_products.product_id', '=', 'products.id')
            ->join('stocks', 'stock_products.stock_id', '=', 'stocks.id')
            ->whereRaw('stock_products.quantity <= products.alert_quantity')
            ->select(
                'products.name',
                'stock_products.quantity',
                'products.alert_quantity',
                'stocks.name as stock_name'
            )
            ->orderBy('stock_products.quantity', 'asc')
            ->limit($limit);

        if ($agency_id) {
            $query->where('stocks.agency_id', $agency_id);
        }

        return $query->get();
    }

    private function getCashRegisterStatus($agency_id)
    {
        $baseQuery = function($model) use ($agency_id) {
            return $agency_id ? $model->where('agency_id', $agency_id) : $model;
        };

        $activeCashRegister = $baseQuery(CashRegister::query())
            ->where('status', 'open')
            ->where('user_id', auth()->id())
            ->first();

        if (!$activeCashRegister) {
            return null;
        }

        // Recettes du jour
        $todayRevenue = $baseQuery(Sale::query())
            ->whereDate('sale_date', Carbon::today())
            ->sum('total_amount');

        // Dépenses du jour
        $todayExpenses = $baseQuery(Expense::query())
            ->whereDate('expense_date', Carbon::today())
            ->sum('amount');

        return [
            'opening_balance' => $activeCashRegister->opening_balance,
            'today_revenue' => $todayRevenue,
            'today_expenses' => $todayExpenses,
            'current_balance' => $activeCashRegister->opening_balance + $todayRevenue - $todayExpenses,
            'opened_at' => $activeCashRegister->opened_at
        ];
    }


    private function getSalesTrend($agency_id)
    {
        $baseQuery = function($model) use ($agency_id) {
            return $agency_id ? $model->where('agency_id', $agency_id) : $model;
        };

        $currentMonth = $baseQuery(Sale::query())
            ->whereMonth('sale_date', Carbon::now()->month)
            ->sum('total_amount');

        $previousMonth = $baseQuery(Sale::query())
            ->whereMonth('sale_date', Carbon::now()->subMonth()->month)
            ->sum('total_amount');

        $trend = $previousMonth > 0
            ? (($currentMonth - $previousMonth) / $previousMonth) * 100
            : 0;

        return [
            'current' => $currentMonth,
            'previous' => $previousMonth,
            'trend' => round($trend, 1),
            'is_positive' => $trend >= 0
        ];
    }

    public function updatePeriod(Request $request)
    {
        $request->validate([
            'period' => 'required|in:7,30,90',
            'agency_id' => 'nullable|exists:agencies,id'
        ]);

        return redirect()->route('dashboard', [
            'period' => $request->period,
            'agency_id' => $request->agency_id
        ]);
    }
}
