<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MyOrdersController extends Controller
{
    /**
     * Show user's orders grouped by status
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua order milik user
        $orders = Order::with(['orderItems.book', 'payment'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pisahkan berdasarkan status
        $groupedOrders = [
            'Pending' => $orders->where('status', 'Pending'),
            'Processed' => $orders->where('status', 'Processed'),
            'Shipped' => $orders->where('status', 'Shipped'),
            'Delivered' => $orders->where('status', 'Delivered'),
            'Cancelled' => $orders->where('status', 'Cancelled'),
        ];

        return view('user.orders.index', compact('groupedOrders'));
    }

    /**
     * Cancel an order (Pending/Processed)
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($order->status, ['Pending', 'Processed'])) {
            return back()->with('error', 'You can only cancel pending or processed orders.');
        }

        // 🔁 Kembalikan stok setiap buku dalam order
        $order->load('orderItems.book'); // pastikan relasi sudah diload

        foreach ($order->orderItems as $item) {
            if ($item->book) {
                $item->book->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'Cancelled']);

        // if ($order->payment) {
        //     $order->payment->update(['payment_status' => 'Rejected']);
        // }

        return back()->with('success', 'Order has been cancelled successfully.');
    }

    /**
     * Mark an order as completed (Delivered)
     */
    public function complete(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'Shipped') {
            return back()->with('error', 'Only shipped orders can be marked as complete.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'Delivered']);

            // Jika ada data payment, ubah jadi paid
            if ($order->payment) {
                $order->payment->update(['payment_status' => 'Paid']);
            }
        });

        return back()->with('success', 'Order marked as delivered successfully.');
    }


    /**
     * Store uploaded payment proof
     */
    public function storeProof(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $payment = $order->payment;

        if (!$payment) {
            return back()->with('error', 'Payment record not found for this order.');
        }

        // Simpan file ke storage/public/payments
        $path = $request->file('payment_proof')->store('payments', 'public');

        // Update payment & order status
        $payment->update([
            'payment_proof' => $path,
            'payment_status' => 'Awaiting Approval',
        ]);

        $order->update([
            'status' => 'processed',
        ]);

        return back()->with('success', 'Payment proof uploaded successfully!');
    }
}
