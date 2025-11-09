@extends('layouts.app')

@section('title', 'Litera Market | Order Tracker')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] px-4 py-10 text-center">

  {{-- Header --}}
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-4">🔍 Track Your Order</h1>
  <p class="text-gray-600 mb-8 max-w-md">
    Enter your <span class="font-semibold text-[#1B3C53]">Order ID</span> and 
    <span class="font-semibold text-[#1B3C53]">Phone Number</span> to securely view your order status.
  </p>

  {{-- Form --}}
  <form 
    x-data="{ loading: false }" 
    @submit="loading = true" 
    action="{{ route('guest.order.tracker.find') }}" 
    method="POST"
    class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-sm p-6 w-full max-w-md text-left transition-all"
  >
    @csrf

    {{-- Order ID --}}
    <div class="mb-4">
      <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Order ID</label>
      <input 
        type="text" 
        name="order_id" 
        id="order_id" 
        placeholder="Enter your Order ID"
        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
        required
      >
    </div>

    {{-- Phone --}}
    <div class="mb-4">
      <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
      <input 
        type="text" 
        name="phone" 
        id="phone" 
        placeholder="Enter your WhatsApp Number"
        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
        required
      >
    </div>

    {{-- Error Message --}}
    @if(session('error'))
      <p class="text-sm text-red-600 mb-3">{{ session('error') }}</p>
    @endif

    {{-- Submit Button --}}
    <button 
      type="submit"
      x-bind:disabled="loading"
      class="w-full px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium disabled:opacity-60"
    >
      <span x-show="!loading">Track Order</span>
      <span x-show="loading">⏳ Checking...</span>
    </button>
  </form>

  <a href="{{ route('home') }}" class="mt-6 text-[#1B3C53] hover:underline text-sm font-medium">
    ← Back to Homepage
  </a>
</div>
@endsection
