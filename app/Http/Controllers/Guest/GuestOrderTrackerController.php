<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Find order by ID and phone number, redirect ke hashed secure URL
     */
    public function find(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric',
            'phone' => 'required|string'
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('phone', $request->phone)
            ->first();

        if (!$order) {
            return back()->with('error', 'Order not found. Please check your Order ID and phone number.');
        }

        $key = hash('sha256', $order->id . '|' . $order->phone . config('app.key'));

        return redirect()->route('guest.order.tracker.show', ['id' => $order->id, 'key' => $key]);
    }

    /**
     * Show guest order details (secured by hashed key)
     */
    public function show($id, $key)
    {
        $order = Order::with(['orderItems.book', 'payment'])->findOrFail($id);
        $validKey = hash('sha256', $order->id . '|' . $order->phone . config('app.key'));

        if ($key !== $validKey) {
            abort(403, 'Unauthorized access.');
        }

        return view('guest.orders.tracker-show', compact('order', 'key'));
    }

    /**
     * Upload payment proof (secured)
     */
    public function uploadProof(Request $request, $id, $key)
    {
        $order = Order::with('payment')->findOrFail($id);
        $validKey = hash('sha256', $order->id . '|' . $order->phone . config('app.key'));

        if ($key !== $validKey) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');

        $order->payment->update([
            'payment_proof' => $path,
            'payment_status' => 'Awaiting Approval',
        ]);

        $order->update(['status' => 'Processed']);

        return back()->with('success', 'Payment proof uploaded successfully!');
    }

    /**
     * Cancel guest order (secured)
     */
    public function cancel($id, $key)
    {
        $order = Order::with(['payment', 'orderItems.book'])->findOrFail($id);
        $validKey = hash('sha256', $order->id . '|' . $order->phone . config('app.key'));

        if ($key !== $validKey) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array($order->status, ['Pending', 'Processed'])) {
            return back()->with('error', 'You can only cancel pending or processed orders.');
        }

        foreach ($order->orderItems as $item) {
            if ($item->book) {
                $item->book->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'Cancelled']);
        // $order->payment->update(['payment_status' => 'Rejected']);

        return back()->with('success', 'Order has been cancelled successfully.');
    }

    /**
     * Mark as delivered (complete) (secured)
     */
    public function complete($id, $key)
    {
        $order = Order::with('payment')->findOrFail($id);
        $validKey = hash('sha256', $order->id . '|' . $order->phone . config('app.key'));

        if ($key !== $validKey) {
            abort(403, 'Unauthorized access.');
        }

        if ($order->status !== 'Shipped') {
            return back()->with('error', 'Only shipped orders can be marked as complete.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'Delivered']);
            $order->payment->update(['payment_status' => 'Paid']);
        });

        return back()->with('success', 'Order marked as delivered successfully.');
    }
}
