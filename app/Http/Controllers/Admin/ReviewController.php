<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
  public function index(Request $request)
  {
    $ratingFilter = $request->get('rating');

    $reviews = \App\Models\Review::with(['orderItem.book', 'orderItem.order.user'])
      ->when($ratingFilter, function ($query) use ($ratingFilter) {
        $query->where('rating', $ratingFilter);
      })
      ->orderByDesc('created_at')
      ->paginate(8);

    // Summary data
    $totalReviews = \App\Models\Review::count();
    $averageRating = number_format(\App\Models\Review::avg('rating'), 1);
    $mostReviewedBook = \App\Models\Book::select('books.title')
      ->join('order_items', 'books.id', '=', 'order_items.book_id')
      ->join('reviews', 'order_items.id', '=', 'reviews.order_item_id')
      ->groupBy('books.id', 'books.title')
      ->orderByRaw('COUNT(reviews.id) DESC')
      ->value('books.title');
    $fiveStarCount = \App\Models\Review::where('rating', 5)->count();

    return view('admin.reviews.index', compact(
      'reviews',
      'ratingFilter',
      'totalReviews',
      'averageRating',
      'mostReviewedBook',
      'fiveStarCount'
    ));
  }
}
