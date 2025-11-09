@extends('layouts.app')

@section('title', 'Litera Market | Guest Success Order')

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
      Please save your order information below, you’ll need it to track your order later.
    </p>

    {{-- ✅ Order Summary --}}
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-lg shadow-sm p-6 text-left max-w-md w-full mb-8">
      <h2 class="font-semibold text-lg text-[#1B3C53] mb-3">Order Summary</h2>
      <ul class="text-gray-700 text-sm space-y-2">
        <li><span class="font-semibold">Name:</span> {{ $order->name }}</li>
        <li><span class="font-semibold">Address:</span> {{ $order->address }}</li>
        <li><span class="font-semibold">WhatsApp Number:</span> {{ $order->phone }}</li>
        <li><span class="font-semibold">Payment Method:</span> {{ strtoupper($order->payment->payment_method ?? '-') }}
        </li>
        <li><span class="font-semibold">Order ID:</span> {{ $order->id }}</li>
      </ul>
    </div>

    {{-- ✅ Conditional Info --}}
    @if (strtoupper($order->payment->payment_method ?? '') === 'COD')
      {{-- COD Info --}}
      <div
        class="max-w-md bg-blue-50 border border-blue-200 text-blue-800 text-sm px-5 py-4 rounded-lg shadow-sm mb-8 text-left">
        <p class="font-semibold mb-1">🚚 Your order is being processed!</p>
        <p class="leading-relaxed">
          Please prepare the payment in cash when the courier arrives at your address.
          Our team will ship your order shortly you can track the delivery status using your Order ID.
        </p>
      </div>
    @else
      {{-- Bank Transfer Info --}}
      <div
        class="max-w-md bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm px-5 py-4 rounded-lg shadow-sm mb-8 text-left">
        <p class="font-semibold mb-1">💳 Complete Your Payment</p>
        <p class="leading-relaxed mb-3">
          Please transfer the total amount to one of the bank accounts below and upload your payment proof via the Order
          Tracker.
        </p>
        <ul class="list-disc pl-5 space-y-1 text-xs sm:text-sm">
          <li><span class="font-medium">BCA:</span> 1234567890 — PT Litera Market Indonesia</li>
          <li><span class="font-medium">Mandiri:</span> 9876543210 — PT Litera Market Indonesia</li>
          <li><span class="font-medium">BNI:</span> 5678901234 — PT Litera Market Indonesia</li>
        </ul>
        <p class="mt-3 text-xs italic text-yellow-700">
          ⚠️ Please complete your payment within 24 hours to avoid automatic cancellation.
        </p>
      </div>
    @endif

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