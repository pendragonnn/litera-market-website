@extends('layouts.admin')

@section('title', 'Admin Panel | Reviews')

@section('breadcrumb', 'Reviews Data Monitoring')

@section('content')
  <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
    <h1 class="text-2xl font-bold text-[#1B3C53]">Books Review Overview</h1>

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
      <h2 class="text-2xl font-bold text-[#1B3C53] mt-1">⭐ {{ $averageRating }}</h2>
    </div>
    <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
      <p class="text-sm text-gray-500">Most Reviewed Book</p>
      <h2 class="text-base font-semibold text-[#1B3C53] mt-1">{{ $mostReviewedBook ?? '—' }}</h2>
    </div>
    <div class="bg-white p-5 rounded-lg shadow border border-gray-200">
      <p class="text-sm text-gray-500">5-Star Reviews</p>
      <h2 class="text-2xl font-bold text-[#1B3C53] mt-1">⭐ {{ $fiveStarCount }}</h2>
    </div>
  </div>

  {{-- Books Cards --}}
  @if ($books->isEmpty())
    <p class="text-gray-500 text-center mt-10">No books with reviews found.</p>
  @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @foreach ($books as $book)
        <div
          class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-5 flex items-start gap-4">
          <img src="{{ $book->image ? asset($book->image) : 'https://placehold.co/100x140?text=No+Image' }}"
            class="w-24 h-32 object-cover rounded-md border">

          <div class="flex flex-col justify-between flex-1">
            <div>
              <h3 class="font-semibold text-[#1B3C53] text-base">{{ $book->title }}</h3>
              <p class="text-sm text-gray-500 mb-2">by {{ $book->author }}</p>

              <div class="flex items-center">
                @for ($i = 1; $i <= 5; $i++)
                  <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    class="w-5 h-5 {{ $i <= round($book->avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}"
                    viewBox="0 0 24 24">
                    <path
                      d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.781 1.402 8.179L12 18.896l-7.336 3.874 1.402-8.179L.132 9.21l8.2-1.192L12 .587z" />
                  </svg>
                @endfor
                <span class="ml-2 text-sm text-gray-600">{{ number_format($book->avg_rating, 1) }}/5</span>
              </div>

              <p class="text-xs text-gray-500 mt-1">{{ $book->review_count }} total reviews</p>
            </div>

            <div class="mt-4">
              <a href="{{ route('admin.reviews.show', $book->id) }}"
                class="inline-block bg-[#1B3C53] text-white px-4 py-2 rounded-md text-sm hover:bg-[#163246] transition">
                View Details
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if ($books->hasPages())
      <div class="mt-3 flex justify-end">
        <div class="dataTables_paginate paging_full_numbers">
          {{-- First Page --}}
          @if ($books->onFirstPage())
            <span class="paginate_button first disabled">First</span>
          @else
            <a href="{{ $books->url(1) }}" class="paginate_button first">First</a>
          @endif

          {{-- Previous --}}
          @if ($books->onFirstPage())
            <span class="paginate_button previous disabled">&lt;</span>
          @else
            <a href="{{ $books->previousPageUrl() }}" class="paginate_button previous">&lt;</a>
          @endif

          {{-- Page Numbers with Ellipsis --}}
          @php
            $current = $books->currentPage();
            $last = $books->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
          @endphp

          {{-- Show first page and ellipsis --}}
          @if ($start > 1)
            <a href="{{ $books->url(1) }}" class="paginate_button">1</a>
            @if ($start > 2)
              <span class="paginate_button disabled">...</span>
            @endif
          @endif

          {{-- Main visible page range --}}
          @for ($page = $start; $page <= $end; $page++)
            @if ($page == $current)
              <span class="paginate_button current">{{ $page }}</span>
            @else
              <a href="{{ $books->url($page) }}" class="paginate_button">{{ $page }}</a>
            @endif
          @endfor

          {{-- Show ellipsis before last page --}}
          @if ($end < $last)
            @if ($end < $last - 1)
              <span class="paginate_button disabled">...</span>
            @endif
            <a href="{{ $books->url($last) }}" class="paginate_button">{{ $last }}</a>
          @endif

          {{-- Next --}}
          @if ($books->hasMorePages())
            <a href="{{ $books->nextPageUrl() }}" class="paginate_button next">&gt;</a>
          @else
            <span class="paginate_button next disabled">&gt;</span>
          @endif

          {{-- Last Page --}}
          @if ($books->hasMorePages())
            <a href="{{ $books->url($books->lastPage()) }}" class="paginate_button last">Last</a>
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