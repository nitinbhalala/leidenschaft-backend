<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;

class DashboardController extends BaseController
{
    public function index()
    {
        $totalSales = Payment::where('status', 'completed')->sum('amount');
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();

        $salesOverview = Payment::selectRaw("
            MONTH(created_at) as month,
            SUM(amount) as total
        ")
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $weeklyOrders = Order::selectRaw("
            DAYNAME(created_at) as day,
            COUNT(*) as total
        ")
            ->groupBy('day')
            ->get();

        $categorySales = OrderItem::selectRaw('products.category, SUM(order_items.total) as total')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.category')
            ->get();

        $topProducts = OrderItem::selectRaw('products.name, SUM(order_items.quantity) as total_sold')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $recentOrders = Order::latest()
            ->take(5)
            ->get(['id', 'user_id', 'total_amount', 'status', 'created_at']);

        $lowStock = Product::whereColumn('stock', '<=', 'threshold')
            ->get(['name', 'stock', 'threshold']);

        return $this->success([
            'cards' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
            ],
            'sales_overview' => $salesOverview,
            'weekly_orders' => $weeklyOrders,
            'category_sales' => $categorySales,
            'top_products' => $topProducts,
            'recent_orders' => $recentOrders,
            'low_stock' => $lowStock,
        ], "Dashboard data fetched");
    }
}
