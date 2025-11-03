@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] px-4 py-10 text-center">

  {{-- Success Icon --}}
  <div class="bg-green-100 border border-green-300 text-green-700 w-16 h-16 flex items-center justify-center rounded-full mb-5">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8">
      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
    </svg>
  </div>

  {{-- Title --}}
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-2">Payment Successful!</h1>
  <p class="text-gray-700 mb-1">Thank you for shopping at <span class="font-semibold text-[#1B3C53]">LiteraMarket</span>.</p>
  <p class="text-gray-600 mb-6">Your order is now being processed.</p>

  {{-- Order Summary --}}
  <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-lg shadow-sm p-6 text-left max-w-md w-full mb-8">
    <h2 class="font-semibold text-lg text-[#1B3C53] mb-3">Order Summary</h2>
    <ul class="text-gray-700 text-sm space-y-2">
      <li><span class="font-semibold">Name:</span> {{ $order->name }}</li>
      <li><span class="font-semibold">Address:</span> {{ $order->address }}</li>
      <li><span class="font-semibold">WhatsApp Number:</span> {{ $order->phone }}</li>
      <li><span class="font-semibold">Payment Method:</span> {{ $order->payment->payment_method }}</li>
    </ul>
  </div>

  {{-- Back to Home --}}
  <a href="{{ route('home') }}" 
    class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium">
    Back to Homepage
  </a>
</div>
@endsection
