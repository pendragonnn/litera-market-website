<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()->with('category');

        // Ambil daftar kategori dari tabel categories
        $categories = Category::select('id', 'name')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        // Filter pencarian (search)
        if ($request->q) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        // Filter kategori (pakai nama kategori di URL)
        if ($request->category && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $books = $query->paginate(8)->appends($request->all());

        return view('home', compact('books', 'categories'));
    }
}
