@extends('layouts.admin')

@section('title', 'Admin Panel | Dashboard')

@section('breadcrumb', 'Dashboard')

@section('content')

  {{-- Stats Card Section --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 text-center">
      <h4 class="text-sm text-gray-500 mb-1">Total Products</h4>
      <p class="text-2xl font-bold text-[#1B3C53]">{{ $totalBooks }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 text-center">
      <h4 class="text-sm text-gray-500 mb-1">Total Orders</h4>
      <p class="text-2xl font-bold text-[#1B3C53]">{{ $totalOrders }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 text-center">
      <h4 class="text-sm text-gray-500 mb-1">Total Users</h4>
      <p class="text-2xl font-bold text-[#1B3C53]">{{ $totalUsers }}</p>
    </div>
  </div>

  {{-- Recent Orders Table --}}
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-[#1B3C53] mb-4">Recent Orders</h3>

    @if ($recentOrders->isEmpty())
      <p class="text-gray-500 text-sm text-center py-6">No recent orders found.</p>
    @else
      <table class="w-full border-collapse text-sm">
        <thead class="bg-gray-100 text-gray-700 border-b border-gray-200">
          <tr>
            <th class="text-left py-2 px-3">Order ID</th>
            <th class="text-left py-2 px-3">Customer</th>
            <th class="text-left py-2 px-3">Date</th>
            <th class="text-left py-2 px-3">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($recentOrders as $order)
            <tr class="border-b hover:bg-gray-50 transition">
              <td class="py-2 px-3">#{{ $order->id }}</td>
              <td class="py-2 px-3">{{ $order->display_name }}</td>
              <td class="py-2 px-3">{{ $order->created_at->format('d M Y') }}</td>
              <td class="py-2 px-3">
                <span class="
                      @if ($order->status === 'Pending') bg-yellow-100 text-yellow-700 
                      @elseif ($order->status === 'Processed') bg-blue-100 text-blue-700 
                      @elseif ($order->status === 'Delivered') bg-green-100 text-green-700 
                      @elseif ($order->status === 'Cancelled') bg-red-100 text-red-700 
                      @else bg-gray-100 text-gray-600 
                      @endif 
                      px-3 py-1 rounded-full text-xs font-medium">
                  {{ $order->status }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
@endsection
