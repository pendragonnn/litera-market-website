<div class="border border-gray-200 rounded-lg p-4 bg-[#F9F3EF]/60 shadow-sm">
  <div class="flex justify-between items-center mb-3">
    <h3 class="font-semibold text-[#1B3C53] text-sm">Order #{{ $order->id }}</h3>
    <div class="flex items-center gap-2">
      {{-- Badge COD --}}
      @if ($order->payment && strtoupper($order->payment->payment_method) === 'COD')
        <span class="text-[10px] bg-yellow-600 text-white px-2 py-1 rounded-md font-semibold uppercase">COD</span>
      @endif
      <span class="text-xs bg-[#1B3C53] text-white px-2 py-1 rounded-md">
        {{ ucfirst($order->status) }}
      </span>
    </div>
  </div>

  {{-- === Order Items === --}}
  <div class="space-y-4 text-sm text-gray-700">
    @foreach ($order->orderItems as $item)
      <div class="border-b border-gray-200 pb-3 last:border-none">
        {{-- Item info --}}
        <div class="flex justify-between items-center">
          <span class="font-medium text-[#1B3C53]">{{ $item->book->title ?? '' }} × {{ $item->quantity }}</span>
          <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>

        {{-- === Review Section per item === --}}
        @if ($order->status === 'Delivered')
          <div class="mt-2 pl-1">
            @if ($item->review)
              {{-- Sudah ada review --}}
              <div class="bg-white/80 border border-gray-200 rounded-md p-2">
                <div class="flex items-center gap-1 mb-1">
                  @for ($i = 1; $i <= 5; $i++)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4"
                      fill="{{ $i <= $item->review->rating ? '#facc15' : '#e5e7eb' }}">
                      <path
                        d="M12 .75l3.32 6.73 7.43 1.08-5.37 5.23 1.27 7.41L12 17.77 5.35 21.2l1.27-7.41L1.25 8.56l7.43-1.08L12 .75z" />
                    </svg>
                  @endfor
                </div>
                @if ($item->review->comment)
                  <p class="text-gray-700 text-xs italic mb-2">“{{ $item->review->comment }}”</p>
                @endif
                <div class="flex gap-2">
                  {{-- Edit Review --}}
                  <button @click="$dispatch('open-review', { 
                                                          mode:'edit', 
                                                          reviewId: {{ $item->review->id }}, 
                                                          rating: {{ $item->review->rating }}, 
                                                          comment: @js($item->review->comment) 
                                                        }); modal='review';"
                    class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded hover:bg-[#163246]">
                    Edit
                  </button>

                  {{-- Delete Review --}}
                  <button @click="reviewId = {{ $item->review->id }}; modal = 'deleteReview';"
                    class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                  </button>
                </div>
              </div>
            @else
              {{-- Belum ada review --}}
              <button @click="$dispatch('open-review', { 
                                                      mode:'create', 
                                                      orderItemId: {{ $item->id }}, 
                                                      rating: 0, 
                                                      comment: '' 
                                                    }); modal='review';"
                class="mt-2 px-3 py-1 text-xs bg-[#1B3C53] text-white rounded hover:bg-[#163246]">
                Write Review
              </button>
            @endif
          </div>
        @endif
      </div>
    @endforeach
  </div>

  {{-- === Footer Section (total + actions) === --}}
  <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-300 text-sm">
    <span class="font-semibold text-[#1B3C53]">
      Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
    </span>

    <div class="flex flex-wrap gap-2 justify-end">
      {{-- === Pending Actions === --}}
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

      {{-- === Processed Actions === --}}
      @if ($order->status === 'Processed')
        <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-start">
          @php
            $adminNumber = '6281234567890';
            $waMessage = urlencode("Halo admin, saya ingin menanyakan status pembayaran untuk pesanan dengan ID #{$order->id} atas nama {$order->name}.");
          @endphp
          <a href="https://wa.me/{{ $adminNumber }}?text={{ $waMessage }}" target="_blank"
            class="inline-flex items-center gap-1 px-3 py-1 text-xs bg-green-600 text-white rounded-md hover:bg-green-700 transition">
            Contact Admin
          </a>
          <button @click="modal = 'cancel'; orderId = {{ $order->id }}"
            class="px-3 py-1 text-xs bg-red-600 text-white rounded-md hover:bg-red-700 transition">
            Cancel
          </button>
        </div>
      @endif


      {{-- === Shipped Actions === --}}
      @if ($order->status === 'Shipped')
        <button @click="modal = 'complete'; orderId = {{ $order->id }}"
          class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
          Mark Complete
        </button>
      @endif
    </div>
  </div>

  @if ($order->status === 'Processed')
    {{-- === Info for user === --}}
  @if ($order->status === 'Processed')
    @if ($order->payment && strtoupper($order->payment->payment_method) === 'COD')
      {{-- COD INFO --}}
      <div
        class="mt-3 bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-md px-4 py-3 leading-relaxed shadow-sm">
        💡 <span class="font-medium">Info:</span> Your order is being processed and will be shipped soon. Please prepare the payment in cash when the courier arrives.
      </div>
    @else
      {{-- BANK TRANSFER INFO --}}
      <div
        class="mt-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-xs rounded-md px-4 py-3 leading-relaxed shadow-sm">
        ⚠️ <span class="font-medium">Note:</span> If your payment proof hasn't been verified within
        <span class="font-semibold text-[#1B3C53]">24 hours</span>,
        please contact our admin through WhatsApp for faster confirmation.
      </div>
    @endif
  @endif
  @endif
</div>