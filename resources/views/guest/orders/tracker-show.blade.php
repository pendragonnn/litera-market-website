@extends('layouts.app')

@section('title', 'Litera Market | Order Details')

@section('content')
  <div class="max-w-3xl mx-auto py-10 px-4">

    {{-- Header --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-6 flex justify-between items-center gap-2">
      <span>📦 Order Details</span>

      <a href="{{ route('guest.order.tracker.index') }}"
        class="px-5 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">
        ← Back to Tracker
      </a>
    </h1>

    {{-- === Notification Toast (Consistent with Cart Modal) === --}}
    @if (session('success') || session('error'))
      <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 w-[90%] max-w-md
                                                                              rounded-xl shadow-xl border border-[#d2c1b6]/70 bg-[#F9F3EF]
                                                                              text-[#1B3C53] text-sm font-medium px-5 py-4 flex justify-between items-center">

        {{-- Message --}}
        <span>{{ session('success') ?? session('error') }}</span>

        {{-- Close Button --}}
        <button class="ml-3 text-[#1B3C53]/60 hover:text-[#1B3C53] font-bold text-sm" @click="show = false">
          ✕
        </button>
      </div>
    @endif

    {{-- Info Box --}}
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-lg p-6 mb-6 shadow-sm">
      <h2 class="font-semibold text-lg text-[#1B3C53] mb-3">Order Information</h2>
      <ul class="text-sm text-gray-700 space-y-2">
        <li><span class="font-semibold">Token:</span> {{ $order->token_order }}</li>
        <li><span class="font-semibold">Name:</span> {{ $order->name }}</li>
        <li><span class="font-semibold">Phone:</span> {{ $order->phone }}</li>
        <li><span class="font-semibold">Address:</span> {{ $order->address }}</li>
        <li><span class="font-semibold">Status:</span>
          <span class="font-semibold {{ 
                                          $order->status === 'Cancelled' ? 'text-red-600' :
    ($order->status === 'Delivered' ? 'text-green-700' : 'text-yellow-700')
                                        }}">
            {{ ucfirst($order->status) }}
          </span>
        </li>
        <li><span class="font-semibold">Total:</span> Rp {{ number_format($order->total_price, 0, ',', '.') }}</li>
      </ul>
    </div>

    {{-- Order Items --}}
    <div class="border border-gray-200 rounded-lg p-5 mb-6 bg-white shadow-sm">
      <h3 class="font-semibold text-[#1B3C53] mb-3">Items</h3>
      <div class="divide-y divide-gray-200 text-sm">
        @foreach ($order->orderItems as $item)
          <div class="flex justify-between py-2">
            <span>{{ $item->book->title }} × {{ $item->quantity }}</span>
            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Payment Info --}}
    <div class="border border-gray-200 rounded-lg p-5 bg-white shadow-sm mb-6">
      <h3 class="font-semibold text-[#1B3C53] mb-3">Payment Information</h3>
      <p class="text-sm text-gray-700 mb-2">
        <span class="font-semibold">Method:</span> {{ $order->payment->payment_method }}
      </p>
      <p class="text-sm text-gray-700 mb-4">
        <span class="font-semibold">Status:</span> {{ $order->payment->payment_status }}
      </p>

      {{-- === Payment Section === --}}
      {{-- === COD Case === --}}
      @if ($order->payment && strtoupper($order->payment->payment_method) === 'COD')
        <div class="bg-blue-50 border border-blue-300 rounded-md p-4 mb-4 text-sm text-blue-800">
          <p class="font-semibold mb-1">💵 Cash on Delivery</p>
          <p>Your order is being processed and will be shipped soon. Please prepare the payment in cash upon delivery.</p>
        </div>
      @else
        @if ($order->payment)
        {{-- {{ dd($order->payment->payment_method) }} --}}
          @if($order->payment->payment_method === "Transfer" && $order->status !== "Cancelled" && ($order->payment->payment_status === "Unpaid" || $order->payment->payment_status === "Rejected"))
          {{-- 💳 Universal Bank Transfer Info --}}
          <div class="mb-8 bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-lg shadow-sm p-5 text-sm text-[#1B3C53]">
            <div class="flex items-start gap-3">
              <div class="text-lg">💳</div>
              <div>
                <h2 class="font-semibold text-[#1B3C53] mb-1">Bank Transfer Information</h2>
                <p class="text-[13px] leading-relaxed text-gray-700 mb-3">
                  If you selected <span class="font-medium text-[#1B3C53]">Bank Transfer</span> as your payment method,
                  please make your payment to one of the accounts below and upload the payment proof in your order details.
                </p>
                <ul class="list-disc pl-5 space-y-1 text-[13px]">
                  <li><span class="font-medium">Bank BCA:</span> 1234567890 — <span class="italic">PT Litera Market
                      Indonesia</span></li>
                  <li><span class="font-medium">Bank Mandiri:</span> 9876543210 — <span class="italic">PT Litera Market
                      Indonesia</span></li>
                  <li><span class="font-medium">Bank BNI:</span> 5678901234 — <span class="italic">PT Litera Market
                      Indonesia</span></li>
                </ul>
                <p class="mt-3 text-xs text-gray-600 italic">
                  ⚠️ Please complete your payment within 24 hours to avoid automatic cancellation.
                </p>
              </div>
            </div>
          </div>
          @endif
          {{-- 🚫 CASE 0: Order dibatalkan oleh user --}}
          @if ($order->status === 'Cancelled')
            <div class="bg-red-50 border border-red-300 rounded-md p-4 mb-4 text-sm text-red-700">
              <p class="font-semibold mb-1">❌ Order Cancelled</p>
              <p>This order has been cancelled. You can no longer upload or manage payment proofs.</p>
            </div>

            {{-- 🧾 CASE 1: Sudah upload proof --}}
          @elseif ($order->payment->payment_proof)

            {{-- 🕓 CASE 1a: Masih menunggu approval --}}
            @if ($order->payment->payment_status === 'Awaiting Approval')
              <div class="bg-yellow-50 border border-yellow-300 rounded-md p-4 mb-4 text-sm text-yellow-800">
                <p class="font-semibold mb-1">⏳ Payment proof under review</p>
                <p>If your payment proof hasn't been verified within <span class="font-semibold text-yellow-800">24 hours</span>,
                  please contact our admin through WhatsApp for faster confirmation.</p>
              </div>

              {{-- ✅ CASE 1b: Sudah di-approve --}}
            @elseif ($order->payment->payment_status === 'Paid')
              <div class="bg-green-50 border border-green-300 rounded-md p-4 mb-4 text-sm text-green-800">
                <p class="font-semibold mb-1">✅ Payment confirmed!</p>
                <p>Your payment has been successfully verified by our admin team.<br>
                  Thank you for completing your purchase!</p>
              </div>

              {{-- ❌ CASE 1c: Bukti pembayaran ditolak oleh admin --}}
            @elseif ($order->payment->payment_status === 'Rejected')
              <div class="bg-red-50 border border-red-300 rounded-md p-4 mb-4 text-sm text-red-800">
                <p class="font-semibold mb-1">❌ Payment proof rejected</p>
                <p>Your payment proof was not approved by our admin team.<br>
                  Please upload a new valid proof below to continue processing your order.</p>

                {{-- ⚠️ Additional Note --}}
                <div class="mt-5 bg-yellow-50 border border-yellow-300 rounded-md p-3 text-xs text-yellow-800">
                  ⚠️ <span class="font-semibold">Important:</span> If you do not complete your payment within
                  <span class="font-semibold">1 day</span>, your order will be automatically cancelled by our admin.
                </div>

                {{-- Form re-upload proof (karena status balik ke Pending) --}}
                <form action="{{ route('guest.order.tracker.upload', $order->token_order) }}" method="POST"
                  enctype="multipart/form-data" class="mt-3">
                  @csrf
                  <input type="file" name="payment_proof" accept="image/*" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 text-sm">
                  <button type="submit"
                    class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] text-sm font-medium">
                    Re-upload Proof
                  </button>
                </form>
              </div>
            @endif

            {{-- 💸 CASE 2: Belum upload payment proof sama sekali --}}
          @else
            <form action="{{ route('guest.order.tracker.upload', $order->token_order) }}" method="POST"
              enctype="multipart/form-data">
              @csrf
              <label class="block text-sm font-medium text-gray-700 mb-1">Upload Payment Proof</label>
              <input type="file" name="payment_proof" accept="image/*" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 text-sm">
              <button type="submit" class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] text-sm font-medium">
                Upload Proof
              </button>

              {{-- ⚠️ Additional Note --}}
              <div class="mt-5 bg-yellow-50 border border-yellow-300 rounded-md p-3 text-xs text-yellow-800">
                ⚠️ <span class="font-semibold">Important:</span> If you do not complete your payment within
                <span class="font-semibold">1 day</span>, your order will be automatically cancelled by our admin.
              </div>
            </form>
          @endif
        @endif
      @endif
    </div>


    {{-- === Review Info Notice for Guests === --}}
    @if ($order->status === 'Delivered')
      <div class="my-6 bg-blue-50 border border-blue-300 rounded-md p-4 text-sm text-blue-800">
        <p class="font-semibold mb-1">💬 Want to review your books?</p>
        <p>
          Thank you for completing your order! To leave a review for your purchased books,
          please create an account or log in for your next purchase.
        </p>
      </div>
    @endif

    {{-- === Processed Actions === --}}
    @if ($order->status === 'Processed')
      <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-start">
        {{-- Contact Admin via WhatsApp --}}
        @php
          $adminNumber = '6281234567890';
          $waMessage = urlencode("Halo admin, saya ingin menanyakan status pembayaran untuk pesanan dengan ID #{$order->id} atas nama {$order->name}. Mohon dicek kembali bukti pembayaran saya. Terima kasih 🙏");
        @endphp

        <a href="https://wa.me/{{ $adminNumber }}?text={{ $waMessage }}" target="_blank"
          class="inline-flex items-center gap-1 px-5 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition">
          Contact Admin
        </a>

        {{-- Cancel Order --}}
        <button onclick="openModal('cancelModal')"
          class="px-5 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
          Cancel
        </button>
      </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3">
      @if ($order->status === 'Pending')
        <button onclick="openModal('cancelModal')"
          class="px-5 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
          Cancel Order
        </button>
      @endif

      @if ($order->status === 'Shipped')
        <button onclick="openModal('completeModal')"
          class="px-5 py-2 bg-green-700 text-white rounded-md hover:bg-green-800 text-sm font-medium">
          Mark as Completed
        </button>
      @endif
    </div>
  </div>

  {{-- === Cancel Modal === --}}
  <div id="cancelModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-[#F9F3EF] rounded-xl shadow-xl w-[90%] max-w-md p-6">
      <h2 class="text-lg font-semibold text-[#1B3C53] mb-3">Cancel Order</h2>
      <p class="text-sm text-gray-700 mb-5">Are you sure you want to cancel this order?</p>
      <div class="flex justify-end gap-3">
        <button onclick="closeModal('cancelModal')" class="px-4 py-2 bg-gray-200 rounded-md text-sm">No</button>
        <form action="{{ route('guest.order.tracker.cancel', $order->token_order) }}" method="POST">
          @csrf
          @method('PATCH')
          <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm">Yes, Cancel</button>
        </form>
      </div>
    </div>
  </div>

  {{-- === Complete Modal === --}}
  <div id="completeModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-[#F9F3EF] rounded-xl shadow-xl w-[90%] max-w-md p-6">
      <h2 class="text-lg font-semibold text-[#1B3C53] mb-3">Mark as Completed</h2>
      <p class="text-sm text-gray-700 mb-5">Confirm this order as completed?</p>
      <div class="flex justify-end gap-3">
        <button onclick="closeModal('completeModal')" class="px-4 py-2 bg-gray-200 rounded-md text-sm">Cancel</button>
        <form action="{{ route('guest.order.tracker.complete', $order->token_order) }}" method="POST">
          @csrf
          @method('PATCH')
          <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-md text-sm">Yes, Complete</button>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
      }

      function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
      }
    </script>
  @endpush
@endsection