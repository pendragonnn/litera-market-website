<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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
     * Show order details
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
        $order = Order::where('token_order', $token)->with('payment')->firstOrFail();

        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->payment->update([
            'payment_proof' => $path,
            'payment_status' => 'Pending Approval',
        ]);

        return back()->with('success', 'Payment proof uploaded successfully!');
    }

    /**
     * Cancel order (if still pending)
     */
    public function cancel($token)
    {
        $order = Order::where('token_order', $token)->firstOrFail();

        if ($order->status === 'Pending') {
            $order->update(['status' => 'Cancelled']);
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->with('error', 'This order cannot be cancelled.');
    }

    /**
     * Mark order as complete
     */
    public function complete($token)
    {
        $order = Order::where('token_order', $token)->firstOrFail();

        if (in_array($order->status, ['Processed', 'Shipped'])) {
            $order->update(['status' => 'Delivered']);
            return back()->with('success', 'Order marked as completed.');
        }

        return back()->with('error', 'This order cannot be marked as completed.');
    }
}
