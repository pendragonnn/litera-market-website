@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
  {{-- Title --}}
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex items-center gap-2">
    🛒 <span>Shopping Cart</span>
  </h1>

  {{-- Cart Items --}}
  @if ($cartItems->isEmpty())
    <div class="text-center text-gray-500 mt-10">
      <p>Your cart is currently empty.</p>
      <a href="{{ route('home') }}" class="inline-block mt-4 px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
        Browse Books
      </a>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-8">
      @foreach ($cartItems as $item)
        <div class="bg-gray-50 border border-gray-300 rounded-md p-3 shadow-sm hover:shadow-md transition">
          <img src="{{ $item->book->image 
            ? asset('storage/' . $item->book->image) 
            : 'https://placehold.co/200x250?text=No+Image' }}" 
            alt="{{ $item->book->title }}" 
            class="w-full h-48 object-cover rounded-md border mb-3">

          <h3 class="text-[#1B3C53] font-semibold text-lg truncate">{{ $item->book->title }}</h3>
          <p class="text-gray-600 text-sm mb-2">Rp {{ number_format($item->book->price, 0, ',', '.') }}</p>

          {{-- Quantity input --}}
          <form action="{{ route('user.cart.update', $item) }}" method="POST" class="flex items-center gap-2 mb-2">
            @csrf
            @method('PUT')
            <input type="number" name="quantity" min="1" value="{{ $item->quantity }}"
              class="w-16 border border-gray-300 rounded-md text-center text-sm py-1 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
            <button type="submit" 
              class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded hover:bg-[#163246]">
              Update
            </button>
          </form>

          {{-- Delete button --}}
          <form action="{{ route('user.cart.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this book from your cart?');">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="w-full mt-1 px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition">
              Delete
            </button>
          </form>
        </div>
      @endforeach
    </div>

    {{-- Cart Summary --}}
    <div class="flex justify-between items-center mt-8">
      <h2 class="text-xl font-bold text-[#1B3C53]">
        Total: Rp {{ number_format($totalPrice, 0, ',', '.') }}
      </h2>

      <div class="flex items-center gap-3">
        {{-- Clear Cart --}}
        <form action="{{ route('user.cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your entire cart?');">
          @csrf
          @method('DELETE')
          <button type="submit" 
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">
            Clear Cart
          </button>
        </form>

        {{-- Checkout --}}
        <a href="#" 
          class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] text-sm font-medium">
          Checkout
        </a>
      </div>
    </div>
  @endif
</div>
@endsection
