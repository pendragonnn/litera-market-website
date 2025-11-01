<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | LiteraMarket</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800">
  <div class="flex h-screen">

    {{-- === Sidebar === --}}
    <aside class="w-64 bg-[#1B3C53] text-white flex flex-col">
      <div class="px-6 py-5 text-2xl font-bold border-b border-white/20">
        LITER<span class="text-[#d2c1b6]">ADMIN</span>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
        <a href="{{ route('admin.dashboard') }}"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.dashboard') ? 'bg-[#163246]' : '' }}">
          Dashboard
        </a>
        <a href="#"
          class="block px-4 py-2 rounded-md hover:bg-[#163246] {{ request()->routeIs('admin.books.*') ? 'bg-[#163246]' : '' }}">
          Data Buku
        </a>
        <a href="#" class="block px-4 py-2 rounded-md hover:bg-[#163246]">Transaksi</a>
        <a href="#" class="block px-4 py-2 rounded-md hover:bg-[#163246]">Pengguna</a>
        <a href="#" class="block px-4 py-2 rounded-md hover:bg-[#163246]">Pesan & Komentar</a>
        <a href="#" class="block px-4 py-2 rounded-md hover:bg-[#163246]">Pengaturan</a>
      </nav>

      <div class="p-4 border-t border-white/20 text-xs text-center text-gray-300">
        © {{ date('Y') }} LiteraMarket
      </div>
    </aside>

    {{-- === Main Content === --}}
    <div class="flex-1 flex flex-col">
      {{-- Topbar --}}
      <header class="bg-white border-b flex items-center justify-between px-6 py-3">
        {{-- Search --}}
        <div class="relative w-1/3">
          <input type="text" placeholder="Search..."
            class="w-full border border-gray-300 rounded-md pl-10 pr-3 py-2 focus:outline-none focus:ring focus:ring-[#d2c1b6]/50">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
          </svg>
        </div>

        {{-- Notification + Profile --}}
        <div class="flex items-center gap-5">
          {{-- Notification Icon --}}
          <button class="relative text-gray-600 hover:text-[#1B3C53]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-9.33-5.014A6.001 6.001 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
          </button>

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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
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
</body>
</html>
