{{-- === Modal Section === --}}
@section('modals')

  {{-- === Toast Notification (Reusable for Login/Register) === --}}
  @if (session('success') || session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition
      x-init="setTimeout(() => show = false, 3000)"
      class="fixed top-5 left-1/2 transform -translate-x-1/2 z-[60] w-[90%] max-w-md
             rounded-xl shadow-xl border border-[#d2c1b6]/70
             {{ session('success') ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}
             text-sm font-medium px-5 py-4 flex justify-between items-center">
      
      <span>
        {{ session('success') ?? session('error') }}
      </span>

      <button class="ml-3 font-bold text-[#1B3C53]/60 hover:text-[#1B3C53]" @click="show = false">
        ✕
      </button>
    </div>
  @endif


  {{-- === Login Modal === --}}
  <div id="loginModal" class="fixed inset-0 z-50 hidden bg-black/40 flex justify-center items-start pt-16">
    <div class="bg-white border border-gray-800 rounded-lg w-full max-w-sm relative overflow-hidden">
      <button onclick="toggleModal('loginModal')"
        class="absolute top-2 right-3 text-gray-400 hover:text-gray-600">✕</button>

      {{-- Header --}}
      <div class="border-b border-gray-800 px-6 py-3">
        <h2 class="text-lg font-semibold text-gray-800">Login</h2>
      </div>

      {{-- Form --}}
      <form id="loginForm" method="POST" action="{{ route('login') }}" class="px-6 py-4 border-b border-gray-400">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" name="email" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" required
            class="w-full border border-gray-800 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
      </form>

      {{-- Footer --}}
      <div class="flex justify-end px-6 py-3">
        <button type="submit" form="loginForm"
          class="bg-[#002D72] text-white text-sm font-medium rounded-md px-4 py-2 hover:bg-[#001E4D]">
          Login
        </button>
      </div>
    </div>
  </div>


  {{-- === Register Modal === --}}
  <div id="registerModal" class="fixed inset-0 z-50 hidden bg-black/40 flex justify-center items-start pt-16">
    <div class="bg-white border border-gray-800 rounded-lg w-full max-w-sm relative overflow-hidden">
      <button onclick="toggleModal('registerModal')"
        class="absolute top-2 right-3 text-gray-400 hover:text-gray-600">✕</button>

      {{-- Header --}}
      <div class="border-b border-gray-800 px-6 py-3">
        <h2 class="text-lg font-semibold text-gray-800">Register</h2>
      </div>

      {{-- Form --}}
      <form id="registerForm" method="POST" action="{{ route('register') }}" class="px-6 py-4 border-b border-gray-400">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
          <input type="text" name="name" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" name="email" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <input type="password" name="password_confirmation" required
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-100 focus:border-blue-400">
        </div>
      </form>

      {{-- Footer --}}
      <div class="flex justify-end px-6 py-3">
        <button type="submit" form="registerForm"
          class="bg-[#007E33] text-white text-sm font-medium rounded-md px-4 py-2 hover:bg-[#006029]">
          Register
        </button>
      </div>
    </div>
  </div>
@endsection


@push('scripts')
  <script>
    function toggleModal(id) {
      const modal = document.getElementById(id);
      modal.classList.toggle('hidden');
    }

    function switchModal(from, to) {
      document.getElementById(from).classList.add('hidden');
      document.getElementById(to).classList.remove('hidden');
    }
  </script>
@endpush
