<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** Riwayat pesanan milik user yang sedang login. */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(5);

        return view('orders.index', compact('orders'));
    }
}
