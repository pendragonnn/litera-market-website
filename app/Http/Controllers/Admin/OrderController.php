<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display all orders for admin.
     */
    public function index()
    {
        $orders = Order::with(['user', 'payment'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show details for a specific order.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'payment', 'orderItems.book']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Confirm an order (admin action).
     */
    public function confirm(Order $order)
    {
        DB::transaction(function () use ($order) {
            // Update payment status
            if ($order->payment) {
                $order->payment->update(['payment_status' => 'paid']);
            }

            // Update order status
            $order->update(['status' => 'shipped']);
        });

        return redirect()->route('admin.orders.index')->with('success', 'Order successfully confirmed and marked as shipped.');
    }

    /**
     * Reject an order (admin action).
     */
    public function reject(Request $request, Order $order)
    {
        DB::transaction(function () use ($order, $request) {
            // Update payment status
            if ($order->payment) {
                $order->payment->update([
                    'payment_status' => 'rejected',
                    'admin_note' => $request->input('admin_note')
                ]);
            }

            // Update order status
            $order->update(['status' => 'cancelled']);
        });

        return redirect()->route('admin.orders.index')->with('error', 'Order has been rejected.');
    }
}
