<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
  public function index()
  {
    // Hitung jumlah data
    $totalBooks = Book::count();
    $totalOrders = Order::count();
    $totalUsers = User::where('role', 'customer')->count();

    // Ambil 5 pesanan terbaru (termasuk guest)
    $recentOrders = Order::with('user')
      ->orderBy('created_at', 'desc')
      ->take(5)
      ->get(['id', 'user_id', 'name', 'status', 'created_at']);

    // Tambahkan fallback nama jika user_id null
    $recentOrders->transform(function ($order) {
      $order->display_name = $order->user->name ?? $order->name ?? 'Guest';
      return $order;
    });

    return view('admin.dashboard', compact('totalBooks', 'totalOrders', 'totalUsers', 'recentOrders'));
  }
}
