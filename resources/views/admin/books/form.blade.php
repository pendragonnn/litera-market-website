@extends('layouts.admin')
{{-- {{ dd($book) }} --}}

@section('content')
  <h1 class="text-2xl font-bold text-[#1B3C53] mb-6">
    {{ $book->exists ? 'Edit Book' : 'Add New Book' }}
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

  <form action="{{ $action }}" method="POST" enctype="multipart/form-data"
    class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
    @csrf
    @if ($book->exists)
      @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- Title --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title', $book->title) }}" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
      </div>

      {{-- Author --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
        <input type="text" name="author" value="{{ old('author', $book->author) }}" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
      </div>

      {{-- Price --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Price (Rp)</label>
        <input type="number" name="price" value="{{ old('price', $book->price) }}" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
      </div>

      {{-- Stock --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
      </div>

      {{-- Category --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <select name="category_id"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]">
          <option value="">-- Select Category --</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}"
              {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Description --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Book Description</label>
        <textarea name="description" rows="5"
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"
          placeholder="Write a short description about this book...">{{ old('description', $book->description) }}</textarea>
      </div>

      {{-- Image --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Book Cover Image</label>
        <input type="file" name="image"
          class="block w-full text-sm border border-gray-300 rounded-md px-3 py-2">
        @if ($book->image)
          <img src="{{ asset('storage/' . $book->image) }}" alt="Book Image"
            class="mt-3 w-32 h-40 object-cover rounded-md border">
        @endif
      </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <a href="{{ route('admin.books.index') }}"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</a>
      <button type="submit"
        class="px-4 py-2 bg-[#1B3C53] text-white rounded-md hover:bg-[#163246]">
        {{ $book->exists ? 'Save Changes' : 'Add Book' }}
      </button>
    </div>
  </form>
@endsection
