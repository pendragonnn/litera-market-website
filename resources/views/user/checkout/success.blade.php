@extends('layouts.app')

@section('title', 'Litera Market | User Success Order')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] px-4 py-10 text-center">

  {{-- ✅ Success Icon --}}
  <div class="bg-green-100 border border-green-300 text-green-700 w-16 h-16 flex items-center justify-center rounded-full mb-5">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8">
      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
    </svg>
  </div>

  {{-- ✅ Title & Description --}}
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-2">Order Successful!</h1>
  <p class="text-gray-700 mb-1">
    Thank you for shopping at <span class="font-semibold text-[#1B3C53]">LiteraMarket</span>.
  </p>
  <p class="text-gray-600 mb-6">Your order is now being processed. You can check your order details below.</p>

  {{-- ✅ Order Summary --}}
  <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-md p-6 text-left max-w-md w-full mb-10">
    <h2 class="font-semibold text-lg text-[#1B3C53] mb-4 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#1B3C53]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h6a2 2 0 012 2v12a2 2 0 01-2 2z" />
      </svg>
      Order Summary
    </h2>

    <ul class="text-gray-700 text-sm space-y-2">
      <li><span class="font-semibold text-[#1B3C53]">Name:</span> {{ $order->name }}</li>
      <li><span class="font-semibold text-[#1B3C53]">Address:</span> {{ $order->address }}</li>
      <li><span class="font-semibold text-[#1B3C53]">WhatsApp Number:</span> {{ $order->phone }}</li>
      <li><span class="font-semibold text-[#1B3C53]">Payment Method:</span> {{ $order->payment->payment_method ?? '—' }}</li>
      <li><span class="font-semibold text-[#1B3C53]">Order ID:</span> {{ $order->id ?? '-' }}</li>
    </ul>
  </div>

  {{-- ✅ Action Buttons --}}
  <div class="flex flex-wrap justify-center gap-4 mb-8">
    <a href="{{ route('home') }}" 
      class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium">
      ← Back to Homepage
    </a>

    <a href="{{ route('user.orders.index') }}" 
      class="px-5 py-2 border border-[#1B3C53] text-[#1B3C53] rounded-md hover:bg-[#1B3C53] hover:text-white transition text-sm font-medium">
      🧾 My Orders
    </a>
  </div>

  {{-- ✅ Next Step Instruction --}}
  <div class="max-w-md bg-blue-50 border border-blue-200 text-blue-800 text-sm px-5 py-4 rounded-lg shadow-sm flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mt-0.5 text-blue-600">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m4 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <p class="text-left leading-relaxed">
      <span class="font-semibold">Next step:</span> Please visit <span class="font-semibold text-[#1B3C53]">My Orders</span> to upload your <span class="font-medium">payment proof</span> and confirm your transaction.  
      Your order will be verified by our team within 24 hours after uploading.
    </p>
  </div>

</div>
@endsection
