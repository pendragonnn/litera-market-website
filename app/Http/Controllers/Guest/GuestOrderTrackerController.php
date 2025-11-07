<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuestOrderTrackerController extends Controller
{
    /**
     * Show tracker form
     */
    public function index()
    {
        return view('guest.orders.tracker-form');
    }

    /**
     * Find order by token
     */
    public function find(Request $request)
    {
        $request->validate(['token_order' => 'required|string']);
        $token = strtoupper(trim($request->token_order));

        $order = Order::where('token_order', $token)->first();

        if (!$order) {
            return back()->with('error', 'Order not found. Please check your token.');
        }

        return redirect()->route('guest.order.tracker.show', ['token' => $token]);
    }

    /**
     * Show guest order details
     */
    public function show($token)
    {
        $order = Order::where('token_order', $token)
            ->with(['orderItems.book', 'payment'])
            ->firstOrFail();

        return view('guest.orders.tracker-show', compact('order'));
    }

    /**
     * Upload payment proof
     */
    public function uploadProof(Request $request, $token)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $order = Order::where('token_order', $token)->with('payment')->firstOrFail();
        $payment = $order->payment;

        if (!$payment) {
            return back()->with('error', 'Payment record not found.');
        }

        $path = $request->file('payment_proof')->store('payments', 'public');

        $payment->update([
            'payment_proof' => $path,
            'payment_status' => 'Awaiting Approval',
        ]);

        $order->update(['status' => 'Processed']);

        return back()->with('success', 'Payment proof uploaded successfully!');
    }

    /**
     * Cancel guest order
     */
    public function cancel($token)
    {
        $order = Order::where('token_order', $token)
            ->with(['payment', 'orderItems.book'])
            ->firstOrFail();

        // Validasi status
        if (!in_array($order->status, ['Pending', 'Processed'])) {
            return back()->with('error', 'You can only cancel pending or processed orders.');
        }

        // Kembalikan stok setiap buku di order
        foreach ($order->orderItems as $item) {
            if ($item->book) {
                $item->book->increment('stock', $item->quantity);
            }
        }

        // Update status order dan payment
        $order->update(['status' => 'Cancelled']);

        if ($order->payment) {
            $order->payment->update(['payment_status' => 'Rejected']);
        }

        return back()->with('success', 'Order has been cancelled successfully, and item stock has been restored.');
    }

    /**
     * Mark guest order as completed
     */
    public function complete($token)
    {
        $order = Order::where('token_order', $token)->firstOrFail();

        if ($order->status !== 'Shipped') {
            return back()->with('error', 'Only shipped orders can be marked as complete.');
        }

        $order->update(['status' => 'Delivered']);

        return back()->with('success', 'Order marked as delivered successfully.');
    }

    public function findToken(Request $request)
    {
        $request->validate([
            'id_order' => 'required|string',
            'phone' => 'required|string'
        ]);

        $order = Order::where('id', $request->id_order)
            ->where('phone', $request->phone)
            ->first();

        if (!$order) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'token_order' => $order->token_order
        ]);
    }
}
