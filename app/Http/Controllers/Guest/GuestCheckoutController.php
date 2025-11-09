<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
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
      $token = strtoupper(Str::random(10));
      $isCOD = strtolower($validated['payment_method']) === 'cod';
      $orderStatus = $isCOD ? 'Processed' : 'Pending';
      $paymentStatus = $isCOD ? 'Unpaid' : 'Unpaid';

      // Buat Order
      $order = Order::create([
        'user_id' => null,
        'token_order' => $token,
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

      // Payment COD
      Payment::create([
        'order_id' => $order->id,
        'payment_method' => strtoupper($validated['payment_method']),
        'payment_status' => $paymentStatus,
        'payment_proof' => null,
      ]);

      DB::commit();

      return response()->json([
        'success' => true,
        'redirect_url' => route('guest.checkout.success', $token),
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
   * Success page setelah guest selesai order
   */
  public function success($token)
  {
    $order = Order::where('token_order', $token)->with('payment')->firstOrFail();
    return view('guest.checkout.success', compact('order'));
  }
}
