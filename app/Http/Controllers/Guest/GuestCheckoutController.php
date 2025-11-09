<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestCheckoutController extends Controller
{
  /**
   * Tampilkan halaman checkout untuk guest
   */
  public function index()
  {
    return view('guest.checkout.index');
  }

  /**
   * Proses penyimpanan order guest (tanpa token)
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:100',
      'phone' => 'required|string|max:20',
      'address' => 'required|string|max:255',
      'payment_method' => 'required|string|max:100',
      'cart' => 'required|array|min:1',
      'cart.*.book_id' => 'required|exists:books,id',
      'cart.*.quantity' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
      $isCOD = strtolower($validated['payment_method']) === 'cod';
      $orderStatus = $isCOD ? 'Processed' : 'Pending';
      $paymentStatus = 'Unpaid';

      $order = Order::create([
        'user_id' => null,
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'address' => $validated['address'],
        'total_price' => 0,
        'status' => $orderStatus,
      ]);

      $total = 0;

      foreach ($validated['cart'] as $item) {
        $book = Book::findOrFail($item['book_id']);

        if ($book->stock < $item['quantity']) {
          throw new \Exception("Insufficient stock for {$book->title}");
        }

        $subtotal = $book->price * $item['quantity'];
        $total += $subtotal;

        OrderItem::create([
          'order_id' => $order->id,
          'book_id' => $book->id,
          'quantity' => $item['quantity'],
          'price' => $book->price,
          'subtotal' => $subtotal,
        ]);

        $book->decrement('stock', $item['quantity']);
      }

      $order->update(['total_price' => $total]);

      Payment::create([
        'order_id' => $order->id,
        'payment_method' => strtoupper($validated['payment_method']),
        'payment_status' => $paymentStatus,
        'payment_proof' => null,
      ]);

      DB::commit();

      // 🔐 Generate secure key
      $key = hash('sha256', $order->id . '|' . $order->phone . config('app.key'));

      // 🔁 Redirect ke success page pakai ID + key
      return response()->json([
        'success' => true,
        'redirect_url' => route('guest.checkout.success', ['id' => $order->id, 'key' => $key]),
      ]);
    } catch (\Exception $e) {
      DB::rollBack();

      return response()->json([
        'success' => false,
        'message' => 'Checkout failed: ' . $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Tampilkan halaman sukses setelah guest order
   */
  public function success($id, $key)
  {
    $order = Order::with('payment')->findOrFail($id);
    $validKey = hash('sha256', $order->id . '|' . $order->phone . config('app.key'));

    if ($key !== $validKey) {
      abort(403, 'Unauthorized access.');
    }

    return view('guest.checkout.success', compact('order'));
  }
}
