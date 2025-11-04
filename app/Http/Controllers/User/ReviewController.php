<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request, OrderItem $orderItem)
    {
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($orderItem->order->status !== 'Delivered') {
            return back()->with('error', 'You can only review delivered orders.');
        }

        if ($orderItem->review) {
            return back()->with('error', 'You have already reviewed this item.');
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'order_item_id' => $orderItem->id,
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'],
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, Review $review)
    {
        if ($review->orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return back()->with('success', 'Your review has been updated.');
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review)
    {
        if ($review->orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Your review has been deleted.');
    }
}
