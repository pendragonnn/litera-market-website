@extends('layouts.app')

@section('content')
  <div class="min-h-screen">
    <div class="bg-white border shadow-lg rounded-sm sm:m-10">
      @include('layouts.navigation')
      {{-- Hero Section --}}
      <div class="px-4 my-5">
        <img src="{{ asset('images/banner.png') }}" alt="LiteraMarket Banner"
          class="w-full rounded-3xl shadow-md object-cover">
      </div>

      {{-- Search --}}
      <div class="max-w-2xl mx-auto mb-10">
        <form method="GET" action="{{ route('home') }}">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Search books by title..."
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-[#d2c1b6]/50 focus:border-[#d2c1b6]">
        </form>
      </div>

      {{-- Book Catalog --}}
      <section id="collection" class="container mx-auto px-6 mb-16">
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

      {{-- Tentang Kami --}}
      <section id="tentang" class="bg-[#1B3C53] text-white text-center py-10 px-6">
        <div class="max-w-3xl mx-auto">
          <h3 class="text-2xl font-semibold mb-3">About Us</h3>
          <p class="leading-relaxed">LiteraMarket is an e-commerce platform that offers a wide selection of books from
            various genres. We believe that literacy is the key to opening up new insights and opportunities for everyone.
          </p>
        </div>
      </section>

      {{-- Cara Order --}}
      <section id="order" class="bg-gray-100 text-center py-10 px-6">
        <div class="max-w-3xl mx-auto">
          <h3 class="text-2xl font-semibold text-[#1B3C53] mb-3">How to Order</h3>
          <p class="text-gray-600 leading-relaxed">Select the books you like, add them to your cart, then make your payment
            using your preferred method. We will process your order quickly and securely.</p>
        </div>
      </section>

      {{-- Footer --}}
      <footer class="text-center py-6 border-t text-sm text-gray-600">
        &copy; {{ date('Y') }} LiteraMarket. - Literation and Market
      </footer>
    </div>
  </div>
@endsection

{{-- === Modal Section === --}}
@section('modals')
  {{-- === Login Modal === --}}
  <div id="loginModal" class="fixed inset-0 z-50 hidden bg-black/40 flex justify-center items-start pt-16">
    <div class="bg-white border border-gray-800 rounded-lg w-full max-w-sm relative overflow-hidden">
      <button onclick="toggleModal('loginModal')"
        class="absolute top-2 right-3 text-gray-400 hover:text-gray-600">✕</button>

      {{-- Header --}}
      <div class="border-b border-gray-800 px-6 py-3">
        <h2 class="text-lg font-semibold text-gray-800">Login</h2>
      </div>

      {{-- Form --}}
      <form id="loginForm" method="POST" action="{{ route('login') }}" class="px-6 py-4 border-b border-gray-400">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" name="email" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" required
            class="w-full border border-gray-800 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
      </form>

      {{-- Footer --}}
      <div class="flex justify-end px-6 py-3">
        <button type="submit" form="loginForm"
          class="bg-[#002D72] text-white text-sm font-medium rounded-md px-4 py-2 hover:bg-[#001E4D]">
          Login
        </button>
      </div>
    </div>
  </div>

  {{-- === Register Modal (Bordered Version) === --}}
  <div id="registerModal" class="fixed inset-0 z-50 hidden bg-black/40 flex justify-center items-start pt-16">
    <div class="bg-white border border-gray-800 rounded-lg w-full max-w-sm relative overflow-hidden">
      <button onclick="toggleModal('registerModal')"
        class="absolute top-2 right-3 text-gray-400 hover:text-gray-600">✕</button>

      {{-- Header --}}
      <div class="border-b border-gray-800 px-6 py-3">
        <h2 class="text-lg font-semibold text-gray-800">Register</h2>
      </div>

      {{-- Form --}}
      <form id="registerForm" method="POST" action="{{ route('register') }}" class="px-6 py-4 border-b border-gray-400">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
          <input type="text" name="name" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" name="email" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <input type="password" name="password_confirmation" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
      </form>

      {{-- Footer --}}
      <div class="flex justify-end px-6 py-3">
        <button type="submit" form="registerForm"
          class="bg-[#007E33] text-white text-sm font-medium rounded-md px-4 py-2 hover:bg-[#006029]">
          Register
        </button>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    function toggleModal(id) {
      const modal = document.getElementById(id);
      modal.classList.toggle('hidden');
    }
    function switchModal(from, to) {
      document.getElementById(from).classList.add('hidden');
      document.getElementById(to).classList.remove('hidden');
    }
  </script>
@endpush