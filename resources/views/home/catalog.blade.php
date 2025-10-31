<section id="ourCollection" class="container mx-auto px-6 mb-16">
  <h3 class="text-center text-2xl font-bold text-[#1B3C53] mb-3">Our Collection</h3>
  <p class="text-center text-gray-600 mb-8">Find a wide selection of interesting books only at LiteraMarket.</p>

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @forelse ($books as $book)
      <div class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-all">
        <img src="{{ $book->image ?? asset('images/default-book.jpg') }}" alt="{{ $book->title }}"
          class="w-full h-52 object-cover">
        <div class="p-4">
          <h4 class="font-semibold text-lg text-[#1B3C53] truncate">{{ $book->title }}</h4>
          <p class="text-sm text-gray-500">{{ $book->author }}</p>
          <div class="flex justify-between items-center mt-3">
            <span class="text-[#C0392B] font-bold text-sm">Rp {{ number_format($book->price, 0, ',', '.') }}</span>
            <div class="flex gap-2">
              <button class="bg-[#1B3C53] text-white text-xs px-3 py-1 rounded">Detail</button>
              <button class="bg-[#d2c1b6] text-[#1B3C53] text-xs px-3 py-1 rounded">🛒</button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <p class="text-center col-span-full text-gray-500">No books found.</p>
    @endforelse
  </div>
</section>
