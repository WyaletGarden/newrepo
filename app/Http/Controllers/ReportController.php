<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Thống kê tổng quan
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'total_customers' => Customer::count(),
            'total_products' => Product::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->count(),
        ];

        // Đơn hàng mới nhất
        $recentOrders = Order::with(['customer', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Sản phẩm bán chạy
        $topProducts = Product::withCount(['orderItems as total_sold' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', '!=', 'cancelled');
                });
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Tính toán các biến bổ sung
        $monthlyRevenue = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $weeklyRevenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_amount');

        $todayRevenue = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $inStockProducts = Product::where('stock_quantity', '>', 0)->count();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();

        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $deliveredOrders = Order::where('status', 'delivered')->count();

        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $vipCustomers = Customer::withCount(['orders as total_orders' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->having('total_orders', '>=', 5)
            ->count();

        $potentialCustomers = Customer::withCount(['orders as total_orders' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->having('total_orders', '>=', 1)
            ->having('total_orders', '<', 5)
            ->count();

        return view('reports.index', [
            'totalRevenue' => $stats['total_revenue'],
            'totalOrders' => $stats['total_orders'],
            'totalProducts' => $stats['total_products'],
            'totalCustomers' => $stats['total_customers'],
            'pendingOrders' => $stats['pending_orders'],
            'lowStockProducts' => $stats['low_stock_products'],
            'monthlyRevenue' => $monthlyRevenue,
            'weeklyRevenue' => $weeklyRevenue,
            'todayRevenue' => $todayRevenue,
            'inStockProducts' => $inStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'topSellingProducts' => $topProducts,
            'ordersThisMonth' => $ordersThisMonth,
            'deliveredOrders' => $deliveredOrders,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'vipCustomers' => $vipCustomers,
            'potentialCustomers' => $potentialCustomers,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Báo cáo doanh thu
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Nếu không có ngày cụ thể, sử dụng period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
            }
        } else {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        // Doanh thu theo ngày
        $dailyRevenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Doanh thu theo tháng
        $monthlyRevenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate->startOfYear(), $endDate->endOfYear()])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Thống kê theo trạng thái đơn hàng
        $orderStatusStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('status')
            ->get();

        // Top khách hàng
        $topCustomers = Customer::withCount(['orders as total_orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
            }])
            ->withSum(['orders as total_spent' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return view('reports.revenue', compact(
            'dailyRevenue', 
            'monthlyRevenue', 
            'orderStatusStats', 
            'topCustomers',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Báo cáo sản phẩm
     */
    public function products(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Nếu không có ngày cụ thể, sử dụng period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
            }
        } else {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        // Sản phẩm bán chạy
        $topSellingProducts = Product::withCount(['orderItems as total_sold' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
                });
            }])
            ->withSum(['orderItems as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
                });
            }], 'total_price')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Thống kê tồn kho
        $stockStats = [
            'in_stock' => Product::where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'low_stock' => Product::where('stock_quantity', '<', 10)->where('stock_quantity', '>', 0)->count(),
            'total_products' => Product::count(),
        ];

        // Sản phẩm theo danh mục
        $productsByCategory = Product::with('category')
            ->selectRaw('category_id, COUNT(*) as count, SUM(stock_quantity) as total_stock')
            ->groupBy('category_id')
            ->get();

        return view('reports.products', compact(
            'topSellingProducts',
            'stockStats',
            'productsByCategory',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Báo cáo khách hàng
     */
    public function customers(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Nếu không có ngày cụ thể, sử dụng period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
            }
        } else {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        // Top khách hàng
        $topCustomers = Customer::withCount(['orders as total_orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
            }])
            ->withSum(['orders as total_spent' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Khách hàng mới
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Thống kê khách hàng
        $customerStats = [
            'total_customers' => Customer::count(),
            'new_customers' => Customer::whereBetween('created_at', [$startDate, $endDate])->count(),
            'vip_customers' => Customer::withCount(['orders as total_orders' => function($query) {
                    $query->where('status', '!=', 'cancelled');
                }])
                ->having('total_orders', '>=', 5)
                ->count(),
            'active_customers' => Customer::whereHas('orders', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', '!=', 'cancelled');
                })
                ->count(),
        ];

        return view('reports.customers', compact(
            'topCustomers',
            'newCustomers',
            'customerStats',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Báo cáo đơn hàng
     */
    public function orders(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Nếu không có ngày cụ thể, sử dụng period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
            }
        } else {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        // Đơn hàng theo trạng thái
        $ordersByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('status')
            ->get();

        // Đơn hàng theo ngày
        $ordersByDate = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Đơn hàng gần đây
        $recentOrders = Order::with(['customer', 'orderItems.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Thống kê đơn hàng
        $orderStats = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'pending_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'pending')
                ->count(),
            'delivered_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'delivered')
                ->count(),
        ];

        return view('reports.orders', compact(
            'ordersByStatus',
            'ordersByDate',
            'recentOrders',
            'orderStats',
            'startDate',
            'endDate',
            'period'
        ));
    }
}
