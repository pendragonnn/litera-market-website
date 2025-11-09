<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function index()
    {
        $cartItems = CartItem::with('book')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $totalPrice = $cartItems->sum(fn($item) => $item->book->price * $item->quantity);

        return view('user.checkout.index', compact('cartItems', 'totalPrice'));
    }

    /**
     * Handle checkout form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Update user info
            $user->update([
                'address' => $validated['address'],
                'phone' => $validated['phone'],
            ]);

            $cartItems = CartItem::with('book')->where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('user.cart.index')->with('error', 'Your cart is empty.');
            }

            $totalPrice = $cartItems->sum(fn($item) => $item->book->price * $item->quantity);

            // === COD Handling ===
            $isCOD = strtolower($validated['payment_method']) === 'cod';
            $orderStatus = $isCOD ? 'Processed' : 'Pending';
            $paymentStatus = $isCOD ? 'Unpaid' : 'Unpaid';

            // Buat Order
            $order = Order::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'total_price' => $totalPrice,
                'status' => $orderStatus,
            ]);

            // Buat Order Item + kurangi stok
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price,
                    'subtotal' => $item->book->price * $item->quantity,
                ]);

                $item->book->decrement('stock', $item->quantity);
            }

            // Buat Payment Record
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => strtoupper($validated['payment_method']),
                'payment_status' => $paymentStatus, // tetap unpaid dulu
                'payment_proof' => null,
            ]);

            // Hapus Cart
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return redirect()->route('user.checkout.success', ['order' => $order->id])
                ->with('success', 'Your order has been placed successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout failed. Please try again.');
        }
    }

    /**
     * Success page
     */
    public function success(Order $order)
    {
        $order->load('payment');
        return view('user.checkout.success', compact('order'));
    }
}
