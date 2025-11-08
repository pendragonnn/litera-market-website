@extends('layouts.admin')

@section('breadcrumb', 'Order Detail')

@section('content')
  {{-- Header --}}
  <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
    <h1 class="text-2xl font-bold text-[#1B3C53]">Order Detail</h1>
    <a href="{{ route('admin.orders.index') }}"
      class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 w-full sm:w-auto text-center">
      ← Back to Orders
    </a>
  </div>

  {{-- Order Information --}}
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-semibold text-[#1B3C53] mb-4">Order Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <p><span class="font-medium text-gray-700">Order ID:</span> #{{ $order->id }}</p>
        <p><span class="font-medium text-gray-700">Customer Name:</span> {{ $order->name }}</p>
        <p><span class="font-medium text-gray-700">Phone:</span> {{ $order->phone }}</p>
        <p><span class="font-medium text-gray-700">Address:</span> {{ $order->address }}</p>
      </div>
      <div>
        <p><span class="font-medium text-gray-700">Total Price:</span> Rp
          {{ number_format($order->total_price, 0, ',', '.') }}</p>
        <p><span class="font-medium text-gray-700">Order Status:</span>
          <span class="px-2 py-1 rounded-md text-xs font-semibold
              @if ($order->status === 'Pending') bg-yellow-100 text-yellow-700
              @elseif ($order->status === 'Shipped') bg-blue-100 text-blue-700
              @elseif ($order->status === 'Delivered') bg-green-100 text-green-700
              @elseif ($order->status === 'Cancelled') bg-red-100 text-red-700
              @else bg-gray-100 text-gray-600 @endif">
            {{ ucfirst($order->status) }}
          </span>
        </p>
        <p><span class="font-medium text-gray-700">Payment Status:</span>
          {{ ucfirst($order->payment->payment_status ?? 'N/A') }}
        </p>
        <p><span class="font-medium text-gray-700">Payment Method:</span>
          {{ ucfirst($order->payment->payment_method ?? '-') }}
        </p>
      </div>
    </div>
  </div>

  {{-- Payment Proof --}}
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-semibold text-[#1B3C53] mb-4">Payment Proof</h2>
    @if ($order->payment && $order->payment->payment_proof)
      <div class="flex flex-col md:flex-row items-start gap-4">
        <img src="{{ Str::startsWith($order->payment->payment_proof, 'http')
        ? $order->payment->payment_proof
        : asset('storage/' . $order->payment->payment_proof) }}" alt="Payment Proof"
          class="w-full md:w-64 rounded-md border object-contain">
        <div>
          <p class="text-sm text-gray-600 mb-2">Uploaded by user as proof of payment.</p>
          <a href="{{ Str::startsWith($order->payment->payment_proof, 'http')
        ? $order->payment->payment_proof
        : asset('storage/' . $order->payment->payment_proof) }}" target="_blank"
            class="inline-block px-4 py-2 bg-[#1B3C53] text-white rounded-md text-sm hover:bg-[#163246]">
            View Full Image
          </a>
        </div>
      </div>
    @else
      <p class="text-gray-500 italic">No payment proof uploaded.</p>
    @endif
  </div>

  {{-- Ordered Items --}}
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-semibold text-[#1B3C53] mb-4">Ordered Items</h2>
    <div class="overflow-x-auto w-full">
      <table class="min-w-[800px] w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 border-b border-gray-300 text-gray-700">
          <tr>
            <th class="px-4 py-2 text-left">Book</th>
            <th class="px-4 py-2 text-left">Price</th>
            <th class="px-4 py-2 text-left">Quantity</th>
            <th class="px-4 py-2 text-left">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($order->orderItems as $item)
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2">{{ $item->book->title ?? '-' }}</td>
              <td class="px-4 py-2">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
              <td class="px-4 py-2">{{ $item->quantity }}</td>
              <td class="px-4 py-2 font-medium text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-gray-500">No items found for this order.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Admin Actions --}}
  @if ($order->status === 'Processed' && $order->payment->payment_status === "Awaiting Approval")
    <div class="flex justify-end flex-wrap gap-3">
      <button type="button" onclick="openConfirmModal('{{ route('admin.orders.confirm', $order) }}')"
        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
        Confirm Order
      </button>

      <button type="button" onclick="openRejectModal('{{ route('admin.orders.reject', $order) }}')"
        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
        Reject Order
      </button>
    </div>
  @endif

  {{-- === Confirm Modal === --}}
  <div id="confirmModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm relative overflow-hidden border border-gray-300">
      <button type="button" onclick="closeConfirmModal()"
        class="absolute top-2 right-3 text-gray-400 hover:text-gray-600 text-xl">✕</button>

      <div class="px-6 py-5 text-center">
        <h2 class="text-lg font-semibold text-[#1B3C53] mb-3">Confirm Order</h2>
        <p class="text-gray-600 text-sm mb-6">
          Have you verified the payment proof thoroughly before confirming this order?
        </p>
        <div class="flex justify-center gap-3">
          <button type="button" onclick="closeConfirmModal()"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
            Cancel
          </button>
          <form id="confirmForm" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
              Yes, Confirm
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- === Reject Modal === --}}
  <div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm relative overflow-hidden border border-gray-300">
      <button type="button" onclick="closeRejectModal()"
        class="absolute top-2 right-3 text-gray-400 hover:text-gray-600 text-xl">✕</button>

      <div class="px-6 py-5 text-center">
        <h2 class="text-lg font-semibold text-[#1B3C53] mb-3">Reject Order</h2>
        <p class="text-gray-600 text-sm mb-6">
          Are you sure you want to reject this order? Please make sure you have reviewed the payment proof carefully.
        </p>
        <div class="flex justify-center gap-3">
          <button type="button" onclick="closeRejectModal()"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
            Cancel
          </button>
          <form id="rejectForm" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
              Yes, Reject
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    function openConfirmModal(actionUrl) {
      const modal = document.getElementById('confirmModal');
      const form = document.getElementById('confirmForm');
      form.action = actionUrl;
      modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
      document.getElementById('confirmModal').classList.add('hidden');
    }

    function openRejectModal(actionUrl) {
      const modal = document.getElementById('rejectModal');
      const form = document.getElementById('rejectForm');
      form.action = actionUrl;
      modal.classList.remove('hidden');
    }

    function closeRejectModal() {
      document.getElementById('rejectModal').classList.add('hidden');
    }

    // close modals when clicking the overlay
    document.getElementById('confirmModal').addEventListener('click', (e) => {
      if (e.target === e.currentTarget) closeConfirmModal();
    });
    document.getElementById('rejectModal').addEventListener('click', (e) => {
      if (e.target === e.currentTarget) closeRejectModal();
    });
  </script>
@endpush