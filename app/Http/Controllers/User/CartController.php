<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Hitung total harga semua item di cart
        $totalPrice = $cartItems->sum(fn($item) => $item->book->price * $item->quantity);

        return view('user.cart.index', compact('cartItems', 'totalPrice'));
    }

    /**
     * Add a book to the user's cart.
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::firstOrNew([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
        ]);

        $cartItem->quantity += $request->quantity;
        $cartItem->save();

        return redirect()->route('user.cart.index')->with('success', 'Book added to cart!');
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

        return redirect()->route('user.cart.index')->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove an item from the cart.
     */
    public function destroy(CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $cartItem->delete();

        return redirect()->route('user.cart.index')->with('success', 'Item removed from cart.');
    }

    /**
     * Helper to make sure users only modify their own cart.
     */
    protected function authorizeCartItem(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
