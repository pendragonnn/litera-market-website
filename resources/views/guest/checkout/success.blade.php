@extends('layouts.app')

@section('content')
  <div class="flex flex-col items-center justify-center min-h-[70vh] px-4 py-10 text-center">

    {{-- ✅ Success Icon --}}
    <div
      class="bg-green-100 border border-green-300 text-green-700 w-16 h-16 flex items-center justify-center rounded-full mb-5 shadow-sm">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
        class="w-8 h-8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
      </svg>
    </div>

    {{-- ✅ Title --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-2">Order Successful!</h1>
    <p class="text-gray-700 mb-1">
      Thank you for shopping at <span class="font-semibold text-[#1B3C53]">LiteraMarket</span>.
    </p>
    <p class="text-gray-600 mb-6">
      Please save your order information below — you’ll need it to track your order or recover your token later.
    </p>

    {{-- ✅ Order Summary --}}
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-lg shadow-sm p-6 text-left max-w-md w-full mb-8">
      <h2 class="font-semibold text-lg text-[#1B3C53] mb-3">Order Summary</h2>
      <ul class="text-gray-700 text-sm space-y-2">
        <li><span class="font-semibold">Name:</span> {{ $order->name }}</li>
        <li><span class="font-semibold">Address:</span> {{ $order->address }}</li>
        <li><span class="font-semibold">WhatsApp Number:</span> {{ $order->phone }}</li>
        <li><span class="font-semibold">Order ID:</span> {{ $order->id }}</li>

        <li>
          <span class="font-semibold">Order Token:</span>
          <div class="flex items-center gap-2 mt-1">
            <span id="orderToken"
              class="font-mono bg-yellow-100 border border-yellow-300 px-2 py-1 rounded text-[#1B3C53] select-all">
              {{ $order->token_order }}
            </span>

            {{-- 🔘 Copy Button --}}
            <button id="copyBtn" class="bg-[#1B3C53] text-white text-xs px-2 py-1 rounded hover:bg-[#163246] transition">
              Copy
            </button>
          </div>
          <p id="copyMsg" class="text-xs text-green-600 mt-1 hidden">✅ Token copied to clipboard!</p>
          <p class="text-xs text-red-600 mt-1">⚠️ Keep this token and your Order ID safe! Both are required for order tracking or recovery.</p>
        </li>
      </ul>
    </div>

    {{-- ✅ Action Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3">
      <a href="{{ route('home') }}"
        class="px-5 py-2 bg-gray-200 text-[#1B3C53] rounded-md hover:bg-gray-300 transition text-sm font-medium">
        ← Back to Homepage
      </a>

      <a href="{{ route('guest.order.tracker.show', ['token' => $order->token_order]) }}"
        class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium">
        📦 Track My Order
      </a>
    </div>
  </div>

  {{-- ✅ Copy Script --}}
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const copyBtn = document.getElementById('copyBtn');
        const orderToken = document.getElementById('orderToken');
        const copyMsg = document.getElementById('copyMsg');

        copyBtn.addEventListener('click', async () => {
          try {
            await navigator.clipboard.writeText(orderToken.textContent.trim());
            copyMsg.classList.remove('hidden');
            copyMsg.classList.add('block');
            setTimeout(() => copyMsg.classList.add('hidden'), 2000);
          } catch (err) {
            alert('Failed to copy token. Please copy it manually.');
          }
        });
      });
    </script>
  @endpush
@endsection