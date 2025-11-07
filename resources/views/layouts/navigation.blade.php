@php
  use App\Models\CartItem;
  use Illuminate\Support\Facades\Auth;

  $cartCount = Auth::check()
    ? CartItem::where('user_id', Auth::id())->sum('quantity')
    : 0;
@endphp

<nav x-data="{ open: false }" class="bg-white shadow-sm" x-init="initCartCount()">
  <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center p-4">
    {{-- === Logo === --}}
    <div class="flex items-center">
      <a href="{{ route('home') }}" class="text-2xl font-bold text-[#1B3C53]">LiteraMarket.</a>
    </div>

    {{-- === Center Menu (Desktop) === --}}
    <div class="hidden md:flex space-x-8">
      <a href="#ourCollection" class="text-gray-700 font-semibold hover:text-[#1B3C53]">Our Collection</a>
      <a href="#aboutUs" class="text-gray-700 font-semibold hover:text-[#1B3C53]">About Us</a>
      <a href="#order" class="text-gray-700 font-semibold hover:text-[#1B3C53]">How to Order</a>
    </div>

    {{-- === Right Section (Desktop) === --}}
    <div class="hidden md:flex items-center gap-4">
      {{-- Category Filter --}}
      <form method="GET" action="{{ route('home') }}">
        <select name="category" id="filterKategori" onchange="this.form.submit()"
          class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]">
          <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
          @foreach ($categories as $category)
            <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
      </form>

      {{-- === Order Tracker / My Orders === --}}
      @guest
        <a href="{{ route('guest.order.tracker.index') }}"
          class="text-gray-700 border border-gray-400 rounded-md px-3 py-1 hover:bg-gray-100 text-sm font-medium">
          📦 Order Tracker
        </a>
      @endguest

      @auth
        <a href="{{ route('user.orders.index') }}"
          class="text-gray-700 border border-gray-400 rounded-md px-3 py-1 hover:bg-gray-100 text-sm font-medium">
          📦 My Orders
        </a>
      @endauth

      {{-- === Cart Icon === --}}
      <a href="{{ auth()->check() ? route('user.cart.index') : route('guest.cart.index') }}"
        class="relative border border-gray-400 rounded-md px-3 py-1 hover:bg-gray-50 transition">
        🛒 Cart
        <span
          class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center cart-count">
          {{ $cartCount }}
        </span>
      </a>

      {{-- === Auth Buttons === --}}
      @guest
        <button onclick="toggleModal('loginModal')"
          class="text-gray-700 border border-gray-400 rounded-md px-3 py-1 hover:bg-gray-100 text-sm font-medium">
          Login
        </button>
        <button onclick="toggleModal('registerModal')"
          class="bg-[#1B3C53] text-white rounded-md px-3 py-1 hover:bg-[#163246] text-sm font-medium">
          Register
        </button>
      @endguest

      @auth
        <x-dropdown align="right" width="48">
          <x-slot name="trigger">
            <button
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-[#1B3C53] focus:outline-none transition">
              <span>{{ Auth::user()->name }}</span>
              <svg class="ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </x-slot>

          <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                Log Out
              </x-dropdown-link>
            </form>
          </x-slot>
        </x-dropdown>
      @endauth
    </div>

    {{-- === Hamburger Menu (Mobile) === --}}
    <button @click="open = !open" class="md:hidden text-gray-700 hover:text-[#1B3C53] focus:outline-none">
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
          stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
          stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>

  {{-- === Mobile Menu === --}}
  <div :class="{ 'block': open, 'hidden': !open }" class="md:hidden border-t border-gray-200">
    <div class="px-4 pt-3 pb-4 space-y-3">
      <a href="#ourCollection" class="block text-gray-700 font-semibold hover:text-[#1B3C53]">Our Collection</a>
      <a href="#aboutUs" class="block text-gray-700 font-semibold hover:text-[#1B3C53]">About Us</a>
      <a href="#order" class="block text-gray-700 font-semibold hover:text-[#1B3C53]">How to Order</a>

      {{-- Mobile Cart --}}
      <a href="{{ auth()->check() ? route('user.cart.index') : route('guest.cart.index') }}"
        class="flex items-center justify-between border border-gray-300 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-50">
        🛒 Cart
        <span
          class="cart-count bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
      </a>

      {{-- === Order Tracker / My Orders (Mobile) === --}}
      @guest
        <a href="{{ route('guest.order.tracker.index') }}"
          class="block border border-gray-300 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-50">
          📦 Order Tracker
        </a>
      @endguest

      @auth
        <a href="{{ route('user.orders.index') }}"
          class="block border border-gray-300 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-50">
          📦 My Orders
        </a>
      @endauth

      <hr class="border-gray-300">

      {{-- Auth Buttons --}}
      @guest
        <button onclick="toggleModal('loginModal')"
          class="w-full text-left text-gray-700 border border-gray-300 rounded-md px-3 py-2 hover:bg-gray-100">
          Login
        </button>
        <button onclick="toggleModal('registerModal')"
          class="w-full text-left bg-[#1B3C53] text-white rounded-md px-3 py-2 hover:bg-[#163246]">
          Register
        </button>
      @endguest

      @auth
        <div class="pt-2 border-t border-gray-300">
          <p class="text-sm text-gray-700 mb-1">{{ Auth::user()->name }}</p>
          <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
              Log Out
            </x-dropdown-link>
          </form>
        </div>
      @endauth
    </div>
  </div>
</nav>

@push('scripts')
  <script>
    async function fetchUserCartCount() {
      try {
        const response = await fetch('/api/cart/count', {
          headers: { 'Accept': 'application/json' },
          credentials: 'same-origin'
        });
        if (response.ok) {
          const data = await response.json();
          updateNavCartCount(data.count);
        }
      } catch (e) {
        console.warn('⚠️ Failed to fetch cart count', e);
      }
    }

    function initCartCount() {
      const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
      if (isLoggedIn) {
        fetchUserCartCount();
      } else {
        const cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
        updateNavCartCount(cart.reduce((sum, i) => sum + (i.quantity || 0), 0));
      }
    }

    function updateNavCartCount(count) {
      document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = count;
        el.classList.add('animate-bounce');
        setTimeout(() => el.classList.remove('animate-bounce'), 500);
      });
    }

    // Event listener biar sinkron dari halaman cart / katalog
    window.addEventListener('cart-updated', (e) => {
      updateNavCartCount(e.detail.count);
    });
  </script>
@endpush