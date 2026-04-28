<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    public function index()
    {
        $now = now();
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $endOfCurrentMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $startOfCurrentMonth->copy()->subMonth();
        $endOfLastMonth = $startOfLastMonth->copy()->endOfMonth();

        // 1. Summary Statistics with MoM Growth
        $totalSales = Order::where('status', '!=', 'failed')->sum('total');
        $currentMonthSales = Order::where('status', '!=', 'failed')
            ->whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])
            ->sum('total');
        $lastMonthSales = Order::where('status', '!=', 'failed')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total');
        $salesGrowth = $this->calculateGrowth($currentMonthSales, $lastMonthSales);

        $totalOrders = Order::count();
        $currentMonthOrders = Order::whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $ordersGrowth = $this->calculateGrowth($currentMonthOrders, $lastMonthOrders);

        $totalCustomers = Customer::count();
        $currentMonthCustomers = Customer::whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count();
        $lastMonthCustomers = Customer::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $customersGrowth = $this->calculateGrowth($currentMonthCustomers, $lastMonthCustomers);

        $totalProducts = Product::count();
        $currentMonthProducts = Product::whereBetween('created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count();
        $lastMonthProducts = Product::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $productsGrowth = $this->calculateGrowth($currentMonthProducts, $lastMonthProducts);

        // 2. Sales Overview (7 Months)
        $sevenMonthsAgo = $now->copy()->subMonths(6)->startOfMonth();
        $salesOverviewData = Order::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_year,
                DATE_FORMAT(created_at, '%b') as month_name,
                SUM(total) as revenue,
                COUNT(*) as orders
            ")
            ->where('status', '!=', 'failed')
            ->where('created_at', '>=', $sevenMonthsAgo)
            ->groupBy('month_year', 'month_name')
            ->orderBy('month_year')
            ->get();

        $totalRevenue7 = $salesOverviewData->sum('revenue');
        $totalOrders7 = $salesOverviewData->sum('orders');
        $avgMonthly = $salesOverviewData->count() > 0 ? $totalRevenue7 / $salesOverviewData->count() : 0;

        // 3. Weekly Activity
        $startOfWeek = $now->copy()->startOfWeek();
        $weeklyRevenue = Order::where('status', '!=', 'failed')
            ->where('created_at', '>=', $startOfWeek)
            ->sum('total');
        $weeklyOrdersCount = Order::where('created_at', '>=', $startOfWeek)
            ->count();

        $weeklyActivity = Order::selectRaw("
                DAYNAME(created_at) as day,
                COUNT(*) as orders
            ")
            ->where('created_at', '>=', $startOfWeek)
            ->groupBy('day')
            ->get()
            ->pluck('orders', 'day');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $formattedWeeklyActivity = collect($days)->map(function ($day) use ($weeklyActivity) {
            return [
                'day' => $day,
                'orders' => $weeklyActivity->get($day, 0)
            ];
        });

        // 4. Sales by Category
        $categorySales = Category::select('categories.name', DB::raw('SUM(order_items.total) as revenue'))
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'failed')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        // 5. Traffic Sources (Derive from Customer Provider or random for demo)
        $trafficSources = Customer::selectRaw("
                IFNULL(provider, 'Direct') as source,
                COUNT(*) as count
            ")
            ->groupBy('source')
            ->get()
            ->map(function ($item) use ($totalCustomers) {
                return [
                    'source' => ucfirst($item->source),
                    'percentage' => $totalCustomers > 0 ? round(($item->count / $totalCustomers) * 100, 1) : 0
                ];
            });

        // 6. Top Products
        $topProducts = Product::select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as sold'), DB::raw('SUM(order_items.total) as revenue'))
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(4)
            ->get();

        // 7. Recent Orders
        $recentOrders = Order::with([
            'customer:id,name',
            'items.product:id,name'
        ])
            ->latest()
            ->take(5)
            ->get(['id', 'order_number', 'customer_id', 'total', 'status', 'created_at']);

        // 8. Low Stock Alert
        $lowStock = Product::join('product_inventories', 'product_inventories.product_id', '=', 'products.id')
            ->whereRaw('product_inventories.stock <= product_inventories.low_stock_threshold')
            ->select('products.name', 'product_inventories.stock', 'product_inventories.low_stock_threshold as threshold')
            ->get();

        return $this->success([
            'summary' => [
                'total_sales' => [
                    'value' => number_format($totalSales, 2),
                    'growth' => $salesGrowth
                ],
                'total_orders' => [
                    'value' => $totalOrders,
                    'growth' => $ordersGrowth
                ],
                'total_customers' => [
                    'value' => $totalCustomers,
                    'growth' => $customersGrowth
                ],
                'total_products' => [
                    'value' => $totalProducts,
                    'growth' => $productsGrowth
                ],
            ],
            'sales_overview' => [
                'chart' => $salesOverviewData,
                'total_revenue' => number_format($totalRevenue7, 2),
                'avg_monthly' => number_format($avgMonthly, 2),
                'total_orders' => $totalOrders7
            ],
            'weekly_activity' => [
                'chart' => $formattedWeeklyActivity,
                'total_orders' => $weeklyOrdersCount,
                'revenue_this_week' => number_format($weeklyRevenue, 2),
                'best_performance' => $formattedWeeklyActivity->sortByDesc('orders')->first()
            ],
            'category_sales' => $categorySales,
            'traffic_sources' => $trafficSources,
            'top_products' => $topProducts,
            'recent_orders' => $recentOrders,
            'low_stock' => $lowStock,
        ], "Dashboard data fetched successfully");
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
