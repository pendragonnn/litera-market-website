@extends('layouts.app')

@section('content')
  <div class="max-w-6xl mx-auto py-10 px-4">
    {{-- Title --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex items-center justify-between">
      <div class="flex items-center gap-2">
        🛒 <span>My Cart</span>
      </div>

      @if (!$cartItems->isEmpty())
        <a href="{{ route('home') }}"
          class="px-4 py-2 bg-[#1B3C53] text-white text-sm rounded-md hover:bg-[#163246] transition">
          ← Back to Homepage
        </a>
      @endif
    </h1>

    {{-- Cart Items --}}
    @if ($cartItems->isEmpty())
      <div class="text-center text-gray-500 mt-10">
        <p>Your cart is currently empty.</p>
        <a href="{{ route('home') }}"
          class="inline-block mt-4 px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
          Browse Books
        </a>
      </div>
    @else
      @php
        $hasOutOfStock = false;
      @endphp

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-8">
        @foreach ($cartItems as $item)
          @php
            $isOutOfStock = $item->book->stock <= 0;
            if ($isOutOfStock) {
                $hasOutOfStock = true;
            }
          @endphp

          <div
            class="relative bg-gray-50 border border-gray-300 rounded-md p-3 shadow-sm hover:shadow-md transition @if($isOutOfStock) opacity-80 @endif">
            
            {{-- Out of Stock Label --}}
            @if ($isOutOfStock)
              <span
                class="absolute top-3 left-3 bg-red-600 text-white text-xs px-2 py-1 rounded-md font-semibold shadow">
                Out of Stock
              </span>
            @endif

            <img src="{{ $item->book->image
            ? asset($item->book->image)
            : 'https://placehold.co/200x250?text=No+Image' }}" 
              alt="{{ $item->book->title }}"
              class="w-full h-[450px] object-cover rounded-md border mb-3">

            <h3 class="text-[#1B3C53] font-semibold text-lg truncate">{{ $item->book->title }}</h3>
            <p class="text-gray-600 text-sm mb-2">Rp {{ number_format($item->book->price, 0, ',', '.') }}</p>

            {{-- Quantity input --}}
            <form action="{{ route('user.cart.update', $item) }}" method="POST"
              class="cart-update-form flex items-center gap-2 mb-2"
              data-max-stock="{{ $item->book->stock }}"
              data-book-title="{{ $item->book->title }}">
              @csrf
              @method('PUT')
              <input type="number" name="quantity" min="1" value="{{ $item->quantity }}"
                class="cart-qty w-16 border border-gray-300 rounded-md text-center text-sm py-1 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"
                @if ($isOutOfStock) disabled @endif>
              <button type="submit"
                class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded hover:bg-[#163246]"
                @if ($isOutOfStock) disabled class="opacity-50 cursor-not-allowed" @endif>
                Update
              </button>
            </form>


            {{-- Delete button --}}
            <button type="button"
              class="w-full mt-1 px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition"
              onclick="openDeleteModal('{{ route('user.cart.destroy', $item) }}', '{{ $item->book->title }}')">
              Delete
            </button>
          </div>
        @endforeach
      </div>

      {{-- Cart Summary --}}
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-8 gap-4">
        <div>
          <h2 class="text-xl font-bold text-[#1B3C53]">
            Total: Rp {{ number_format($totalPrice, 0, ',', '.') }}
          </h2>

          {{-- Warning jika ada stok habis --}}
          @if ($hasOutOfStock)
            <p class="text-red-600 text-sm mt-2 font-medium">
              ⚠️ Some items are out of stock. Please remove them before checkout.
            </p>
          @endif
        </div>

        <div class="flex items-center gap-3">
          {{-- Clear Cart --}}
          <button type="button"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium"
            onclick="openClearModal('{{ route('user.cart.clear') }}')">
            Clear Cart
          </button>

          {{-- Checkout --}}
          <a href="{{ $hasOutOfStock ? '#' : route('user.checkout.index') }}"
            class="px-5 py-2 bg-[#1B3C53] text-white rounded-md text-sm font-medium transition
              {{ $hasOutOfStock ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#163246]' }}"
            @if ($hasOutOfStock) onclick="event.preventDefault();" title="Some items are out of stock" @endif>
            Checkout
          </a>
        </div>
      </div>
    @endif
  </div>

  {{-- === Delete Modal === --}}
  <div id="deleteModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-xl w-[90%] max-w-md animate-fadeIn">
      <div class="border-b border-[#d2c1b6]/60 px-4 py-3 flex justify-between items-center">
        <h2 class="font-semibold text-[#1B3C53]">LiteraMarket</h2>
        <button onclick="closeModal('deleteModal')" class="text-[#1B3C53]/60 hover:text-[#1B3C53]">✕</button>
      </div>
      <div class="px-5 py-4 text-center">
        <p class="text-[#1B3C53] mb-5 text-sm">Are you sure you want to remove <span id="deleteItemName"
            class="font-semibold"></span> from your cart?</p>
        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="flex justify-center gap-3">
            <button type="button" onclick="closeModal('deleteModal')"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">Cancel</button>
            <button type="submit"
              class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- === Clear Cart Modal === --}}
  <div id="clearModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-xl w-[90%] max-w-md animate-fadeIn">
      <div class="border-b border-[#d2c1b6]/60 px-4 py-3 flex justify-between items-center">
        <h2 class="font-semibold text-[#1B3C53]">LiteraMarket</h2>
        <button onclick="closeModal('clearModal')" class="text-[#1B3C53]/60 hover:text-[#1B3C53]">✕</button>
      </div>
      <div class="px-5 py-4 text-center">
        <p class="text-[#1B3C53] mb-5 text-sm">Are you sure you want to clear your entire cart?</p>
        <form id="clearForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="flex justify-center gap-3">
            <button type="button" onclick="closeModal('clearModal')"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">Cancel</button>
            <button type="submit"
              class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Clear</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <style>
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .animate-fadeIn {
      animation: fadeIn 0.25s ease-in-out;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
    // === Prevent overstock quantity ===
    document.querySelectorAll('.cart-update-form').forEach(form => {
      form.addEventListener('submit', e => {
        const maxStock = parseInt(form.dataset.maxStock);
        const input = form.querySelector('.cart-qty');
        const qty = parseInt(input.value);
        const title = form.dataset.bookTitle;

        if (qty > maxStock) {
          e.preventDefault(); // stop form submit
          showStockWarning(title, maxStock);
          input.value = maxStock; // auto-reset ke max
        }
      });
    });
  });

    // === Simple warning toast ===
    function showStockWarning(title, maxStock) {
      const toast = document.createElement('div');
      toast.className = "fixed top-6 right-6 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg px-4 py-3 shadow-md text-sm font-medium z-[9999] animate-fadeIn";
      toast.innerHTML = `⚠️ The quantity for <strong>${title}</strong> exceeds available stock (${maxStock}).<br>It has been adjusted automatically.`;

      document.body.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 400);
      }, 3500);
    }

    function openDeleteModal(actionUrl, itemName) {
      const modal = document.getElementById('deleteModal');
      const form = document.getElementById('deleteForm');
      const name = document.getElementById('deleteItemName');
      form.action = actionUrl;
      name.textContent = `"${itemName}"`;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function openClearModal(actionUrl) {
      const modal = document.getElementById('clearModal');
      const form = document.getElementById('clearForm');
      form.action = actionUrl;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeModal(id) {
      const modal = document.getElementById(id);
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  </script>
@endpush
