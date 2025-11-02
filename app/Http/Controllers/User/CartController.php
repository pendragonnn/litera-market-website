<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index()
    {
        $cartItems = CartItem::with('book')
            ->where('user_id', Auth::id())
            ->get();

        $totalPrice = $cartItems->sum(fn($item) => $item->book->price * $item->quantity);

        return view('user.cart.index', compact('cartItems', 'totalPrice'));
    }

    /**
     * Add a book to the user's cart.
     */
    public function store(Request $request, Book $book)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem = CartItem::firstOrNew([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
            ]);

            // Jika cart item sudah ada, tambahkan quantity
            if ($cartItem->exists) {
                $cartItem->quantity += $validated['quantity'];
            } else {
                $cartItem->quantity = $validated['quantity'];
            }
            
            $cartItem->save();

            // Hitung total items di cart (sum quantity semua items)
            $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity');

            // ✅ Jika request AJAX, return JSON
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$book->title} has been added to your cart.",
                    'cart_count' => $cartCount,
                    'book_title' => $book->title,
                ], 200);
            }

            // Jika request biasa, redirect
            return redirect()->route('user.cart.index')
                ->with('success', 'Book added to cart!');

        } catch (\Exception $e) {
            Log::error('Cart store error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'book_id' => $book->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add item to cart. Please try again.',
                ], 500);
            }

            return back()->with('error', 'Failed to add to cart.');
        }
    }

    /**
     * Update quantity of an item in the cart.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem->update($validated);

        return redirect()->route('user.cart.index')
            ->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove a single item from the user's cart.
     */
    public function destroy(CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $cartItem->delete();

        return redirect()->route('user.cart.index')
            ->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all items in the user's cart.
     */
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('user.cart.index')
            ->with('success', 'Your cart has been cleared.');
    }

    /**
     * Authorize that the cart item belongs to the authenticated user.
     */
    protected function authorizeCartItem(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}