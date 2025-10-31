<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->q) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $books = $query->paginate(8); 

        return view('home', compact('books'));
    }
}
