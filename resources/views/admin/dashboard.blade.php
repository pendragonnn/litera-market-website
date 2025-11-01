@extends('layouts.admin')

@section('content')
  {{-- Breadcrumb --}}
  <p class="text-sm text-gray-500 mb-4">Admin Page > <span class="text-[#1B3C53] font-semibold">Dashboard Admin</span></p>

  {{-- Stats Card Section --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 text-center">
      <h4 class="text-sm text-gray-500 mb-1">Jumlah Produk</h4>
      <p class="text-2xl font-bold text-[#1B3C53]">120</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 text-center">
      <h4 class="text-sm text-gray-500 mb-1">Jumlah Pesanan</h4>
      <p class="text-2xl font-bold text-[#1B3C53]">45</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 text-center">
      <h4 class="text-sm text-gray-500 mb-1">Jumlah Pengguna</h4>
      <p class="text-2xl font-bold text-[#1B3C53]">100</p>
    </div>
  </div>

  {{-- Recent Orders Table --}}
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-[#1B3C53] mb-4">Pesanan Terbaru</h3>

    <table class="w-full border-collapse text-sm">
      <thead class="bg-gray-100 text-gray-700 border-b border-gray-200">
        <tr>
          <th class="text-left py-2 px-3">ID</th>
          <th class="text-left py-2 px-3">Pengguna</th>
          <th class="text-left py-2 px-3">Tanggal</th>
          <th class="text-left py-2 px-3">Status</th>
        </tr>
      </thead>
      <tbody>
        @php
          $orders = [
            ['id' => 1, 'user' => 'User 1', 'date' => '2025-04-24', 'status' => 'Diproses'],
            ['id' => 2, 'user' => 'User 2', 'date' => '2025-04-24', 'status' => 'Diproses'],
            ['id' => 3, 'user' => 'User 3', 'date' => '2025-04-24', 'status' => 'Diproses'],
          ];
        @endphp

        @foreach ($orders as $order)
          <tr class="border-b hover:bg-gray-50">
            <td class="py-2 px-3">{{ $order['id'] }}</td>
            <td class="py-2 px-3">{{ $order['user'] }}</td>
            <td class="py-2 px-3">{{ $order['date'] }}</td>
            <td class="py-2 px-3">
              <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">
                {{ $order['status'] }}
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
