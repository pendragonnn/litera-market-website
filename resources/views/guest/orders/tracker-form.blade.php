@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] px-4 py-10 text-center" x-data="orderTracker()">

  {{-- Title --}}
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-4">🔍 Track Your Order</h1>
  <p class="text-gray-600 mb-8 max-w-md">
    Enter your <span class="font-semibold text-[#1B3C53]">Order Token</span> to check your order status and details.
  </p>

  {{-- Form --}}
  <form action="{{ route('guest.order.tracker.find') }}" method="POST"
        class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-sm p-6 w-full max-w-md text-left">
    @csrf
    <div class="mb-4">
      <label for="token_order" class="block text-sm font-medium text-gray-700 mb-1">
        Order Token
      </label>
      <input type="text" name="token_order" id="token_order" placeholder="Enter your token (e.g. 9ABF3X1TQZ)"
             class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]"
             required>
    </div>

    @if(session('error'))
      <p class="text-sm text-red-600 mb-3">{{ session('error') }}</p>
    @endif

    <div class="flex justify-between items-center">
      <button type="button" @click="openForgetModal"
              class="text-[#1B3C53] hover:underline text-sm font-medium">
        Forgot Token?
      </button>
      <button type="submit"
              class="px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium">
        Track Order
      </button>
    </div>
  </form>

  {{-- Back to Home --}}
  <a href="{{ route('home') }}" class="mt-6 text-[#1B3C53] hover:underline text-sm font-medium">
    ← Back to Homepage
  </a>

  {{-- === Forget Token Modal === --}}
  <div x-show="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
       x-transition>
    <div class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-lg shadow-lg w-[90%] max-w-md p-6 text-left relative"
         @click.away="closeModal">

      {{-- Close --}}
      <button @click="closeModal" class="absolute top-3 right-4 text-[#1B3C53]/70 hover:text-[#1B3C53] font-bold text-lg">
        ✕
      </button>

      <h2 class="text-lg font-semibold text-[#1B3C53] mb-4">🔑 Retrieve Your Token</h2>

      {{-- Form --}}
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Order ID</label>
          <input type="text" x-model="form.id_order" placeholder="Enter your Order ID"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#d2c1b6]">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
          <input type="text" x-model="form.phone" placeholder="Enter your WhatsApp Number"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#d2c1b6]">
        </div>

        <button @click="findToken" :disabled="loading"
                class="w-full px-5 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246] transition text-sm font-medium disabled:opacity-50">
          <span x-show="!loading">Find My Token</span>
          <span x-show="loading">⏳ Searching...</span>
        </button>
      </div>

      {{-- Result Section --}}
      <template x-if="result !== null">
        <div class="mt-5 border-t border-[#d2c1b6]/70 pt-4">
          <template x-if="result.found">
            <div>
              <p class="text-sm text-gray-700 mb-2">Here’s your token:</p>
              <div class="flex items-center gap-2">
                <span id="foundToken" class="font-mono bg-yellow-100 border border-yellow-300 px-2 py-1 rounded text-[#1B3C53]">
                  <span x-text="result.token"></span>
                </span>
                <button @click="copyToken" class="bg-[#1B3C53] text-white text-xs px-2 py-1 rounded hover:bg-[#163246] transition">
                  Copy
                </button>
              </div>
              <p id="copyMsg" class="text-xs text-green-600 mt-1 hidden">✅ Token copied to clipboard!</p>
            </div>
          </template>

          <template x-if="!result.found">
            <p class="text-sm text-red-600">❌ Order not found. Please check your input.</p>
          </template>
        </div>
      </template>
    </div>
  </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('orderTracker', () => ({
    showModal: false,
    loading: false,
    form: { id_order: '', phone: '' },
    result: null,

    openForgetModal() {
      this.showModal = true;
      this.result = null;
    },
    closeModal() {
      this.showModal = false;
    },

    async findToken() {
      if (!this.form.id_order || !this.form.phone) {
        alert('Please fill in both fields.');
        return;
      }
      this.loading = true;
      try {
        const res = await fetch("{{ route('guest.order.tracker.findToken') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(this.form)
        });
        const data = await res.json();
        if (data.success) {
          this.result = { found: true, token: data.token_order };
        } else {
          this.result = { found: false };
        }
      } catch (err) {
        console.error(err);
        this.result = { found: false };
      } finally {
        this.loading = false;
      }
    },

    async copyToken() {
      const tokenText = this.result.token;
      try {
        await navigator.clipboard.writeText(tokenText);
        const msg = document.getElementById('copyMsg');
        msg.classList.remove('hidden');
        setTimeout(() => msg.classList.add('hidden'), 2000);
      } catch (e) {
        alert('Failed to copy token.');
      }
    }
  }));
});
</script>
@endpush
@endsection
