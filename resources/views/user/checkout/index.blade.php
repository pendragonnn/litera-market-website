@extends('layouts.app')

@section('content')
  <div class="max-w-4xl mx-auto py-10 px-4">
    {{-- Title --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex justify-between items-center gap-2">
      <span>💳 Checkout</span>

      {{-- 🔙 Back to Cart Button --}}
      <a href="{{ route('guest.cart.index') }}"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium transition">
        ← Back to Cart
      </a>
    </h1>

    {{-- Order Summary --}}
    <div class="bg-[#F9F3EF] border border-[#d2c1b6] rounded-lg p-5 mb-6 shadow-sm">
      <h2 class="font-semibold text-lg text-[#1B3C53] mb-3">Order Summary</h2>
      <div class="space-y-2 text-sm text-gray-700">
        @foreach ($cartItems as $item)
          <div class="flex justify-between border-b border-gray-200 py-2">
            <span>{{ $item->book->title }} × {{ $item->quantity }}</span>
            <span>Rp {{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}</span>
          </div>
        @endforeach
      </div>
      <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-300 font-bold text-[#1B3C53]">
        <span>Total</span>
        <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
      </div>
    </div>

    {{-- Checkout Form --}}
    <form action="{{ route('user.checkout.store') }}" method="POST"
      class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 space-y-5">
      @csrf
      @php $user = Auth::user(); @endphp

      {{-- Full Name --}}
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name_display" id="name" value="{{ $user->name }}" disabled
          class="w-full border border-gray-300 bg-gray-100 rounded-md px-3 py-2 text-gray-700 cursor-not-allowed">
        <input type="hidden" name="name" value="{{ $user->name }}">
      </div>

      {{-- Address --}}
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Full Address</label>
        <textarea name="address" id="address" rows="3" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">{{ old('address', $user->address ?? '') }}</textarea>
      </div>

      {{-- WhatsApp Number --}}
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
      </div>

      {{-- Payment Method --}}
      <div>
        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
        <select name="payment_method" id="payment_method" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
          <option value="" disabled selected>-- Choose Payment Method --</option>
          <option value="COD">Cash on Delivery (COD)</option>
          <option value="Transfer">Bank Transfer</option>
        </select>
      </div>

      {{-- Submit --}}
      <div class="pt-4 flex justify-end">
        <button type="submit"
          class="px-6 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium">
          Confirm & Pay
        </button>
      </div>
    </form>
  </div>
@endsection