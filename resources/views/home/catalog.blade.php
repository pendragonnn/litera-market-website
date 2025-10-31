<section id="ourCollection" class="container mx-auto px-6 mb-16">
  <h3 class="text-center text-2xl font-bold text-[#1B3C53] mb-3">Our Collection</h3>
  <p class="text-center text-gray-600 mb-8">Find a wide selection of interesting books only at LiteraMarket.</p>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    @forelse ($books as $book)
      <div class="border border-gray-400 bg-[#f9f9f9] flex flex-col items-center shadow-sm rounded-md hover:shadow-md transition-all">
        <div class="w-full h-120 border-b border-gray-300 flex justify-center items-center bg-white rounded-t-md">
          <img src="{{ $book->image ?? asset('images/default-book.jpg') }}" alt="{{ $book->title }}"
            class="h-full object-contain">
        </div>

        <div class="w-full px-3 py-3 flex flex-col justify-between flex-grow text-left">
          <div>
            <h4 class="text-sm font-bold text-gray-800 truncate">{{ $book->title }}</h4>
            <p class="text-sm text-gray-600">{{ $book->author }}</p>
          </div>

          <div class="flex justify-between items-center mt-3">
            <div class="flex gap-1">
              <button class="bg-[#002D72] text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-[#001E4D]">
                Detail
              </button>
              <button class="bg-[#1B3C53] text-white text-sm font-medium px-3 py-2 rounded-md hover:bg-[#102a3e]">
                🛒
              </button>
            </div>
            <span class="text-[#C0392B] font-semibold text-sm">
              Rp {{ number_format($book->price ?? 51000, 0, ',', '.') }}
            </span>
          </div>
        </div>
      </div>
    @empty
      <p class="text-center col-span-full text-gray-500">No books found.</p>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="mt-10 flex justify-center">
    {{ $books->links('components.pagination.pagination') }}
  </div>
</section>
