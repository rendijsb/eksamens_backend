<?php

declare(strict_types=1);

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getDashboardData(): JsonResponse
    {
        $data = [
            'overview' => $this->getOverviewData(),
            'salesData' => $this->getSalesData(),
            'topProducts' => $this->getTopProducts(),
            'recentOrders' => $this->getRecentOrders(),
            'userGrowth' => $this->getUserGrowth(),
            'categoryPerformance' => $this->getCategoryPerformance()
        ];

        return response()->json(['data' => $data]);
    }

    private function getOverviewData(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'totalRevenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'monthlyRevenue' => Order::where('payment_status', 'paid')
                ->where('created_at', '>=', $currentMonth)
                ->sum('total_amount'),
            'totalOrders' => Order::count(),
            'monthlyOrders' => Order::where('created_at', '>=', $currentMonth)->count(),
            'totalUsers' => User::count(),
            'newUsers' => User::where('created_at', '>=', $currentMonth)->count(),
            'totalProducts' => Product::count(),
            'lowStockProducts' => Product::where('stock', '<=', 5)->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
        ];
    }

    private function getSalesData(): array
    {
        $salesByDay = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'dailySales' => $salesByDay,
            'totalSales' => $salesByDay->sum('total'),
            'totalOrders' => $salesByDay->sum('orders'),
            'averageDaily' => $salesByDay->avg('total')
        ];
    }

    private function getTopProducts(): Collection
    {
        return DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.name',
                'products.slug',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.slug')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
    }

    private function getRecentOrders(): array
    {
        return Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getUserGrowth(): array
    {
        $usersByMonth = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as users')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $usersByMonth->toArray();
    }

    private function getCategoryPerformance(): array
    {
        return DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.total_price) as revenue'),
                DB::raw('SUM(order_items.quantity) as items_sold')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }
}
