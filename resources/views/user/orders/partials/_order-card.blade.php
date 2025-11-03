<div class="border border-gray-200 rounded-lg p-4 bg-[#F9F3EF]/60 shadow-sm">
  <div class="flex justify-between items-center mb-3">
    <h3 class="font-semibold text-[#1B3C53] text-sm">Order #{{ $order->id }}</h3>
    <span class="text-xs bg-[#1B3C53] text-white px-2 py-1 rounded-md">{{ ucfirst($order->status) }}</span>
  </div>

  <div class="space-y-2 text-sm text-gray-700">
    @foreach ($order->orderItems as $item)
      <div class="flex justify-between">
        <span>{{ $item->book->title }} × {{ $item->quantity }}</span>
        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
      </div>
    @endforeach
  </div>

  <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-300 text-sm">
    <span class="font-semibold text-[#1B3C53]">
      Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
    </span>

    <div class="flex gap-2">
      {{-- Pending --}}
      @if ($order->status === 'Pending')
        
          <button @click="modal = 'uploadProof'; orderId = {{ $order->id }}"
            class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
            Upload Payment Proof
          </button>
        
        <button @click="modal = 'cancel'; orderId = {{ $order->id }}"
          class="px-3 py-1 text-xs bg-red-600 text-white rounded-md hover:bg-red-700">
          Cancel
        </button>
      @endif

      {{-- Processed --}}
      @if ($order->status === 'Processed')
        <a href="https://wa.me/6281234567890?text=Halo%20admin%2C%20status%20pesanan%20#{{ $order->id }}"
          target="_blank"
          class="px-3 py-1 text-xs bg-green-600 text-white rounded-md hover:bg-green-700">
          Contact Admin
        </a>
        <button @click="modal = 'cancel'; orderId = {{ $order->id }}"
          class="px-3 py-1 text-xs bg-red-600 text-white rounded-md hover:bg-red-700">
          Cancel
        </button>
      @endif

      {{-- Shipped --}}
      @if ($order->status === 'Shipped')
        <button @click="modal = 'complete'; orderId = {{ $order->id }}"
          class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
          Mark Complete
        </button>
      @endif

      {{-- Delivered --}}
      @if ($order->status === 'Delivered')
        <button @click="modal = 'review'; orderId = {{ $order->id }}"
          class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
          Write Review
        </button>
      @endif
    </div>
  </div>
</div>
