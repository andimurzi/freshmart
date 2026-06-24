<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products'   => Product::count(),
            'categories' => Category::count(),
            'orders'     => Order::count(),
            'revenue'    => (int) Order::where('status', '!=', 'dibatalkan')->sum('total'),
        ];

        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $lowStock     = Product::active()->where('stock', '<', 10)->orderBy('stock')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStock'));
    }
}
