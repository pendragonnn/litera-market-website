@extends('layouts.admin')
{{-- {{ dd($order->payment->payment_proof) }} --}}

@section('content')
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#1B3C53]">Order Detail</h1>
    <a href="{{ route('admin.orders.index') }}"
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
      ← Back to Orders
    </a>
  </div>

  {{-- Order Information --}}
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-[#1B3C53] mb-4">Order Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <p><span class="font-medium text-gray-700">Order ID:</span> #{{ $order->id }}</p>
        <p><span class="font-medium text-gray-700">Customer Name:</span> {{ $order->name }}</p>
        <p><span class="font-medium text-gray-700">Phone:</span> {{ $order->phone }}</p>
        <p><span class="font-medium text-gray-700">Address:</span> {{ $order->address }}</p>
      </div>
      <div>
        <p><span class="font-medium text-gray-700">Total Price:</span> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        <p><span class="font-medium text-gray-700">Order Status:</span>
          <span class="px-2 py-1 rounded-md text-xs font-semibold
            @if ($order->status === 'pending') bg-yellow-100 text-yellow-700
            @elseif ($order->status === 'shipped') bg-blue-100 text-blue-700
            @elseif ($order->status === 'delivered') bg-green-100 text-green-700
            @elseif ($order->status === 'cancelled') bg-red-100 text-red-700
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
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-[#1B3C53] mb-4">Payment Proof</h2>
    @if ($order->payment && $order->payment->payment_proof)
      <div class="flex flex-col md:flex-row items-start gap-4">
        <img src="{{ Str::startsWith($order->payment->payment_proof, 'http') ? $order->payment->payment_proof : asset('storage/' . $order->payment->payment_proof) }}"
             alt="Payment Proof"
             class="w-full md:w-64 rounded-md border object-contain">
        <div>
          <p class="text-sm text-gray-600 mb-2">Uploaded by user as proof of payment.</p>
          <a href="{{ Str::startsWith($order->payment->payment_proof, 'http') ? $order->payment->payment_proof : asset('storage/' . $order->payment->payment_proof) }}"
             target="_blank"
             class="inline-block px-4 py-2 bg-[#1B3C53] text-white rounded-md text-sm hover:bg-[#163246]">
            View Full Image
          </a>
        </div>
      </div>
    @else
      <p class="text-gray-500 italic">No payment proof uploaded.</p>
    @endif
  </div>

  {{-- Order Items --}}
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-[#1B3C53] mb-4">Ordered Items</h2>
    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
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

  {{-- Admin Actions --}}
  @if ($order->status === 'pending')
    <div class="flex justify-end gap-3">
      <form action="{{ route('admin.orders.confirm', $order) }}" method="POST">
        @csrf
        <button type="submit"
          class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
          Confirm Order
        </button>
      </form>

      <form action="{{ route('admin.orders.reject', $order) }}" method="POST">
        @csrf
        <button type="submit"
          class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
          Reject Order
        </button>
      </form>
    </div>
  @endif
@endsection
