@extends('layouts.app')

@section('content')
  <div class="max-w-3xl mx-auto py-10 px-4">

    {{-- Header --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-6 flex items-center gap-2">
      📦 <span>Order Details</span>
    </h1>

    {{-- === Notification Toast (Consistent with Cart Modal) === --}}
    @if (session('success') || session('error'))
      <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 w-[90%] max-w-md
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
      <p class="text-sm text-gray-700 mb-2"><span class="font-semibold">Method:</span>
        {{ $order->payment->payment_method }}</p>
      <p class="text-sm text-gray-700 mb-4"><span class="font-semibold">Status:</span>
        {{ $order->payment->payment_status }}</p>

      @if ($order->payment->payment_proof)
        <div class="mb-4">
          <p class="text-sm text-gray-700 mb-1 font-medium">Uploaded Proof:</p>
          <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" alt="Payment Proof"
            class="max-h-60 rounded-md border">
        </div>

        <p class="text-sm text-gray-600 italic">
          🕓 Your payment proof has been uploaded and is now being processed by our admin team.
        </p>
      @elseif (in_array($order->payment->payment_status, ['Awaiting Approval', 'Pending Approval']))
        <form action="{{ route('guest.order.tracker.upload', $order->token_order) }}" method="POST"
          enctype="multipart/form-data">
          @csrf
          <label class="block text-sm font-medium text-gray-700 mb-1">Upload Payment Proof</label>
          <input type="file" name="payment_proof" accept="image/*" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 text-sm">
          <button type="submit" class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] text-sm font-medium">
            Upload Proof
          </button>
        </form>
      @endif
    </div>

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

      <a href="{{ route('guest.order.tracker.index') }}"
        class="px-5 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">
        Back to Tracker
      </a>
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