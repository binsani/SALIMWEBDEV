<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalSales = Transaction::successful()->sum('amount') / 100; // Convert from kobo
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $paidOrders = Order::paid()->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Best-selling products
        $bestSellingProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::with('user', 'order')
            ->latest()
            ->take(10)
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('product_type', 'physical')
            ->where('stock_quantity', '<=', DB::raw('(SELECT value FROM settings WHERE key = "low_stock_threshold")'))
            ->where('stock_quantity', '>', 0)
            ->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'pendingOrders',
            'paidOrders',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'bestSellingProducts',
            'recentTransactions',
            'lowStockProducts'
        ));
    }
}