@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-6">
    {{ $user->exists ? 'Edit User' : 'Add New User' }}
  </h1>

  @if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $action }}" method="POST" class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
    @csrf
    @if ($user->exists)
      @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- Name --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
      </div>

      {{-- Email --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
      </div>

      {{-- Role --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select name="role" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]" required>
          <option value="">-- Select Role --</option>
          <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
          <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
        </select>
      </div>

      {{-- Password --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"
          {{ $user->exists ? '' : 'required' }}>
      </div>

      {{-- Confirm Password --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"
          {{ $user->exists ? '' : 'required' }}>
      </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</a>
      <button type="submit" class="px-4 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
        {{ $user->exists ? 'Save Changes' : 'Add User' }}
      </button>
    </div>
  </form>
@endsection
