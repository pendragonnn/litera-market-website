<nav x-data="{ open: false }" class="bg-white">
    <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center p-4">
        {{-- Logo --}}
        <div class="flex items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-[#1B3C53]">LiteraMarket.</a>
        </div>

        {{-- Center Menu --}}
        <div class="hidden md:flex space-x-8">
            <a href="#ourCollection" class="text-gray-700 font-semibold hover:text-[#1B3C53]">Our Collection</a>
            <a href="#aboutUs" class="text-gray-700 font-semibold hover:text-[#1B3C53]">About Us</a>
            <a href="#order" class="text-gray-700 font-semibold hover:text-[#1B3C53]">How to Order</a>
        </div>

        {{-- Right Section --}}
        <div class="hidden md:flex items-center gap-4">
            {{-- Dropdown Kategori --}}
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

            {{-- Ikon Keranjang --}}
            <a href="{{ Route::has('cart.index') ? route('cart.index') : '#' }}"
                class="relative border border-gray-400 rounded-md px-3 py-1">
                🛒
                <span
                    class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center cart-count">0</span>
            </a>

            {{-- Tombol Auth --}}
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
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-[#1B3C53] focus:outline-none transition ease-in-out duration-150">
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
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @endauth


        </div>
        {{-- Hamburger Mobile --}}
        <button @click="open = ! open" class="md:hidden text-gray-700 hover:text-[#1B3C53] focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="md:hidden border-t border-gray-200">
        <div class="px-4 pt-3 pb-4 space-y-3">
            {{-- Navigasi utama --}}
            <a href="#ourCollection" class="block text-gray-700 font-semibold hover:text-[#1B3C53]">Our Collection</a>
            <a href="#aboutUs" class="block text-gray-700 font-semibold hover:text-[#1B3C53]">About Us</a>
            <a href="#order" class="block text-gray-700 font-semibold hover:text-[#1B3C53]">How to Order</a>

            {{-- 🔽 Dropdown Kategori (Mobile) --}}
            <form method="GET" action="{{ route('home') }}" class="mt-2">
                <label for="filterKategoriMobile" class="block text-sm font-semibold text-gray-700 mb-1">
                    Category
                </label>
                <select name="category" id="filterKategoriMobile" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d2c1b6]">
                    <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <hr class="border-gray-300">

            {{-- Tombol Auth --}}
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
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Log Out
                        </x-dropdown-link>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>