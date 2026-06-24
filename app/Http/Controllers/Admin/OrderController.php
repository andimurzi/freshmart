<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'items'])
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('payment_status'), fn ($q, $ps) => $q->where('payment_status', $ps))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status'         => ['nullable', Rule::in(Order::STATUSES)],
            'payment_status' => ['nullable', Rule::in(Order::PAYMENT_STATUSES)],
            'payment_note'   => ['nullable', 'string', 'max:500'],
        ]);

        // Filter null values
        $data = array_filter($data, fn ($v) => $v !== null);

        $order->update($data);

        $msg = 'Pesanan ' . $order->invoice_number . ' diperbarui.';

        return back()->with('success', $msg);
    }
}
