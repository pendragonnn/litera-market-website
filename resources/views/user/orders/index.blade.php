@extends('layouts.app')

@section('content')
<div 
  class="max-w-6xl mx-auto py-10 px-4" 
  x-data="{
    tab: new URLSearchParams(window.location.search).get('tab') || 'All', 
    modal: null, 
    orderId: null,
    setTab(status) {
      this.tab = status;
      const url = new URL(window.location.href);
      url.searchParams.set('tab', status);
      window.history.pushState({}, '', url);
    }
  }"
  x-init="
    $watch('tab', value => {
      const url = new URL(window.location.href);
      url.searchParams.set('tab', value);
      window.history.replaceState({}, '', url);
    });
  "
>

  <h1 class="text-2xl font-bold text-[#1B3C53] mb-8 flex items-center gap-2">
    📦 <span>My Orders</span>
  </h1>

  {{-- === Notification Toast === --}}
  @if (session('success') || session('error'))
    <div 
      x-data="{ show: true }"
      x-show="show"
      x-transition
      class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 px-5 py-3 rounded-lg shadow-lg border text-sm font-medium"
      :class="`{{ session('success') ? 'bg-green-50 border-green-300 text-green-800' : 'bg-red-50 border-red-300 text-red-800' }}`"
    >
      {{ session('success') ?? session('error') }}
      <button class="ml-3 text-xs underline" @click="show = false">Close</button>
    </div>
  @endif

  {{-- === Tabs === --}}
  <div class="flex flex-wrap gap-2 mb-6">
    @foreach (['All', 'Pending', 'Processed', 'Shipped', 'Delivered', 'Cancelled'] as $status)
      <button 
        @click="setTab('{{ $status }}')"
        :class="tab === '{{ $status }}' 
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
          <button type="submit" class="px-4 py-1 bg-[#1B3C53] text-white rounded text-sm hover:bg-[#163246]">Upload</button>
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
          <button type="submit" class="px-4 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">Yes, Cancel</button>
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
          <button type="submit" class="px-4 py-1 bg-[#1B3C53] text-white rounded text-sm hover:bg-[#163246]">Yes</button>
        </div>
      </form>
    </div>
  </div>

  {{-- === Review Modal === --}}
  <div x-show="modal === 'review'" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
      <h3 class="text-lg font-semibold text-[#1B3C53] mb-4">Write a Review</h3>
      <form method="POST" x-bind:action="`/user/orders/${orderId}/review`">
        @csrf
        <div class="flex items-center gap-2 mb-4">
          @for ($i = 1; $i <= 5; $i++)
            <label>
              <input type="radio" name="rating" value="{{ $i }}" class="hidden">
              <span class="text-2xl cursor-pointer hover:text-yellow-400">⭐</span>
            </label>
          @endfor
        </div>
        <textarea name="comment" rows="3" placeholder="Write your experience..."
          class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4 text-sm focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"></textarea>
        <div class="flex justify-end gap-3">
          <button type="button" @click="modal = null" class="px-3 py-1 border rounded text-sm">Cancel</button>
          <button type="submit" class="px-4 py-1 bg-[#1B3C53] text-white rounded text-sm hover:bg-[#163246]">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
