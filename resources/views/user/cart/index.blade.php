@extends('layouts.user')

@section('content')
  <div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-6">My Cart</h1>

    @if (session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
        {{ session('success') }}
      </div>
    @endif

    @if ($cartItems->isEmpty())
      <p class="text-gray-500 text-center mt-10">Your cart is empty.</p>
    @else
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 border-b">
            <tr class="text-left">
              <th class="p-3">Book</th>
              <th class="p-3">Price</th>
              <th class="p-3">Quantity</th>
              <th class="p-3">Subtotal</th>
              <th class="p-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($cartItems as $item)
              <tr class="border-b hover:bg-gray-50">
                {{-- Book info --}}
                <td class="p-3 flex items-center gap-3">
                  <img src="{{ $item->book->image ? asset('storage/' . $item->book->image) : 'https://placehold.co/60x80?text=No+Image' }}"
                    class="w-12 h-16 object-cover rounded border">
                  <span class="font-medium text-[#1B3C53]">{{ $item->book->title }}</span>
                </td>

                <td class="p-3">Rp {{ number_format($item->book->price, 0, ',', '.') }}</td>

                {{-- Quantity update form --}}
                <td class="p-3">
                  <form action="{{ route('user.cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('PUT')
                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                      class="w-16 border border-gray-300 rounded-md text-center py-1 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
                    <button type="submit"
                      class="text-xs px-3 py-1 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">Update</button>
                  </form>
                </td>

                {{-- Subtotal --}}
                <td class="p-3 font-medium text-gray-700">
                  Rp {{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}
                </td>

                {{-- Remove item --}}
                <td class="p-3 text-center">
                  <form action="{{ route('user.cart.destroy', $item) }}" method="POST"
                    onsubmit="return confirm('Remove this item from cart?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="px-3 py-1 text-xs bg-red-600 text-white rounded-md hover:bg-red-700">Remove</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Footer section: total + clear button --}}
      <div class="flex justify-between items-center mt-6">
        <form action="{{ route('user.cart.clear') }}" method="POST"
          onsubmit="return confirm('Are you sure you want to clear your cart?')">
          @csrf
          @method('DELETE')
          <button type="submit"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">Clear Cart</button>
        </form>

        <div class="text-right">
          <p class="text-gray-600">Total:</p>
          <h3 class="text-xl font-bold text-[#1B3C53]">Rp {{ number_format($totalPrice, 0, ',', '.') }}</h3>
          <a href="#" {{-- nanti ganti ke route checkout --}}
            class="mt-3 inline-block bg-[#1B3C53] text-white px-4 py-2 rounded-md hover:bg-[#163246] transition">
            Proceed to Checkout
          </a>
        </div>
      </div>
    @endif
  </div>
@endsection
