@extends('layouts.app')

@section('content')
  <div class="max-w-4xl mx-auto py-10 px-4" x-data="guestCheckout()" x-init="loadCart()">
    {{-- Title --}}
    <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex justify-between items-center gap-2">
      <span>💳 Guest Checkout</span>

      {{-- 🔙 Back to Cart Button --}}
      <a href="{{ route('guest.cart.index') }}"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium transition">
        ← Back to Cart
      </a>
    </h1>

    {{-- Order Summary --}}
    <div class="bg-[#F9F3EF] border border-[#d2c1b6] rounded-lg p-5 mb-6 shadow-sm">
      <h2 class="font-semibold text-lg text-[#1B3C53] mb-3">Order Summary</h2>
      <div class="space-y-2 text-sm text-gray-700">
        <template x-for="item in cart" :key="item.book_id">
          <div class="flex justify-between border-b border-gray-200 py-2">
            <span x-text="item.title + ' × ' + item.quantity"></span>
            <span>Rp <span x-text="(item.price * item.quantity).toLocaleString('id-ID')"></span></span>
          </div>
        </template>
      </div>

      <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-300 font-bold text-[#1B3C53]">
        <span>Total</span>
        <span>Rp <span x-text="totalPrice.toLocaleString('id-ID')"></span></span>
      </div>
    </div>

    {{-- Checkout Form --}}
    <form @submit.prevent="submitOrder" class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 space-y-5">
      {{-- Full Name --}}
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" id="name" x-model="form.name"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
          required>
      </div>

      {{-- Email --}}
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" x-model="form.email"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
          required>
      </div>

      {{-- Address --}}
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Full Address</label>
        <textarea id="address" rows="3" x-model="form.address"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
          required></textarea>
      </div>

      {{-- WhatsApp Number --}}
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
        <input type="text" id="phone" x-model="form.phone"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
          required>
      </div>

      {{-- Payment Method --}}
      <div>
        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
        <select id="payment_method" x-model="form.payment_method"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
          required>
          <option value="" disabled selected>-- Choose Payment Method --</option>
          <option value="COD">Cash on Delivery (COD)</option>
          <option value="Transfer">Bank Transfer</option>
        </select>
      </div>

      {{-- Submit --}}
      <div class="pt-4 flex justify-end">
        <button type="submit" :disabled="loading"
          class="px-6 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium disabled:opacity-50">
          <span x-show="!loading">Confirm & Pay</span>
          <span x-show="loading">⏳ Processing...</span>
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('guestCheckout', () => ({
        cart: [],
        totalPrice: 0,
        loading: false,
        form: {
          name: '',
          email: '',
          phone: '',
          address: '',
          payment_method: ''
        },

        loadCart() {
          this.cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
          this.calculateTotal();
        },

        calculateTotal() {
          this.totalPrice = this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        },

        async submitOrder() {
          if (this.cart.length === 0) {
            alert('Your cart is empty.');
            return;
          }

          this.loading = true;
          try {
            const res = await fetch('{{ route('guest.checkout.store') }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").content
              },
              body: JSON.stringify({ ...this.form, cart: this.cart }),
            });

            const data = await res.json();
            if (data.success) {
              localStorage.removeItem('guest_cart');
              window.location.href = data.redirect_url;
            } else {
              alert(data.message || 'Failed to submit order.');
            }
          } catch (e) {
            console.error(e);
            alert('An error occurred while processing your order.');
          } finally {
            this.loading = false;
          }
        }
      }));
    });
  </script>
@endpush