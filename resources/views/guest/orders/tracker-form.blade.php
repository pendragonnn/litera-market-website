@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] px-4 py-10 text-center">

  {{-- Title --}}
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-4">🔍 Track Your Order</h1>
  <p class="text-gray-600 mb-8 max-w-md">
    Enter your <span class="font-semibold text-[#1B3C53]">Order Token</span> to check your order status and details.
  </p>

  {{-- Form --}}
  <form action="{{ route('guest.order.tracker.find') }}" method="POST"
        class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-sm p-6 w-full max-w-md text-left">
    @csrf
    <div class="mb-4">
      <label for="token_order" class="block text-sm font-medium text-gray-700 mb-1">
        Order Token
      </label>
      <input type="text" name="token_order" id="token_order" placeholder="Enter your token (e.g. 9ABF3X1TQZ)"
             class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
             required>
    </div>

    @if(session('error'))
      <p class="text-sm text-red-600 mb-3">{{ session('error') }}</p>
    @endif

    <div class="flex justify-end">
      <button type="submit"
              class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium">
        Track Order
      </button>
    </div>
  </form>

  {{-- Back to Home --}}
  <a href="{{ route('home') }}" class="mt-6 text-[#1B3C53] hover:underline text-sm font-medium">
    ← Back to Homepage
  </a>
</div>
@endsection
