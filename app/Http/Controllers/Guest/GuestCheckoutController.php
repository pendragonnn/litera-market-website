<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GuestCheckoutController extends Controller
{
    /**
     * Show guest checkout form
     */
    public function index()
    {
        return view('guest.checkout.index');
    }

    /**
     * Handle guest checkout submission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|max:100',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string|max:255',
            'cart'        => 'required|array|min:1',
            'cart.*.book_id'  => 'required|exists:books,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Buat token unik buat tracking order guest
            $token = Str::uuid()->toString();

            // Buat order baru
            $order = Order::create([
                'user_id'     => null,
                'token_order' => $token,
                'name'        => $validated['name'],
                'phone'       => $validated['phone'],
                'address'     => $validated['address'],
                'total_price' => 0,
                'status'      => 'Pending',
            ]);

            $total = 0;

            // Simpan semua item dari cart localStorage
            foreach ($validated['cart'] as $item) {
                $book = Book::findOrFail($item['book_id']);
                $subtotal = $book->price * $item['quantity'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id'  => $book->id,
                    'quantity' => $item['quantity'],
                    'price'    => $book->price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total harga
            $order->update(['total_price' => $total]);

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_url' => route('guest.checkout.success', $token),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed. Please try again later.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Success page setelah guest selesai order
     */
    public function success($token)
    {
        $order = Order::where('token_order', $token)->firstOrFail();
        return view('guest.checkout.success', compact('order'));
    }
}
