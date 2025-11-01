@extends('layouts.admin')

@section('content')
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#1B3C53]">Categories Management</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-[#1B3C53] text-white px-4 py-2 rounded-md hover:bg-[#163246]">
      + Add Category
    </a>
  </div>

  @if (session('success'))
    <div class="mb-4 p-3 text-green-700 bg-green-100 rounded-lg">
      {{ session('success') }}
    </div>
  @endif

  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
    <table id="categoriesTable" class="display hover row-border w-full text-sm">
      <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
          <th class="px-4 py-3">Category Name</th>
          <th class="px-4 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($categories as $index => $category)
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-3">{{ $category->name }}</td>
            <td class="px-4 py-3 text-center flex justify-start gap-2">
              <a href="{{ route('admin.categories.edit', $category) }}"
                class="px-3 py-1 bg-[#1B3C53] text-white rounded-md text-xs hover:bg-[#102a3e]">Edit</a>
              <button type="button"
                class="px-3 py-1 bg-red-600 text-white rounded-md text-xs hover:bg-red-700"
                onclick="openDeleteModal('{{ route('admin.categories.destroy', $category) }}', '{{ $category->name }}')">
                Delete
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center py-4 text-gray-500">No categories available.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

{{-- === Delete Confirmation Modal === --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-sm relative overflow-hidden border border-gray-300">
    {{-- Close Button --}}
    <button type="button" onclick="closeDeleteModal()"
      class="absolute top-2 right-3 text-gray-400 hover:text-gray-600 text-xl">✕</button>

    {{-- Modal Header --}}
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-[#1B3C53]">Confirm Deletion</h2>
    </div>

    {{-- Modal Body --}}
    <div class="px-6 py-5 text-gray-700 text-sm">
      <p>Are you sure you want to delete <span id="categoryName" class="font-semibold text-[#1B3C53]"></span>?</p>
      <p class="text-gray-500 text-xs mt-2">This action cannot be undone.</p>
    </div>

    {{-- Modal Footer --}}
    <div class="flex justify-end items-start gap-3 px-6 py-4 border-t border-gray-200">
      <button type="button" onclick="closeDeleteModal()"
        class="px-4 py-2 text-sm rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">
        Cancel
      </button>

      <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-700">
          Delete
        </button>
      </form>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    function openDeleteModal(actionUrl, categoryName) {
      const modal = document.getElementById('deleteModal');
      const form = document.getElementById('deleteForm');
      const nameSpan = document.getElementById('categoryName');

      form.action = actionUrl;
      nameSpan.textContent = `"${categoryName}"`;

      modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
    }

    // Optional: Close modal on background click
    document.getElementById('deleteModal').addEventListener('click', function (e) {
      if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('DOMContentLoaded', function () {
      if ($('#categoriesTable').length) {
        $('#categoriesTable').DataTable({
          responsive: true,
          language: {
            search: "",
            searchPlaceholder: "Search category...",
            lengthMenu: "Show _MENU_ per page",
            zeroRecords: "No matching categories found",
            info: "Showing _START_ to _END_ of _TOTAL_ categories",
            infoEmpty: "No categories to display",
            paginate: {
              first: "First",
              last: "Last",
              next: ">",
              previous: "<"
            }
          },
          pageLength: 10,
          lengthMenu: [5, 10, 25, 50],
          order: [[1, 'asc']]
        });
      }
    });
  </script>
@endpush
