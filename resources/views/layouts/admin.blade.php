<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | LiteraMarket</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
</head>

<body class="bg-gray-50 text-gray-800" x-data="{ sidebarOpen: false }">

  {{-- === Global Toast Notification === --}}
  @if (session('success') || session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
      class="fixed top-5 right-5 z-50 w-[90%] max-w-sm rounded-lg shadow-lg
                  px-4 py-3 border
                  {{ session('success') ? 'bg-green-50 border-green-400 text-green-800' : 'bg-red-50 border-red-400 text-red-800' }}">
      <div class="flex items-center justify-between gap-3">
        <p class="text-sm font-medium">
          {{ session('success') ?? session('error') }}
        </p>
        <button @click="show = false" class="text-lg font-bold text-gray-500 hover:text-gray-700">×</button>
      </div>
    </div>
  @endif

  <div class="flex h-screen">

    {{-- === Sidebar === --}}
    <aside
      class="fixed inset-y-0 left-0 z-40 w-64 bg-[#1B3C53] text-white flex flex-col transform transition-transform duration-300 ease-in-out lg:translate-x-0"
      :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">

      <div class="px-6 py-5 text-2xl font-bold border-b border-white/20 flex items-center justify-between">
        <div>
          LITER<span class="text-[#d2c1b6]">ADMIN</span>
        </div>
        {{-- Close button (visible only on mobile) --}}
        <button @click="sidebarOpen = false"
          class="lg:hidden text-white text-2xl font-bold leading-none focus:outline-none">
          ×
        </button>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
        <a href="{{ route('admin.dashboard') }}"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.dashboard') ? 'bg-[#163246]' : '' }}">
          Dashboard
        </a>
        <a href="{{ route('admin.books.index') }}"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.books.*') ? 'bg-[#163246]' : '' }}">
          Books
        </a>
        <a href="{{ route('admin.categories.index') }}"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.categories.*') ? 'bg-[#163246]' : '' }}">
          Categories
        </a>
        <a href="{{ route('admin.orders.index') }}"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.orders.*') ? 'bg-[#163246]' : '' }}">
          Orders
        </a>
        <a href="{{ route('admin.users.index') }}"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.users.*') ? 'bg-[#163246]' : '' }}">
          Users
        </a>
        <a href="{{ route('admin.reviews.index') }}"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.reviews.*') ? 'bg-[#163246]' : '' }}">
          Messages & Comments
        </a>
        <a href="#" class="block px-4 py-2 rounded-md hover:bg-[#163246]">Settings</a>
      </nav>

      <div class="p-4 border-t border-white/20 text-xs text-center text-gray-300">
        © {{ date('Y') }} LiteraMarket. All rights reserved.
      </div>
    </aside>

    {{-- Overlay for mobile --}}
    <div class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen = false"
      x-transition.opacity>
    </div>

    {{-- === Main Content === --}}
    <div class="flex-1 flex flex-col lg:ml-64 w-screen">
      {{-- Topbar --}}
      <header class="bg-white border-b flex items-center justify-between px-6 py-3 flex-wrap gap-2">
        {{-- Left Section: Hamburger + Breadcrumb --}}
        <div class="flex items-center gap-4 flex-wrap">
          {{-- Hamburger button (visible only on mobile) --}}
          <button @click="sidebarOpen = true" class="text-[#1B3C53] focus:outline-none lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" class="w-7 h-7">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          {{-- Breadcrumb --}}
          <div>
            <p class="text-sm text-gray-500 hidden sm:flex">
              Admin Page > <span class="text-[#1B3C53] font-semibold">@yield('breadcrumb')</span>
            </p>
          </div>
        </div>

        {{-- Right Section: Profile + Logout --}}
        <div class="flex items-center gap-4 py-4">
          {{-- Profile Dropdown --}}
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="inline-flex items-center text-gray-700 hover:text-[#1B3C53] font-medium text-sm focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.779.607 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ Auth::user()->name }}
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
        </div>
      </header>

      {{-- Content --}}
      <main class="flex-1 overflow-y-auto p-6">
        @yield('content')
      </main>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
  @stack('scripts')
</body>
</html>
