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

        if ( !auth()->check() || !auth()->user()->isAdmin()) {
            return sendError('Accès interdit', 403);
        }

        try {
            $user = auth()->user();
            $agency_id = $request->get('agency_id') ?? $user->agency_id;
            $period = $request->get('period', '30');

            // Validation des paramètres
            if (!in_array($period, ['7', '30', '90'])) {
                return sendError('Période invalide. Utilisez 7, 30 ou 90 jours.', 400);
            }

            $startDate = Carbon::now()->subDays($period);
            $endDate = Carbon::now();

            // Collecte des données
            $data = [
                'stats' => $this->getMainStats($agency_id, $startDate, $endDate),
                'chartData' => $this->getChartData($agency_id, $period),
                'topProducts' => $this->getTopProducts($agency_id, $startDate, $endDate),
                'recentActivities' => $this->getRecentActivities($agency_id),
                'lowStockProducts' => $this->getLowStockProducts($agency_id),
                'cashRegisterStatus' => $this->getCashRegisterStatus($agency_id),
                'salesTrend' => $this->getSalesTrend($agency_id),
                'agencies' => $this->getAgencies($user->company_id),
                'currentPeriod' => $period,
                'currentAgencyId' => $agency_id
            ];

            return sendResponse($data,'Dashboard chargé avec succès');

        } catch (\Throwable $e) {
            return sendError('Erreur lors du chargement du dashboard', 500, ['error' => $e->getMessage()]);
        }
    }

    private function getMainStats($agency_id, $startDate, $endDate)
    {
        try {
            // Chiffre d'affaires actuel
            $currentRevenue = $this->scopeByAgency(Sale::query(), $agency_id)
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->sum('total_amount');

            // Chiffre d'affaires précédent
            $previousPeriodStart = $startDate->copy()->subDays($endDate->diffInDays($startDate));
            $previousRevenue = $this->scopeByAgency(Sale::query(), $agency_id)
                ->whereBetween('sale_date', [$previousPeriodStart, $startDate])
                ->sum('total_amount');

            $revenueGrowth = $previousRevenue > 0
                ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
                : 0;

            // Ventes aujourd'hui
            $todayData = $this->getTodayStats($agency_id);

            // Statistiques produits
            $productsStats = $this->getProductsStats($agency_id);

            // Statistiques clients
            $clientsStats = $this->getClientsStats($agency_id, $startDate, $endDate);

            return [
                'revenue' => [
                    'amount' => (float) $currentRevenue,
                    'growth' => round($revenueGrowth, 1),
                    'is_positive' => $revenueGrowth >= 0
                ],
                'today_sales' => $todayData,
                'products' => $productsStats,
                'clients' => $clientsStats
            ];

        } catch (\Exception $e) {
            throw new \Exception('Erreur lors du calcul des statistiques: ' . $e->getMessage());
        }
    }

    private function getTodayStats($agency_id)
    {
        $todayQuery = $this->scopeByAgency(Sale::query(), $agency_id)
            ->whereDate('sale_date', Carbon::today());

        return [
            'amount' => (float) $todayQuery->sum('total_amount'),
            'count' => $todayQuery->count()
        ];
    }

    private function getProductsStats($agency_id)
    {
        $stockQuery = StockProduct::query();

        if ($agency_id) {
            $stockQuery->whereHas('stock', function($q) use ($agency_id) {
                $q->where('agency_id', $agency_id);
            });
        }

        $totalProducts = $stockQuery->sum('quantity');
        $lowStockCount = $stockQuery->whereHas('product', function($q) {
            $q->whereRaw('stock_products.quantity <= products.alert_quantity');
        })->count();

        return [
            'total' => (int) $totalProducts,
            'low_stock' => (int) $lowStockCount
        ];
    }

    private function getClientsStats($agency_id, $startDate, $endDate)
    {
        $activeClientsCount = $this->scopeByAgency(Sale::query(), $agency_id)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->distinct('client_id')
            ->count('client_id');

        $newClientsCount = $this->scopeByAgency(Client::query(), $agency_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'active' => (int) $activeClientsCount,
            'new' => (int) $newClientsCount
        ];
    }

    private function getChartData($agency_id, $period)
    {
        $days = match($period) {
            '7' => 7,
            '30' => 30,
            '90' => 90,
            default => 30
        };

        $labels = [];
        $salesData = [];
        $purchasesData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format($days <= 7 ? 'D' : 'j/n');

            $dailySales = $this->scopeByAgency(Sale::query(), $agency_id)
                ->whereDate('sale_date', $date)
                ->sum('total_amount');

            $dailyPurchases = $this->scopeByAgency(Purchase::query(), $agency_id)
                ->whereDate('purchase_date', $date)
                ->sum('total_amount');

            $salesData[] = (float) $dailySales;
            $purchasesData[] = (float) $dailyPurchases;
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
                'products.id',
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

        return $query->get()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'total_quantity' => (int) $item->total_quantity,
                'total_revenue' => (float) $item->total_revenue
            ];
        });
    }

    private function getRecentActivities($agency_id, $limit = 5)
    {
        $activities = collect();

        // Récentes ventes
        $recentSales = $this->scopeByAgency(Sale::with('client'), $agency_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'type' => 'sale',
                    'title' => 'Nouvelle vente',
                    'description' => "Vente #{$sale->id} - Client: {$sale->client->name}",
                    'amount' => (float) $sale->total_amount,
                    'created_at' => $sale->created_at->toISOString(),
                    'badge_class' => 'bg-success',
                    'icon' => 'bi-cart-check'
                ];
            });

        // Récents achats
        $recentPurchases = $this->scopeByAgency(Purchase::with('supplier'), $agency_id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($purchase) {
                return [
                    'id' => $purchase->id,
                    'type' => 'purchase',
                    'title' => 'Réapprovisionnement',
                    'description' => "Achat #{$purchase->id} - Fournisseur: {$purchase->supplier->name}",
                    'amount' => (float) $purchase->total_amount,
                    'created_at' => $purchase->created_at->toISOString(),
                    'badge_class' => 'bg-info',
                    'icon' => 'bi-box-arrow-in-down'
                ];
            });

        // Nouveaux clients
        $newClients = $this->scopeByAgency(Client::query(), $agency_id)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'type' => 'client',
                    'title' => 'Nouveau client',
                    'description' => "Inscription: {$client->name}",
                    'amount' => null,
                    'created_at' => $client->created_at->toISOString(),
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
                'products.id',
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

        return $query->get()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => (int) $item->quantity,
                'alert_quantity' => (int) $item->alert_quantity,
                'stock_name' => $item->stock_name
            ];
        });
    }

    private function getCashRegisterStatus($agency_id)
    {
        $activeCashRegister = $this->scopeByAgency(CashRegister::query(), $agency_id)
            ->where('status', 'open')
            ->where('user_id', auth()->id())
            ->first();

        if (!$activeCashRegister) {
            return null;
        }

        // Recettes du jour
        $todayRevenue = $this->scopeByAgency(Sale::query(), $agency_id)
            ->whereDate('sale_date', Carbon::today())
            ->sum('total_amount');

        // Dépenses du jour
        $todayExpenses = $this->scopeByAgency(Expense::query(), $agency_id)
            ->whereDate('expense_date', Carbon::today())
            ->sum('amount');

        return [
            'id' => $activeCashRegister->id,
            'opening_balance' => (float) $activeCashRegister->opening_balance,
            'today_revenue' => (float) $todayRevenue,
            'today_expenses' => (float) $todayExpenses,
            'current_balance' => (float) ($activeCashRegister->opening_balance + $todayRevenue - $todayExpenses),
            'opened_at' => Carbon::parse($activeCashRegister->opened_at)->toISOString()
        ];
    }

    private function getSalesTrend($agency_id)
    {
        $currentMonth = $this->scopeByAgency(Sale::query(), $agency_id)
            ->whereMonth('sale_date', Carbon::now()->month)
            ->whereYear('sale_date', Carbon::now()->year)
            ->sum('total_amount');

        $previousMonth = $this->scopeByAgency(Sale::query(), $agency_id)
            ->whereMonth('sale_date', Carbon::now()->subMonth()->month)
            ->whereYear('sale_date', Carbon::now()->subMonth()->year)
            ->sum('total_amount');

        $trend = $previousMonth > 0
            ? (($currentMonth - $previousMonth) / $previousMonth) * 100
            : 0;

        return [
            'current' => (float) $currentMonth,
            'previous' => (float) $previousMonth,
            'trend' => round($trend, 1),
            'is_positive' => $trend >= 0
        ];
    }

    private function getAgencies($company_id)
    {
        return Agency::where('company_id', $company_id)
            ->select('id', 'name')
            ->get()
            ->map(function($agency) {
                return [
                    'id' => $agency->id,
                    'name' => $agency->name
                ];
            });
    }

    private function scopeByAgency($query, $agency_id)
    {
        return $agency_id ? $query->where('agency_id', $agency_id) : $query;
    }

    public function updatePeriod(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:7,30,90',
            'agency_id' => 'nullable|exists:agencies,id'
        ]);

        return sendSuccess('Paramètres mis à jour', $validated);
    }
}
