@extends('layouts.app')

@section('content')
  <div class="max-w-6xl mx-auto py-10 px-4" x-data="guestCart()" x-init="loadCart()">

    {{-- Title --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex items-center gap-2">
      🛒 <span>My Cart (Guest)</span>
    </h1>

    {{-- Empty Cart --}}
    <template x-if="items.length === 0">
      <div class="text-center text-gray-500 mt-10">
        <p>Your cart is currently empty.</p>
        <a href="{{ route('home') }}"
          class="inline-block mt-4 px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
          Browse Books
        </a>
      </div>
    </template>

    {{-- Cart Items --}}
    <template x-if="items.length > 0">
      <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-8">
          <template x-for="(item, index) in items" :key="item.book_id">
            <div class="bg-gray-50 border border-gray-300 rounded-md p-3 shadow-sm hover:shadow-md transition">
              <img :src="item.image || 'https://placehold.co/200x250?text=No+Image'"
                class="w-full h-[450px] object-cover rounded-md border mb-3">

              <h3 class="text-[#1B3C53] font-semibold text-lg truncate" x-text="item.title"></h3>
              <p class="text-gray-600 text-sm mb-2">Rp <span x-text="Number(item.price).toLocaleString('id-ID')"></span></p>

              {{-- Quantity input --}}
              <div class="flex items-center gap-2 mb-2">
                <input type="number" min="1" x-model.number="item.quantity"
                  class="w-16 border border-gray-300 rounded-md text-center text-sm py-1 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
                <button @click="updateItem(index)"
                  class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded hover:bg-[#163246]">
                  Update
                </button>
              </div>

              {{-- Delete button --}}
              <button type="button"
                class="w-full mt-1 px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition"
                @click="openDeleteModal(index)">
                Delete
              </button>
            </div>
          </template>
        </div>

        {{-- Cart Summary --}}
        <div class="flex justify-between items-center mt-8">
          <h2 class="text-xl font-bold text-[#1B3C53]">
            Total: Rp <span x-text="totalPrice.toLocaleString('id-ID')"></span>
          </h2>

          <div class="flex items-center gap-3">
            {{-- Clear Cart --}}
            <button type="button"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium"
              @click="openClearModal()">
              Clear Cart
            </button>

            {{-- Checkout --}}
            <a href="#"
              class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] text-sm font-medium">
              Checkout as Guest
            </a>
          </div>
        </div>
      </div>
    </template>
  </div>

  {{-- === Delete Confirmation Modal === --}}
  <div id="deleteModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-xl w-[90%] max-w-md animate-fadeIn">
      <div class="border-b border-[#d2c1b6]/60 px-4 py-3 flex justify-between items-center">
        <h2 class="font-semibold text-[#1B3C53]">LiteraMarket</h2>
        <button @click="closeModal('deleteModal')" class="text-[#1B3C53]/60 hover:text-[#1B3C53]">✕</button>
      </div>
      <div class="px-5 py-4 text-center">
        <p class="text-[#1B3C53] mb-5 text-sm">
          Are you sure you want to remove <span id="deleteItemName" class="font-semibold"></span> from your cart?
        </p>
        <div class="flex justify-center gap-3">
          <button type="button" @click="closeModal('deleteModal')"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">Cancel</button>
          <button @click="confirmDelete()"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Delete</button>
        </div>
      </div>
    </div>
  </div>

  {{-- === Clear Cart Confirmation Modal === --}}
  <div id="clearModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-xl w-[90%] max-w-md animate-fadeIn">
      <div class="border-b border-[#d2c1b6]/60 px-4 py-3 flex justify-between items-center">
        <h2 class="font-semibold text-[#1B3C53]">LiteraMarket</h2>
        <button @click="closeModal('clearModal')" class="text-[#1B3C53]/60 hover:text-[#1B3C53]">✕</button>
      </div>
      <div class="px-5 py-4 text-center">
        <p class="text-[#1B3C53] mb-5 text-sm">Are you sure you want to clear your entire cart?</p>
        <div class="flex justify-center gap-3">
          <button type="button" @click="closeModal('clearModal')"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">Cancel</button>
          <button @click="confirmClear()"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">Clear</button>
        </div>
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
    document.addEventListener('alpine:init', () => {
      Alpine.data('guestCart', () => ({
        items: [],
        totalPrice: 0,
        deleteIndex: null,

        loadCart() {
          this.items = JSON.parse(localStorage.getItem('guest_cart') || '[]');
          this.calculateTotal();
          this.updateNavCartCount();
        },

        saveCart() {
          localStorage.setItem('guest_cart', JSON.stringify(this.items));
          this.calculateTotal();
          this.updateNavCartCount();
        },

        updateItem(index) {
          this.items[index].quantity = Math.max(1, this.items[index].quantity);
          this.saveCart();
        },

        openDeleteModal(index) {
          this.deleteIndex = index;
          const item = this.items[index];
          document.getElementById('deleteItemName').textContent = `"${item.title}"`;
          const modal = document.getElementById('deleteModal');
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        },

        confirmDelete() {
          if (this.deleteIndex !== null) {
            this.items.splice(this.deleteIndex, 1);
            this.saveCart();
          }
          this.closeModal('deleteModal');
        },

        openClearModal() {
          const modal = document.getElementById('clearModal');
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        },

        confirmClear() {
          this.items = [];
          localStorage.removeItem('guest_cart');
          this.calculateTotal();
          this.updateNavCartCount();
          this.closeModal('clearModal');
        },

        closeModal(id) {
          const modal = document.getElementById(id);
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        },

        calculateTotal() {
          this.totalPrice = this.items.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        },

        updateNavCartCount() {
          const count = this.items.reduce((sum, i) => sum + i.quantity, 0);
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count } }));
        }
      }));
    });
  </script>
@endpush
