@extends('layouts.admin')
{{-- {{ dd($reviews->toArray()) }} --}}

@section('content')
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#1B3C53]">User Reviews</h1>

    <form method="GET" class="flex items-center gap-2">
      <select name="rating"
        class="border border-gray-300 rounded-md text-sm px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
        <option value="">All Ratings</option>
        @for ($i = 5; $i >= 1; $i--)
          <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
            {{ $i }} Stars
          </option>
        @endfor
      </select>
      <button type="submit" class="bg-[#1B3C53] text-white px-3 py-2 rounded-md text-sm hover:bg-[#163246] transition">
        Filter
      </button>
    </form>
  </div>

  {{-- Summary Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
      <p class="text-sm text-gray-500">Total Reviews</p>
      <h2 class="text-2xl font-bold text-[#1B3C53] mt-1">{{ $totalReviews }}</h2>
    </div>

    <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
      <p class="text-sm text-gray-500">Average Rating</p>
      <h2 class="text-2xl font-bold text-[#1B3C53] mt-1">
        ⭐ {{ $averageRating }}
      </h2>
    </div>

    <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
      <p class="text-sm text-gray-500">Most Reviewed Book</p>
      <h2 class="text-base font-semibold text-[#1B3C53] mt-1">
        {{ $mostReviewedBook ?? '—' }}
      </h2>
    </div>

    <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
      <p class="text-sm text-gray-500">5-Star Reviews</p>
      <h2 class="text-2xl font-bold text-[#1B3C53] mt-1">
        ⭐ {{ $fiveStarCount }}
      </h2>
    </div>
  </div>

  {{-- Reviews List --}}
  @if ($reviews->isEmpty())
    <p class="text-gray-500 text-center mt-10">No reviews available.</p>
  @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @foreach ($reviews as $review)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition">
          <div class="p-5">
            {{-- Book info --}}
            <div class="flex items-center gap-4 mb-4">
              <img src="{{ $review->orderItem->book->image
                ? asset('storage/' . $review->orderItem->book->image)
                : 'https://placehold.co/80x100?text=No+Image' }}"
                class="w-16 h-20 object-cover rounded-md border">
              <div>
                <h3 class="font-semibold text-[#1B3C53]">
                  {{ $review->orderItem->book->title }}
                </h3>
                <p class="text-sm text-gray-500">
                  by {{ $review->orderItem->book->author ?? 'Unknown' }}
                </p>
              </div>
            </div>

            {{-- Rating --}}
            <div class="flex items-center mb-3">
              @for ($i = 1; $i <= 5; $i++)
                <svg xmlns="http://www.w3.org/2000/svg"
                  class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                  viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.781 1.402 8.179L12 18.896l-7.336 3.874 1.402-8.179L.132 9.21l8.2-1.192L12 .587z" />
                </svg>
              @endfor
              <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
            </div>

            {{-- Review text --}}
            <p class="text-gray-700 text-sm mb-4">{{ $review->comment }}</p>

            {{-- Reviewer info --}}
            <div class="text-xs text-gray-500 flex justify-between">
              <span>by {{ $review->orderItem->order->user->name ?? 'Anonymous' }}</span>
              <span>{{ $review->created_at->format('d M Y') }}</span>
            </div>
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

{{-- Inline Pagination Styles --}}
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
