<?php

declare(strict_types=1);

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Carts\Cart;
use App\Models\Coupons\Coupon;
use App\Models\Newsletter\NewsletterSubscription;
use App\Models\Orders\Order;
use App\Models\Products\Product;
use App\Models\Reviews\Review;
use App\Models\Users\User;
use App\Models\Wishlists\WishlistItem;
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
            'categoryPerformance' => $this->getCategoryPerformance(),
            'couponAnalytics' => $this->getCouponAnalytics(),
            'reviewAnalytics' => $this->getReviewAnalytics(),
            'newsletterAnalytics' => $this->getNewsletterAnalytics(),
            'cartAnalytics' => $this->getCartAnalytics(),
            'inventoryAnalytics' => $this->getInventoryAnalytics(),
            'customerAnalytics' => $this->getCustomerAnalytics(),
            'productAnalytics' => $this->getProductAnalytics(),
        ];

        return response()->json(['data' => $data]);
    }

    private function getOverviewData(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthRevenue = (float)Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $currentMonth)
            ->sum('total_amount');

        $lastMonthRevenue = (float)Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $currentMonth)
            ->sum('total_amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        $currentMonthOrders = Order::where('created_at', '>=', $currentMonth)->count();
        $lastMonthOrders = Order::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $currentMonth)->count();

        $ordersGrowth = $lastMonthOrders > 0
            ? (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100
            : 0;

        $averageOrderValue = Order::where('payment_status', 'paid')->count() > 0
            ? (float)Order::where('payment_status', 'paid')->avg('total_amount')
            : 0;

        return [
            'totalRevenue' => (float)Order::where('payment_status', 'paid')->sum('total_amount'),
            'monthlyRevenue' => $currentMonthRevenue,
            'revenueGrowth' => round($revenueGrowth, 2),
            'totalOrders' => Order::count(),
            'monthlyOrders' => $currentMonthOrders,
            'ordersGrowth' => round($ordersGrowth, 2),
            'totalUsers' => User::count(),
            'newUsers' => User::where('created_at', '>=', $currentMonth)->count(),
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('status', 'active')->count(),
            'lowStockProducts' => Product::where('stock', '<=', 5)->count(),
            'outOfStockProducts' => Product::where('stock', 0)->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
            'averageOrderValue' => round($averageOrderValue, 2),
            'totalCouponsUsed' => Coupon::sum('used_count'),
            'totalReviews' => Review::count(),
            'pendingReviews' => Review::where('is_approved', false)->count(),
            'newsletterSubscribers' => NewsletterSubscription::where('is_active', true)->count(),
        ];
    }

    private function getSalesData(): array
    {
        $salesByDay = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('AVG(total_amount) as average_order')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesByPaymentMethod = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select('payment_method', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        $salesByHour = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'dailySales' => $salesByDay,
            'totalSales' => (float)$salesByDay->sum('total'),
            'totalOrders' => $salesByDay->sum('orders'),
            'averageDaily' => $salesByDay->count() > 0 ? (float)$salesByDay->avg('total') : 0,
            'salesByPaymentMethod' => $salesByPaymentMethod,
            'salesByHour' => $salesByHour,
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
                'products.price',
                'products.sale_price',
                'products.stock',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as revenue'),
                DB::raw('AVG(reviews.rating) as avg_rating'),
                DB::raw('COUNT(reviews.id) as review_count')
            )
            ->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
            ->groupBy('products.id', 'products.name', 'products.slug', 'products.price', 'products.sale_price', 'products.stock')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
    }

    private function getRecentOrders(): array
    {
        return Order::with(['user', 'items.product'])
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
                DB::raw('SUM(order_items.quantity) as items_sold'),
                DB::raw('COUNT(DISTINCT products.id) as total_products'),
                DB::raw('AVG(reviews.rating) as avg_rating')
            )
            ->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }

    private function getCouponAnalytics(): array
    {
        $totalCoupons = Coupon::count();
        $activeCoupons = Coupon::where('is_active', true)->count();
        $usedCoupons = Coupon::where('used_count', '>', 0)->count();
        $totalDiscountGiven = (float)DB::table('coupon_usages')->sum('discount_amount');

        $popularCoupons = Coupon::select(['code', 'type', 'value', 'used_count'])
            ->orderByDesc('used_count')
            ->limit(5)
            ->get();

        $couponUsageByMonth = DB::table('coupon_usages')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as uses'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $ordersWithCoupons = Order::whereNotNull('coupon_id')->count();
        $totalOrders = Order::count();
        $couponConversionRate = $totalOrders > 0 ? ($ordersWithCoupons / $totalOrders) * 100 : 0;

        return [
            'totalCoupons' => $totalCoupons,
            'activeCoupons' => $activeCoupons,
            'usedCoupons' => $usedCoupons,
            'totalDiscountGiven' => $totalDiscountGiven,
            'popularCoupons' => $popularCoupons,
            'couponUsageByMonth' => $couponUsageByMonth,
            'couponConversionRate' => round($couponConversionRate, 2),
        ];
    }

    private function getReviewAnalytics(): array
    {
        $totalReviews = Review::count();
        $approvedReviews = Review::where('is_approved', true)->count();
        $pendingReviews = Review::where('is_approved', false)->count();

        $averageRating = Review::where('is_approved', true)->count() > 0
            ? (float)Review::where('is_approved', true)->avg('rating')
            : 0;

        $ratingDistribution = Review::where('is_approved', true)
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        $categoryRatings = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('reviews', 'products.id', '=', 'reviews.product_id')
            ->where('reviews.is_approved', true)
            ->select(
                'categories.name',
                DB::raw('AVG(reviews.rating) as avg_rating'),
                DB::raw('COUNT(reviews.id) as review_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->having('review_count', '>=', 5)
            ->orderByDesc('avg_rating')
            ->get();

        $reviewsByMonth = Review::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total_reviews'),
            DB::raw('AVG(rating) as avg_rating')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'totalReviews' => $totalReviews,
            'approvedReviews' => $approvedReviews,
            'pendingReviews' => $pendingReviews,
            'approvalRate' => $totalReviews > 0 ? round(($approvedReviews / $totalReviews) * 100, 2) : 0,
            'averageRating' => round($averageRating, 2),
            'ratingDistribution' => $ratingDistribution,
            'categoryRatings' => $categoryRatings,
            'reviewsByMonth' => $reviewsByMonth,
        ];
    }

    private function getNewsletterAnalytics(): array
    {
        $totalSubscribers = NewsletterSubscription::count();
        $activeSubscribers = NewsletterSubscription::where('is_active', true)->count();
        $unsubscribed = NewsletterSubscription::where('is_active', false)->count();

        $subscriptionGrowth = NewsletterSubscription::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as new_subscribers')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $unsubscriptionRate = NewsletterSubscription::select(
            DB::raw('DATE_FORMAT(unsubscribed_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as unsubscribers')
        )
            ->whereNotNull('unsubscribed_at')
            ->where('unsubscribed_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'totalSubscribers' => $totalSubscribers,
            'activeSubscribers' => $activeSubscribers,
            'unsubscribed' => $unsubscribed,
            'subscriptionRate' => $totalSubscribers > 0 ? round(($activeSubscribers / $totalSubscribers) * 100, 2) : 0,
            'subscriptionGrowth' => $subscriptionGrowth,
            'unsubscriptionRate' => $unsubscriptionRate,
        ];
    }

    private function getCartAnalytics(): array
    {
        $totalCarts = Cart::count();
        $cartsWithItems = Cart::whereHas('items')->count();
        $emptyCarts = $totalCarts - $cartsWithItems;

        $cartsConverted = DB::table('carts')
            ->join('orders', 'carts.user_id', '=', 'orders.user_id')
            ->distinct('carts.id')
            ->count();
        $abandonmentRate = $cartsWithItems > 0 ? (1 - ($cartsConverted / $cartsWithItems)) * 100 : 0;

        $cartItemsCount = DB::table('cart_items')->count();
        $averageCartValue = $cartItemsCount > 0
            ? (float)DB::table('cart_items')->avg('total_price')
            : 0;

        $cartItemsByCategory = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw('SUM(cart_items.quantity) as total_quantity'),
                DB::raw('SUM(cart_items.total_price) as total_value')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_value')
            ->get();

        return [
            'totalCarts' => $totalCarts,
            'cartsWithItems' => $cartsWithItems,
            'emptyCarts' => $emptyCarts,
            'abandonmentRate' => round($abandonmentRate, 2),
            'conversionRate' => round(100 - $abandonmentRate, 2),
            'averageCartValue' => round($averageCartValue, 2),
            'cartItemsByCategory' => $cartItemsByCategory,
        ];
    }

    private function getInventoryAnalytics(): array
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $lowStockProducts = Product::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();
        $productsOnSale = Product::whereNotNull('sale_price')->count();

        $stockByCategory = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->select(
                'categories.name',
                DB::raw('SUM(products.stock) as total_stock'),
                DB::raw('COUNT(products.id) as product_count'),
                DB::raw('AVG(products.stock) as avg_stock')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_stock')
            ->get();

        $fastMovingProducts = Product::select(['name', 'sold', 'stock'])
            ->where('sold', '>', 0)
            ->orderByDesc('sold')
            ->limit(10)
            ->get();

        $slowMovingProducts = Product::select(['name', 'sold', 'stock'])
            ->where('status', 'active')
            ->orderBy('sold')
            ->limit(10)
            ->get();

        $needRestock = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->get(['name', 'stock', 'sold']);

        return [
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'productsOnSale' => $productsOnSale,
            'stockByCategory' => $stockByCategory,
            'fastMovingProducts' => $fastMovingProducts,
            'slowMovingProducts' => $slowMovingProducts,
            'needRestock' => $needRestock,
        ];
    }

    private function getCustomerAnalytics(): array
    {
        $totalCustomers = User::where('role_id', '!=', 1)->count();

        $customersWithOrders = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.id')
            ->distinct()
            ->count();

        $customersWithoutOrders = $totalCustomers - $customersWithOrders;

        $customerLTV = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('SUM(orders.total_amount) as total_spent'),
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('AVG(orders.total_amount) as avg_order_value')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $repeatCustomers = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.id')
            ->groupBy('users.id')
            ->havingRaw('COUNT(orders.id) > 1')
            ->count();

        $repeatCustomerRate = $customersWithOrders > 0 ? ($repeatCustomers / $customersWithOrders) * 100 : 0;

        $customersByMonth = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as new_customers')
        )
            ->where('role_id', '!=', 1)
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'totalCustomers' => $totalCustomers,
            'customersWithOrders' => $customersWithOrders,
            'customersWithoutOrders' => $customersWithoutOrders,
            'customerConversionRate' => $totalCustomers > 0 ? round(($customersWithOrders / $totalCustomers) * 100, 2) : 0,
            'repeatCustomers' => $repeatCustomers,
            'repeatCustomerRate' => round($repeatCustomerRate, 2),
            'topCustomers' => $customerLTV,
            'customersByMonth' => $customersByMonth,
        ];
    }

    private function getProductAnalytics(): array
    {
        $totalWishlistItems = WishlistItem::count();
        $mostWishlisted = DB::table('wishlist_items')
            ->join('products', 'wishlist_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('COUNT(*) as wishlist_count')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('wishlist_count')
            ->limit(10)
            ->get();

        $wishlistToPurchase = DB::table('wishlist_items')
            ->join('order_items', 'wishlist_items.product_id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.user_id', DB::raw('wishlist_items.user_id'))
            ->distinct()
            ->count('wishlist_items.id');

        $wishlistConversionRate = $totalWishlistItems > 0 ? ($wishlistToPurchase / $totalWishlistItems) * 100 : 0;

        $productsWithSales = Product::whereNotNull('sale_price')->count();
        $revenueFromSales = (float)DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('order_items.product_sale_price')
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.total_price');

        $totalRevenue = (float)Order::where('payment_status', 'paid')->sum('total_amount');
        $saleRevenuePercentage = $totalRevenue > 0 ? ($revenueFromSales / $totalRevenue) * 100 : 0;

        $productsByStatus = Product::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return [
            'totalWishlistItems' => $totalWishlistItems,
            'mostWishlisted' => $mostWishlisted,
            'wishlistConversionRate' => round($wishlistConversionRate, 2),
            'productsWithSales' => $productsWithSales,
            'saleRevenuePercentage' => round($saleRevenuePercentage, 2),
            'productsByStatus' => $productsByStatus,
        ];
    }

    public function getSalesAnalytics(): JsonResponse
    {
        return response()->json(['data' => $this->getSalesData()]);
    }

    public function getCustomerAnalyticsDetails(): JsonResponse
    {
        return response()->json(['data' => $this->getCustomerAnalytics()]);
    }

    public function getProductAnalyticsDetails(): JsonResponse
    {
        return response()->json(['data' => $this->getProductAnalytics()]);
    }

    public function getInventoryAnalyticsDetails(): JsonResponse
    {
        return response()->json(['data' => $this->getInventoryAnalytics()]);
    }
}
