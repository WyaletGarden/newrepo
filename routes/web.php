<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
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

    return view('dashboard', compact('stats', 'recentOrders'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Orders
    Route::resource('orders', OrderController::class);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    Route::get('/reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
});

require __DIR__.'/auth.php';
