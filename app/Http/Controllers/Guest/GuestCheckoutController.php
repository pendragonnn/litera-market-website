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
      'email' => 'required|email|max:100',
      'phone' => 'required|string|max:20',
      'address' => 'required|string|max:255',
      'payment_method' => 'required|string|max:100',
      'cart' => 'required|array|min:1',
      'cart.*.book_id' => 'required|exists:books,id',
      'cart.*.quantity' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();
    try {
      // Generate token unik untuk guest order
      $token = strtoupper(Str::random(10));

      // Buat order utama
      $order = Order::create([
        'user_id' => null,
        'token_order' => $token,
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'address' => $validated['address'],
        'total_price' => 0,
        'status' => 'Pending',
      ]);

      $total = 0;

      // Simpan semua item dari cart dan kurangi stok
      foreach ($validated['cart'] as $item) {
        $book = Book::findOrFail($item['book_id']);

        // Cek stok buku cukup atau tidak
        if ($book->stock < $item['quantity']) {
          throw new \Exception("Insufficient stock for {$book->title}");
        }

        $subtotal = $book->price * $item['quantity'];
        $total += $subtotal;

        // Tambahkan order item
        OrderItem::create([
          'order_id' => $order->id,
          'book_id' => $book->id,
          'quantity' => $item['quantity'],
          'price' => $book->price,
          'subtotal' => $subtotal,
        ]);

        // Kurangi stok buku
        $book->decrement('stock', $item['quantity']);
      }

      // Update total harga di order
      $order->update(['total_price' => $total]);

      // Tambahkan data payment dengan status default Awaiting Approval
      Payment::create([
        'order_id' => $order->id,
        'payment_method' => $validated['payment_method'],
        'payment_status' => 'Awaiting Approval',
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
