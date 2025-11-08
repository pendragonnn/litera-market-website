@extends('layouts.app')

@section('title', 'Litera Market | Guest Cart')

@section('content')
  <div class="max-w-6xl mx-auto py-10 px-4" x-data="guestCart()" x-init="loadCart()">

    {{-- Title --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex items-center justify-between">
      <div class="flex items-center gap-2">
        🛒 <span>My Cart (Guest)</span>
      </div>

      <template x-if="items.length !== 0">
        <a href="{{ route('home') }}"
          class="px-4 py-2 bg-[#1B3C53] text-white text-sm rounded-md hover:bg-[#163246] transition">
          ← Back to Homepage
        </a>
      </template>
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
            <div class="relative bg-gray-50 border border-gray-300 rounded-md p-3 shadow-sm hover:shadow-md transition"
              :class="{ 'opacity-80': item.stock <= 0 }">

              {{-- Out of Stock Badge --}}
              <template x-if="item.stock <= 0">
                <span
                  class="absolute top-3 left-3 bg-red-600 text-white text-xs px-2 py-1 rounded-md font-semibold shadow">
                  Out of Stock
                </span>
              </template>

              <img :src="item.image || 'https://placehold.co/200x250?text=No+Image'"
                class="w-full h-[450px] object-cover rounded-md border mb-3">

              <h3 class="text-[#1B3C53] font-semibold text-lg truncate" x-text="item.title"></h3>
              <p class="text-gray-600 text-sm mb-2">
                Rp <span x-text="Number(item.price).toLocaleString('id-ID')"></span>
              </p>

              {{-- Quantity Input --}}
              <div class="flex items-center gap-2 mb-2">
                <label class="text-sm text-gray-700 font-medium">Quantity:</label>
                <input type="number" min="1" :max="item.stock || 99" x-model.number="item.quantity"
                  class="w-16 border border-gray-300 rounded-md text-center text-sm py-1 px-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"
                  :disabled="item.stock <= 0">
                <button @click="updateItem(index)"
                  class="px-3 py-1 text-xs bg-[#1B3C53] text-white rounded hover:bg-[#163246]"
                  :disabled="item.stock <= 0">
                  Update
                </button>
              </div>

              {{-- Delete Button --}}
              <button type="button"
                class="w-full mt-1 px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition"
                @click="openDeleteModal(index)">
                Delete
              </button>
            </div>
          </template>
        </div>

        {{-- Cart Summary --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-8 gap-4">
          <div>
            <h2 class="text-xl font-bold text-[#1B3C53]">
              Total: Rp <span x-text="totalPrice.toLocaleString('id-ID')"></span>
            </h2>

            {{-- Warning if out of stock --}}
            <template x-if="hasOutOfStock()">
              <p class="text-red-600 text-sm mt-2 font-medium">
                ⚠️ Some items are out of stock. Please remove them before checkout.
              </p>
            </template>
          </div>

          <div class="flex items-center gap-3">
            {{-- Clear Cart --}}
            <button type="button"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium"
              @click="openClearModal()">
              Clear Cart
            </button>

            {{-- Checkout --}}
            <a href="{{ route('guest.checkout.index') }}"
              class="px-5 py-2 bg-[#1B3C53] text-white rounded-md text-sm font-medium transition"
              :class="{ 'opacity-50 cursor-not-allowed': hasOutOfStock() }" @click="if (hasOutOfStock()) { 
              showStockWarning('Some items are out of stock. Please remove them before checkout.'); 
              $event.preventDefault(); 
            }">
              Checkout as Guest
            </a>

          </div>
        </div>
      </div>
    </template>

    {{-- Delete Modal --}}
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

    {{-- Clear Modal --}}
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
          const item = this.items[index];
          item.quantity = Math.max(1, item.quantity);

          // ✅ Validate stock before saving
          if (item.stock && item.quantity > item.stock) {
            this.showStockWarning(`${item.title}`, item.stock);
            item.quantity = item.stock;
          }

          this.saveCart();
        },

        showStockWarning(title, maxStock = null) {
          const toast = document.createElement('div');
          toast.className =
            "fixed bottom-6 right-6 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg px-4 py-3 shadow-md text-sm font-medium z-[9999] animate-fadeIn";
          toast.innerHTML = maxStock
            ? `⚠️ The quantity for <strong>${title}</strong> exceeds available stock (${maxStock}).<br>It has been adjusted automatically.`
            : `⚠️ ${title}`;
          document.body.appendChild(toast);
          setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
          }, 3500);
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
        },

        hasOutOfStock() {
          // Anggap item tanpa properti stock = 1 (aman)
          return this.items.some(i => Number(i.stock ?? 1) <= 0);
        },

      }));
    });
  </script>
@endpush