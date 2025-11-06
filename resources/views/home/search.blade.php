<div class="max-w-2xl mx-auto mb-5 px-4">
  <form method="GET" action="{{ route('home') }}">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search books by title..."
      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-[#d2c1b6]/50 focus:border-[#d2c1b6]">
  </form>
</div>