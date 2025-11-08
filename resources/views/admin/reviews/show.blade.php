@extends('layouts.admin')

@section('breadcrumb', 'Book Reviews')

@section('content')
  <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
    <h1 class="text-2xl font-bold text-[#1B3C53]">Reviews for "{{ $book->title }}"</h1>
    <a href="{{ route('admin.reviews.index') }}"
      class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 w-full sm:w-auto text-center">
      ← Back to Reviews
    </a>
  </div>

  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6 flex flex-col sm:flex-row gap-6">
    <img src="{{ $book->image ? asset($book->image) : 'https://placehold.co/120x160?text=No+Image' }}"
      class="w-28 h-40 object-cover rounded-md border">
    <div>
      <h2 class="text-xl font-semibold text-[#1B3C53]">{{ $book->title }}</h2>
      <p class="text-sm text-gray-600 mb-3">by {{ $book->author }}</p>
      <p class="text-lg text-[#1B3C53] font-semibold">
        ⭐ {{ $averageRating }}/5 ({{ $totalReviews }} Reviews)
      </p>
      <p class="text-sm text-gray-600 mb-3">by {{ $book->description }}</p>
    </div>
  </div>

  @if ($reviews->isEmpty())
    <p class="text-gray-500 text-center mt-10">No reviews for this book yet.</p>
  @else
    <div class="space-y-4">
      @foreach ($reviews as $review)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
          <div class="flex items-center mb-2">
            @for ($i = 1; $i <= 5; $i++)
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" viewBox="0 0 24 24">
                <path
                  d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.781 1.402 8.179L12 18.896l-7.336 3.874 1.402-8.179L.132 9.21l8.2-1.192L12 .587z" />
              </svg>
            @endfor
            <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
          </div>

          <p class="text-gray-700 text-sm mb-2">{{ $review->comment }}</p>
          <div class="text-xs text-gray-500 flex justify-between">
            <span>by {{ $review->orderItem->order->user->name ?? 'Anonymous' }}</span>
            <span>{{ $review->created_at->format('d M Y') }}</span>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if ($reviews->hasPages())
      <div class="mt-3 flex justify-end">
        <div class="dataTables_paginate paging_full_numbers">
          {{-- First Page --}}
          @if ($reviews->onFirstPage())
            <span class="paginate_button first disabled">First</span>
          @else
            <a href="{{ $reviews->url(1) }}" class="paginate_button first">First</a>
          @endif

          {{-- Previous Page --}}
          @if ($reviews->onFirstPage())
            <span class="paginate_button previous disabled">&lt;</span>
          @else
            <a href="{{ $reviews->previousPageUrl() }}" class="paginate_button previous">&lt;</a>
          @endif

          {{-- Page Numbers --}}
          @foreach ($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
            @if ($page == $reviews->currentPage())
              <span class="paginate_button current">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="paginate_button">{{ $page }}</a>
            @endif
          @endforeach

          {{-- Next Page --}}
          @if ($reviews->hasMorePages())
            <a href="{{ $reviews->nextPageUrl() }}" class="paginate_button next">&gt;</a>
          @else
            <span class="paginate_button next disabled">&gt;</span>
          @endif

          {{-- Last Page --}}
          @if ($reviews->hasMorePages())
            <a href="{{ $reviews->url($reviews->lastPage()) }}" class="paginate_button last">Last</a>
          @else
            <span class="paginate_button last disabled">Last</span>
          @endif
        </div>
      </div>
    @endif
  @endif
@endsection

<style>
  .dataTables_paginate {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 4px;
    font-size: 0.875rem;
    margin-top: 1rem;
  }

  .paginate_button {
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 3px;
    color: #1B3C53;
    background-color: #fff;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    min-width: 32px;
    text-align: center;
  }

  .paginate_button:hover {
    background-color: #f3f4f6;
  }

  .paginate_button.current {
    background-color: #1B3C53;
    color: #fff;
    border-color: #1B3C53;
    font-weight: 600;
  }

  .paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .paging_full_numbers {
    display: flex;
    gap: 5px;
  }

  .paging_full_numbers a,
  .paging_full_numbers span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 32px;
  }
</style>