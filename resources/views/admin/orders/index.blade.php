@extends('layouts.admin')
{{-- {{ dd($orders) }} --}}

@section('content')
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#1B3C53]">Orders Data Management</h1>
  </div>

  {{-- Success / Error Messages --}}
  @if (session('success'))
    <div class="mb-4 p-3 text-green-700 bg-green-100 rounded-lg">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 p-3 text-red-700 bg-red-100 rounded-lg">
      {{ session('error') }}
    </div>
  @endif

  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
    <table id="ordersTable" class="display hover row-border w-full text-sm">
      <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
          <th class="px-4 py-3">Order ID</th>
          <th class="px-4 py-3">Customer</th>
          <th class="px-4 py-3">Total Price</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Payment</th>
          <th class="px-4 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($orders as $order)
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-3 font-medium text-gray-800">#{{ $order->id }}</td>
            <td class="px-4 py-3">{{ $order->user->name ?? 'Unknown User' }}</td>
            <td class="px-4 py-3">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            <td class="px-4 py-3">
              @php
                $statusColors = [
                    'Pending' => 'bg-yellow-100 text-yellow-700',
                    'Shipped' => 'bg-blue-100 text-blue-700',
                    'Delivered' => 'bg-green-100 text-green-700',
                    'Cancelled' => 'bg-red-100 text-red-700',
                ];
              @endphp
              <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ ucfirst($order->status) }}
              </span>
            </td>
            <td class="px-4 py-3">
              <span class="text-sm text-gray-700">
                {{ ucfirst($order->payment->payment_status ?? 'N/A') }}
              </span>
            </td>
            <td class="px-4 py-3 text-center items-start flex justify-start gap-2">
              <a href="{{ route('admin.orders.show', $order) }}"
                 class="px-3 py-1 bg-[#1B3C53] text-white rounded-md text-xs hover:bg-[#102a3e]">View</a>

              @if ($order->status === 'Processed' && $order->payment->payment_status === "Awaiting Approval")
                <form action="{{ route('admin.orders.confirm', $order) }}" method="POST">
                  @csrf
                  <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700">
                    Confirm
                  </button>
                </form>
                <button type="button"
                        class="px-3 py-1 bg-red-600 text-white rounded-md text-xs hover:bg-red-700"
                        onclick="openRejectModal('{{ route('admin.orders.reject', $order) }}', '{{ $order->id }}')">
                  Reject
                </button>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-500">No orders available.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection


{{-- === Reject Confirmation Modal === --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-sm relative overflow-hidden border border-gray-300">
    <button type="button" onclick="closeRejectModal()"
      class="absolute top-2 right-3 text-gray-400 hover:text-gray-600 text-xl">✕</button>

    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-[#1B3C53]">Reject Order</h2>
    </div>

    <form id="rejectForm" method="POST" class="px-6 py-4 space-y-3">
      @csrf
      <label class="block text-sm text-gray-700 font-medium">Reason (optional)</label>
      <textarea name="admin_note" rows="3"
        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-[#d2c1b6] focus:border-[#d2c1b6]"
        placeholder="Enter rejection reason..."></textarea>

      <div class="flex justify-end gap-3 mt-4">
        <button type="button" onclick="closeRejectModal()"
          class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
  <script>
    function openRejectModal(actionUrl, orderId) {
      const modal = document.getElementById('rejectModal');
      const form = document.getElementById('rejectForm');
      form.action = actionUrl;
      modal.classList.remove('hidden');
    }

    function closeRejectModal() {
      document.getElementById('rejectModal').classList.add('hidden');
    }

    // Optional: close modal on background click
    document.getElementById('rejectModal').addEventListener('click', function (e) {
      if (e.target === this) closeRejectModal();
    });

    document.addEventListener('DOMContentLoaded', function () {
      if ($('#ordersTable').length) {
        $('#ordersTable').DataTable({
          responsive: true,
          language: {
            search: "",
            searchPlaceholder: "Search order...",
            lengthMenu: "Show _MENU_ per page",
            zeroRecords: "No matching orders found",
            info: "Showing _START_ to _END_ of _TOTAL_ orders",
            infoEmpty: "No orders to display",
            paginate: {
              first: "First",
              last: "Last",
              next: ">",
              previous: "<"
            }
          },
          pageLength: 10,
          lengthMenu: [5, 10, 25, 50],
          order: [[0, 'asc']]
        });
      }
    });
  </script>
@endpush
