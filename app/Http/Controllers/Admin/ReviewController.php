<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
  public function index(Request $request)
  {
    $ratingFilter = $request->get('rating');

    // === 1️⃣ Ambil data buku yang punya review valid ===
    $books = Book::select(
      'books.id',
      'books.title',
      'books.author',
      'books.image',
      DB::raw('AVG(reviews.rating) as avg_rating'),
      DB::raw('COUNT(reviews.id) as review_count')
    )
      ->join('order_items', 'books.id', '=', 'order_items.book_id')
      ->join('reviews', 'order_items.id', '=', 'reviews.order_item_id')
      ->when($ratingFilter, function ($query) use ($ratingFilter) {
        $query->where('reviews.rating', $ratingFilter);
      })
      ->groupBy('books.id', 'books.title', 'books.author', 'books.image')
      ->orderByDesc('review_count')
      ->paginate(8);

    // === 2️⃣ Summary cards (mengacu pada review yang punya book_id valid) ===

    // Total reviews (yang valid)
    $totalReviews = Review::join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
      ->join('books', 'order_items.book_id', '=', 'books.id')
      ->count();

      // dd($totalReviews);

    // Average rating (yang valid)
    $averageRating = number_format(
      Review::join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
        ->join('books', 'order_items.book_id', '=', 'books.id')
        ->avg('reviews.rating'),
      1
    );

    // Most reviewed book (yang valid)
    $mostReviewedBook = Book::select('books.title')
      ->join('order_items', 'books.id', '=', 'order_items.book_id')
      ->join('reviews', 'order_items.id', '=', 'reviews.order_item_id')
      ->groupBy('books.id', 'books.title')
      ->orderByRaw('COUNT(reviews.id) DESC')
      ->value('books.title');

    // Five-star reviews (yang valid)
    $fiveStarCount = Review::join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
      ->join('books', 'order_items.book_id', '=', 'books.id')
      ->where('reviews.rating', 5)
      ->count();

    // === 3️⃣ Kirim ke view ===
    return view('admin.reviews.index', compact(
      'books',
      'ratingFilter',
      'totalReviews',
      'averageRating',
      'mostReviewedBook',
      'fiveStarCount'
    ));
  }


  public function show($bookId)
  {
    $book = Book::findOrFail($bookId);

    $reviews = Review::whereHas('orderItem', function ($query) use ($bookId) {
      $query->where('book_id', $bookId);
    })
      ->with(['orderItem.order.user'])
      ->orderByDesc('created_at')
      ->paginate(8);

    $averageRating = number_format($reviews->avg('rating'), 1);
    $totalReviews = $reviews->total();

    return view('admin.reviews.show', compact('book', 'reviews', 'averageRating', 'totalReviews'));
  }
}
