@extends('layouts.admin')

@section('title', 'Admin Panel | Categories Form')

@section('breadcrumb', 'Categories Data Management > Categories Form')

@section('content')
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-6">
    {{ $category->exists ? 'Edit Category' : 'Add New Category' }}
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
    @if ($category->exists)
      @method('PUT')
    @endif

    {{-- Category Name --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
      <input type="text" name="name" value="{{ old('name', $category->name) }}" required
        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <a href="{{ route('admin.categories.index') }}"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</a>
      <button type="submit"
        class="px-4 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
        {{ $category->exists ? 'Save Changes' : 'Add Category' }}
      </button>
    </div>
  </form>
@endsection
