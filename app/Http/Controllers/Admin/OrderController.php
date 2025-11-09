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
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'user_id', 'name', 'total_price', 'status', 'created_at']);

        // dd($orders);
        $orders->transform(function ($order) {
            $order->display_name = $order->user->name ?? $order->name ?? 'Guest';
            return $order;
        });

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
     * Mark COD order as shipped (admin action).
     */
    public function shipCOD(Order $order)
    {
        // Pastikan ini order COD
        if (!$order->payment || strtoupper($order->payment->payment_method) !== 'COD') {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'This order is not a COD order.');
        }

        // Pastikan statusnya masih Processed sebelum dikirim
        if ($order->status !== 'Processed') {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'Only processed COD orders can be marked as shipped.');
        }

        DB::transaction(function () use ($order) {
            // Update order jadi shipped tanpa ubah status pembayaran
            $order->update(['status' => 'Shipped']);
        });

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'COD order has been marked as shipped successfully.');
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
            $order->update(['status' => 'pending']);
        });

        return redirect()->route('admin.orders.index')->with('error', 'Order has been rejected.');
    }

    public function cancel(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order->load('orderItems.book');

            foreach ($order->orderItems as $item) {
                if ($item->book) {
                    $item->book->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => 'Cancelled']);

            if ($order->payment) {
                $order->payment->update(['payment_status' => 'Rejected']);
            }
        });

        return redirect()->route('admin.orders.index')->with('error', 'Order has been cancelled and stock restored.');
    }
}
