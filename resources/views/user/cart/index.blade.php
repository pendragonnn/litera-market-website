@extends('layouts.app')

@section('content')
  <div class="max-w-6xl mx-auto py-10 px-4">
    {{-- Title --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex items-center gap-2">
      🛒 <span>Shopping Cart</span>
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
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-8">
        @foreach ($cartItems as $item)
          <div class="bg-gray-50 border border-gray-300 rounded-md p-3 shadow-sm hover:shadow-md transition">
            <img src="{{ $item->book->image
            ? asset($item->book->image)
            : 'https://placehold.co/200x250?text=No+Image' }}" alt="{{ $item->book->title }}"
              class="w-full h-[450px] object-cover rounded-md border mb-3">

            <h3 class="text-[#1B3C53] font-semibold text-lg truncate">{{ $item->book->title }}</h3>
            <p class="text-gray-600 text-sm mb-2">Rp {{ number_format($item->book->price, 0, ',', '.') }}</p>

            {{-- Quantity input --}}
            <form action="{{ route('user.cart.update', $item) }}" method="POST" class="flex items-center gap-2 mb-2">
              @csrf
              @method('PUT')
              <input type="number" name="quantity" min="1" value="{{ $item->quantity }}"
                class="w-16 border border-gray-300 rounded-md text-center text-sm py-1 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
              <button type="submit" class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded hover:bg-[#163246]">
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
      <div class="flex justify-between items-center mt-8">
        <h2 class="text-xl font-bold text-[#1B3C53]">
          Total: Rp {{ number_format($totalPrice, 0, ',', '.') }}
        </h2>

        <div class="flex items-center gap-3">
          {{-- Clear Cart --}}
          <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium"
            onclick="openClearModal('{{ route('user.cart.clear') }}')">
            Clear Cart
          </button>

          {{-- Checkout --}}
          <a href="{{ route('user.checkout.index') }}"
            class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] text-sm font-medium">
            Checkout
          </a>
        </div>
      </div>
    @endif
  </div>

  {{-- === Delete Confirmation Modal === --}}
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

  {{-- === Clear Cart Confirmation Modal === --}}
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