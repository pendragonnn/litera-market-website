@extends('layouts.app')

@section('content')
  <div class="max-w-6xl mx-auto py-10 px-4" x-data="{
                    tab: new URLSearchParams(window.location.search).get('tab') || 'All', 
                    modal: null, 
                    orderId: null,
                    orderItemId: null,
                    reviewMode: 'create',
                    reviewId: null,
                    setTab(status) {
                      this.tab = status;
                      const url = new URL(window.location.href);
                      url.searchParams.set('tab', status);
                      window.history.pushState({}, '', url);
                    }
                  }" x-init="
                    $watch('tab', value => {
                      const url = new URL(window.location.href);
                      url.searchParams.set('tab', value);
                      window.history.replaceState({}, '', url);
                    });
                  ">

    <h1
      class="text-2xl font-bold text-[#1B3C53] mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        📦 <span>My Orders</span>
      </div>

      {{-- Back to Homepage --}}
      <a href="{{ route('home') }}"
        class="px-4 py-2 bg-[#1B3C53] text-white text-sm rounded-md hover:bg-[#163246] transition shadow-sm flex items-center gap-1">
        ← <span>Back to Homepage</span>
      </a>
    </h1>

    {{-- === Notification Toast (Consistent with Cart Modal) === --}}
    @if (session('success') || session('error'))
      <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 w-[90%] max-w-md
                           rounded-xl shadow-xl border border-[#d2c1b6]/70 bg-[#F9F3EF]
                           text-[#1B3C53] text-sm font-medium px-5 py-4 flex justify-between items-center">
        {{-- Message --}}
        <span>{{ session('success') ?? session('error') }}</span>

        {{-- Close Button --}}
        <button class="ml-3 text-[#1B3C53]/60 hover:text-[#1B3C53] font-bold text-sm" @click="show = false">
          ✕
        </button>
      </div>
    @endif

    {{-- === Tabs === --}}
    <div class="flex flex-wrap gap-2 mb-6">
      @foreach (['All', 'Pending', 'Processed', 'Shipped', 'Delivered', 'Cancelled'] as $status)
        <button @click="setTab('{{ $status }}')" :class="tab === '{{ $status }}' 
                                          ? 'bg-[#1B3C53] text-white' 
                                          : 'bg-white text-[#1B3C53] border border-[#1B3C53]'"
          class="px-4 py-2 rounded-md font-medium text-sm transition-all">
          {{ $status }}
        </button>
      @endforeach
    </div>

    {{-- === Tab Content === --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
      {{-- === ALL ORDERS TAB === --}}
      <div x-show="tab === 'All'" x-transition>
        @if (collect($groupedOrders)->flatten(1)->isEmpty())
          <p class="text-gray-500 text-sm text-center py-10">You have no orders yet.</p>
        @else
          <div class="space-y-5">
            @foreach (collect($groupedOrders)->flatten(1) as $order)
              @include('user.orders.partials._order-card', ['order' => $order])
            @endforeach
          </div>
        @endif
      </div>

      {{-- === INDIVIDUAL STATUS TABS === --}}
      @foreach ($groupedOrders as $status => $orders)
        <div x-show="tab === '{{ $status }}'" x-transition>
          @if ($orders->isEmpty())
            <p class="text-gray-500 text-sm text-center py-10">No {{ strtolower($status) }} orders found.</p>
          @else
            <div class="space-y-5">
              @foreach ($orders as $order)
                @include('user.orders.partials._order-card', ['order' => $order, 'status' => $status])
              @endforeach
            </div>
          @endif
        </div>
      @endforeach
    </div>

    {{-- === Upload Proof Modal === --}}
    <div x-show="modal === 'uploadProof'" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
      <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-[#1B3C53] mb-4">Upload Payment Proof</h3>
        <form method="POST" x-bind:action="`/user/orders/${orderId}/upload-proof`" enctype="multipart/form-data">
          @csrf
          <input type="file" name="payment_proof" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4 text-sm">
          <div class="flex justify-end gap-3">
            <button type="button" @click="modal = null" class="px-3 py-1 border rounded text-sm">Cancel</button>
            <button type="submit"
              class="px-4 py-1 bg-[#1B3C53] text-white rounded text-sm hover:bg-[#163246]">Upload</button>
          </div>
        </form>
      </div>
    </div>

    {{-- === Cancel Confirmation Modal === --}}
    <div x-show="modal === 'cancel'" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
      <div class="bg-white w-full max-w-sm rounded-lg shadow-lg p-6 text-center">
        <h3 class="text-lg font-semibold text-[#1B3C53] mb-4">Cancel this order?</h3>
        <form method="POST" x-bind:action="`/user/orders/${orderId}/cancel`">
          @csrf
          @method('PATCH')
          <div class="flex justify-center gap-4">
            <button type="button" @click="modal = null" class="px-3 py-1 border rounded text-sm">No</button>
            <button type="submit" class="px-4 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">Yes,
              Cancel</button>
          </div>
        </form>
      </div>
    </div>

    {{-- === Complete Confirmation Modal === --}}
    <div x-show="modal === 'complete'" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
      <div class="bg-white w-full max-w-sm rounded-lg shadow-lg p-6 text-center">
        <h3 class="text-lg font-semibold text-[#1B3C53] mb-4">Mark this order as delivered?</h3>
        <form method="POST" x-bind:action="`/user/orders/${orderId}/complete`">
          @csrf
          @method('PATCH')
          <div class="flex justify-center gap-4">
            <button type="button" @click="modal = null" class="px-3 py-1 border rounded text-sm">No</button>
            <button type="submit"
              class="px-4 py-1 bg-[#1B3C53] text-white rounded text-sm hover:bg-[#163246]">Yes</button>
          </div>
        </form>
      </div>
    </div>

    {{-- === Review Modal (Create / Edit) === --}}
    <div x-show="modal === 'review'" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40 z-50"
      x-data="{ currentRating: 0, currentComment: '', errorMessage: '' }" @open-review.window="
      reviewMode = $event.detail.mode;
      orderItemId = $event.detail.orderItemId ?? null;
      reviewId = $event.detail.reviewId ?? null;
      currentRating = Number($event.detail.rating ?? 0);
      currentComment = $event.detail.comment ?? '';
      errorMessage = '';
    ">
      <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-[#1B3C53] mb-4"
          x-text="reviewMode === 'create' ? 'Write a Review' : 'Edit Review'"></h3>

        <form method="POST"
          x-bind:action="reviewMode === 'create' ? `/user/reviews/${orderItemId}` : `/user/reviews/${reviewId}`"
          @submit.prevent="
          if (currentRating === 0) {
            errorMessage = 'Please give a rating before submitting your review.';
            return;
          }
          $el.submit();
        ">
          @csrf
          <template x-if="reviewMode === 'edit'">
            <input type="hidden" name="_method" value="PUT">
          </template>

          {{-- Rating stars (SVG) --}}
          <div class="flex items-center gap-1.5 mb-2">
            <template x-for="i in 5" :key="i">
              <button type="button" class="p-1" @click="currentRating = i" aria-label="Set rating">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-7 h-7"
                  :class="i <= currentRating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor">
                  <path
                    d="M12 .75l3.32 6.73 7.43 1.08-5.37 5.23 1.27 7.41L12 17.77 5.35 21.2l1.27-7.41L1.25 8.56l7.43-1.08L12 .75z" />
                </svg>
              </button>
            </template>
          </div>

          {{-- Error message for rating --}}
          <template x-if="errorMessage">
            <p class="text-red-600 text-xs mb-2" x-text="errorMessage"></p>
          </template>

          {{-- Comment (optional) --}}
          <textarea name="comment" rows="3" x-model="currentComment"
            class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4 text-sm focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"
            placeholder="Write your experience (optional)..."></textarea>

          {{-- Ensure rating submitted --}}
          <input type="hidden" name="rating" x-model="currentRating">

          <div class="flex justify-end gap-3">
            <button type="button" @click="modal = null" class="px-3 py-1 border rounded text-sm">Cancel</button>
            <button type="submit" class="px-4 py-1 bg-[#1B3C53] text-white rounded text-sm hover:bg-[#163246]"
              x-text="reviewMode === 'create' ? 'Submit' : 'Update'"></button>
          </div>
        </form>
      </div>
    </div>

    {{-- === Delete Review Confirmation Modal === --}}
    <div x-show="modal === 'deleteReview'" x-cloak
      class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
      <div class="bg-white w-full max-w-sm rounded-lg shadow-lg p-6 text-center border border-[#d2c1b6]/70">
        <h3 class="text-lg font-semibold text-[#1B3C53] mb-4">Delete this review?</h3>
        <p class="text-sm text-[#1B3C53]/80 mb-6">This action cannot be undone.</p>

        <form method="POST" x-bind:action="`/user/reviews/${reviewId}`">
          @csrf
          @method('DELETE')
          <div class="flex justify-center gap-4">
            <button type="button" @click="modal = null" class="px-4 py-1 border rounded text-sm text-[#1B3C53] ">
              Cancel
            </button>

            <button type="submit" class="px-4 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
              Yes, Delete
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection