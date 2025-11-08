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

    // === 1️⃣ Ambil semua buku yang punya review, termasuk yang soft deleted ===
    $books = Book::withTrashed() // ⬅️ include buku yang sudah dihapus
      ->select(
        'books.id',
        'books.title',
        'books.author',
        'books.image',
        DB::raw('AVG(reviews.rating) as avg_rating'),
        DB::raw('COUNT(reviews.id) as review_count')
      )
      ->leftJoin('order_items', 'books.id', '=', 'order_items.book_id')
      ->leftJoin('reviews', 'order_items.id', '=', 'reviews.order_item_id')
      ->when($ratingFilter, function ($query) use ($ratingFilter) {
        $query->where('reviews.rating', $ratingFilter);
      })
      ->groupBy('books.id', 'books.title', 'books.author', 'books.image')
      ->orderByDesc('review_count')
      ->paginate(8);

    // === 2️⃣ Summary cards (include buku yang soft deleted juga) ===

    $totalReviews = Review::join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
      ->join('books', 'order_items.book_id', '=', 'books.id')
      ->whereNull('books.deleted_at') // opsional: hapus baris ini kalau mau include buku deleted juga
      ->orWhereNotNull('books.deleted_at') // ⬅️ biar tetap ambil buku yang soft deleted
      ->count();

    $averageRating = number_format(
      Review::join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
        ->join('books', 'order_items.book_id', '=', 'books.id')
        ->where(function ($q) {
          $q->whereNull('books.deleted_at')
            ->orWhereNotNull('books.deleted_at');
        })
        ->avg('reviews.rating'),
      1
    );

    $mostReviewedBook = Book::withTrashed()
      ->select('books.title')
      ->leftJoin('order_items', 'books.id', '=', 'order_items.book_id')
      ->leftJoin('reviews', 'order_items.id', '=', 'reviews.order_item_id')
      ->groupBy('books.id', 'books.title')
      ->orderByRaw('COUNT(reviews.id) DESC')
      ->value('books.title');

    $fiveStarCount = Review::join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
      ->join('books', 'order_items.book_id', '=', 'books.id')
      ->where(function ($q) {
        $q->whereNull('books.deleted_at')
          ->orWhereNotNull('books.deleted_at');
      })
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
    $book = Book::withTrashed()->findOrFail($bookId);

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
